import 'dart:ui';

import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/features/visitor/visitor_explore/services/map_pathfinder.dart';

void main() {
  test('wheelchairOnly excludes inaccessible edges', () {
    const graph = MapNavigationGraph(
      nodes: [
        MapPathNode(id: 1, x: 0.1, y: 0.1),
        MapPathNode(id: 2, x: 0.5, y: 0.1),
        MapPathNode(id: 3, x: 0.9, y: 0.1),
      ],
      edges: [
        MapPathEdge(from: 1, to: 2, distance: 100, isAccessible: true),
        MapPathEdge(from: 2, to: 1, distance: 100, isAccessible: true),
        MapPathEdge(from: 2, to: 3, distance: 100, isAccessible: false),
        MapPathEdge(from: 3, to: 2, distance: 100, isAccessible: false),
      ],
    );

    expect(
      graph.shortestPathResult(1, 3)?.totalDistanceMeters,
      200,
    );
    expect(
      graph.shortestPathResult(
        1,
        3,
        options: const MapRouteOptions(wheelchairOnly: true),
      ),
      isNull,
    );
  });
}
