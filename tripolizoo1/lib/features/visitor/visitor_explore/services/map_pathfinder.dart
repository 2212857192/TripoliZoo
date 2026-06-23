import 'dart:ui';

class MapRouteOptions {
  const MapRouteOptions({this.wheelchairOnly = false});

  final bool wheelchairOnly;

  bool isEdgeUsable(MapPathEdge edge) {
    if (!edge.isActive) return false;
    if (wheelchairOnly && !edge.isAccessible) return false;
    return true;
  }
}

class MapPathNode {
  const MapPathNode({
    required this.id,
    required this.x,
    required this.y,
    this.nodeKey,
    this.name,
    this.mapLocationId,
    this.isActive = true,
  });

  final int id;
  final double x;
  final double y;
  final String? nodeKey;
  final String? name;
  final int? mapLocationId;
  final bool isActive;

  Offset get position => Offset(x, y);

  factory MapPathNode.fromJson(Map<String, dynamic> json) {
    return MapPathNode(
      id: (json['id'] as num).toInt(),
      x: (json['x'] as num).toDouble(),
      y: (json['y'] as num).toDouble(),
      nodeKey: json['node_key']?.toString(),
      name: json['name']?.toString(),
      mapLocationId: (json['map_location_id'] as num?)?.toInt(),
      isActive: json['is_active'] as bool? ?? true,
    );
  }
}

class MapPathEdge {
  const MapPathEdge({
    required this.from,
    required this.to,
    required this.distance,
    this.geometry = const [],
    this.isActive = true,
    this.isAccessible = true,
  });

  final int from;
  final int to;
  final int distance;
  final List<Offset> geometry;
  final bool isActive;
  final bool isAccessible;

  factory MapPathEdge.fromJson(Map<String, dynamic> json) {
    final rawGeometry = json['geometry'] as List?;
    final geometry = rawGeometry
            ?.whereType<List<dynamic>>()
            .map(
              (point) => Offset(
                (point[0] as num).toDouble(),
                (point[1] as num).toDouble(),
              ),
            )
            .toList() ??
        const [];

    return MapPathEdge(
      from: (json['from'] as num).toInt(),
      to: (json['to'] as num).toInt(),
      distance: (json['distance'] as num).toInt(),
      geometry: geometry,
      isActive: json['is_active'] as bool? ?? true,
      isAccessible: json['is_accessible'] as bool? ?? true,
    );
  }
}

class MapNavigationGraph {
  const MapNavigationGraph({
    required this.nodes,
    required this.edges,
  });

  final List<MapPathNode> nodes;
  final List<MapPathEdge> edges;

  factory MapNavigationGraph.fromJson(Map<String, dynamic> json) {
    final nodes = (json['nodes'] as List? ?? const [])
        .whereType<Map<String, dynamic>>()
        .map(MapPathNode.fromJson)
        .where((node) => node.isActive)
        .toList();
    final edges = (json['edges'] as List? ?? const [])
        .whereType<Map<String, dynamic>>()
        .map(MapPathEdge.fromJson)
        .toList();

    return MapNavigationGraph(nodes: nodes, edges: edges);
  }

  MapPathNode? nodeForLocation(int mapLocationId) {
    for (final node in nodes) {
      if (node.mapLocationId == mapLocationId) {
        return node;
      }
    }
    return null;
  }

  MapPathNode? nearestNode(Offset normalizedPosition) {
    if (nodes.isEmpty) {
      return null;
    }

    var best = nodes.first;
    var bestDistance = double.infinity;

    for (final node in nodes) {
      final dx = node.x - normalizedPosition.dx;
      final dy = node.y - normalizedPosition.dy;
      final distance = (dx * dx) + (dy * dy);
      if (distance < bestDistance) {
        bestDistance = distance;
        best = node;
      }
    }

    return best;
  }

  List<Offset> shortestPathOffsets(
    int startNodeId,
    int endNodeId, {
    MapRouteOptions options = const MapRouteOptions(),
  }) {
    final nodeIds = shortestPathNodeIds(
      startNodeId,
      endNodeId,
      options: options,
    );
    if (nodeIds.isEmpty) return [];
    if (nodeIds.length == 1) {
      final node = _nodeById(nodeIds.first);
      return node != null ? [node.position] : [];
    }

    final adjacency = _adjacencyFor(options);

    final offsets = <Offset>[];

    for (var i = 0; i < nodeIds.length - 1; i++) {
      final fromId = nodeIds[i];
      final toId = nodeIds[i + 1];
      final edge = adjacency[fromId]?[toId];

      if (edge != null && edge.geometry.length > 1) {
        final startIdx = offsets.isEmpty ? 0 : 1;
        offsets.addAll(edge.geometry.skip(startIdx));
      } else {
        if (offsets.isEmpty) {
          final fromNode = _nodeById(fromId);
          if (fromNode != null) offsets.add(fromNode.position);
        }
        final toNode = _nodeById(toId);
        if (toNode != null) offsets.add(toNode.position);
      }
    }

    return offsets;
  }

  List<int> shortestPathNodeIds(
    int startNodeId,
    int endNodeId, {
    MapRouteOptions options = const MapRouteOptions(),
  }) {
    if (startNodeId == endNodeId) {
      return [startNodeId];
    }

    const unreachable = 1 << 30;

    final distances = <int, int>{startNodeId: 0};
    final previous = <int, int?>{startNodeId: null};
    final visited = <int>{};
    final queue = <int>[startNodeId];

    while (queue.isNotEmpty) {
      queue.sort(
        (a, b) =>
            (distances[a] ?? unreachable).compareTo(distances[b] ?? unreachable),
      );
      final current = queue.removeAt(0);
      if (!visited.add(current)) continue;
      if (current == endNodeId) break;

      final currentDistance = distances[current];
      if (currentDistance == null) continue;

      for (final edge in edges.where((edge) => edge.from == current)) {
        if (!options.isEdgeUsable(edge)) continue;

        final nextDistance = currentDistance + edge.distance;
        final known = distances[edge.to];
        if (known == null || nextDistance < known) {
          distances[edge.to] = nextDistance;
          previous[edge.to] = current;
          if (!visited.contains(edge.to)) {
            queue.add(edge.to);
          }
        }
      }
    }

    if (!previous.containsKey(endNodeId)) {
      return const [];
    }

    final path = <int>[];
    var step = endNodeId;
    while (true) {
      path.insert(0, step);
      final parent = previous[step];
      if (parent == null) break;
      step = parent;
    }

    return path;
  }

  MapPathNode? nodeById(int id) => _nodeById(id);

  MapPathNode? nodeByKey(String? key) {
    if (key == null || key.isEmpty) return null;
    for (final node in nodes) {
      if (node.nodeKey == key) return node;
    }
    return null;
  }

  MapPathResult? shortestPathResult(
    int startNodeId,
    int endNodeId, {
    MapRouteOptions options = const MapRouteOptions(),
  }) {
    final nodeIds = shortestPathNodeIds(
      startNodeId,
      endNodeId,
      options: options,
    );
    if (nodeIds.isEmpty) return null;

    final offsets = shortestPathOffsets(
      startNodeId,
      endNodeId,
      options: options,
    );
    if (offsets.isEmpty) return null;

    final adjacency = _adjacencyFor(options);

    var totalDistance = 0;
    for (var i = 0; i < nodeIds.length - 1; i++) {
      totalDistance += adjacency[nodeIds[i]]?[nodeIds[i + 1]]?.distance ?? 0;
    }

    return MapPathResult(
      offsets: offsets,
      totalDistanceMeters: totalDistance,
    );
  }

  Map<int, Map<int, MapPathEdge>> _adjacencyFor(MapRouteOptions options) {
    final adjacency = <int, Map<int, MapPathEdge>>{};
    for (final edge in edges) {
      if (!options.isEdgeUsable(edge)) continue;
      adjacency.putIfAbsent(edge.from, () => {})[edge.to] = edge;
    }
    return adjacency;
  }

  MapPathNode? _nodeById(int id) {
    for (final node in nodes) {
      if (node.id == id) return node;
    }
    return null;
  }
}

class MapPathResult {
  const MapPathResult({
    required this.offsets,
    required this.totalDistanceMeters,
  });

  final List<Offset> offsets;
  final int totalDistanceMeters;

  int get estimatedWalkMinutes =>
      (totalDistanceMeters / 72).ceil().clamp(1, 999);
}
