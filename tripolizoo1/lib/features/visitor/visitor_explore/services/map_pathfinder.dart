import 'dart:ui';

class MapPathNode {
  const MapPathNode({
    required this.id,
    required this.x,
    required this.y,
    this.name,
    this.mapLocationId,
  });

  final int id;
  final double x;
  final double y;
  final String? name;
  final int? mapLocationId;

  Offset get position => Offset(x, y);

  factory MapPathNode.fromJson(Map<String, dynamic> json) {
    return MapPathNode(
      id: (json['id'] as num).toInt(),
      x: (json['x'] as num).toDouble(),
      y: (json['y'] as num).toDouble(),
      name: json['name']?.toString(),
      mapLocationId: (json['map_location_id'] as num?)?.toInt(),
    );
  }
}

class MapPathEdge {
  const MapPathEdge({
    required this.from,
    required this.to,
    required this.distance,
  });

  final int from;
  final int to;
  final int distance;

  factory MapPathEdge.fromJson(Map<String, dynamic> json) {
    return MapPathEdge(
      from: (json['from'] as num).toInt(),
      to: (json['to'] as num).toInt(),
      distance: (json['distance'] as num).toInt(),
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

  List<Offset> shortestPathOffsets(int startNodeId, int endNodeId) {
    final nodeIds = shortestPathNodeIds(startNodeId, endNodeId);
    return nodeIds
        .map((id) => nodes.firstWhere((node) => node.id == id).position)
        .toList();
  }

  List<int> shortestPathNodeIds(int startNodeId, int endNodeId) {
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
        (a, b) => (distances[a] ?? unreachable).compareTo(distances[b] ?? unreachable),
      );
      final current = queue.removeAt(0);
      if (!visited.add(current)) continue;
      if (current == endNodeId) break;

      final currentDistance = distances[current];
      if (currentDistance == null) continue;

      for (final edge in edges.where((edge) => edge.from == current)) {
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
      return [startNodeId, endNodeId];
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
}
