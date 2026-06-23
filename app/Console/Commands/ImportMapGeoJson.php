<?php

namespace App\Console\Commands;

use App\Models\MapLocation;
use App\Models\MapPathEdge;
use App\Models\MapPathNode;
use Illuminate\Console\Command;

class ImportMapGeoJson extends Command
{
    protected $signature = 'map:import-geojson
                            {--file= : Path to GeoJSON file (defaults to Zoo_Routing_Ready (4).geojson in project root)}
                            {--force : Clear existing routing data before import}';

    protected $description = 'Import routing nodes and edges from the zoo GeoJSON file into the database';

    public function handle(): int
    {
        $filePath = $this->option('file') ?? base_path('Zoo_Routing_Ready (4).geojson');

        if (! file_exists($filePath)) {
            $this->error("GeoJSON file not found: {$filePath}");

            return self::FAILURE;
        }

        $this->info("Reading GeoJSON from: {$filePath}");

        $raw = file_get_contents($filePath);
        $geojson = json_decode($raw, true);

        if (! $geojson || $geojson['type'] !== 'FeatureCollection') {
            $this->error('Invalid GeoJSON: expected a FeatureCollection.');

            return self::FAILURE;
        }

        $meta = $geojson['metadata'] ?? [];
        $imageWidth = (float) ($meta['image_width'] ?? 4516);
        $imageHeight = (float) ($meta['image_height'] ?? 3374);
        $features = $geojson['features'] ?? [];

        $nodes = array_values(array_filter($features, fn ($f) => ($f['properties']['entity'] ?? '') === 'map_path_node'));
        $edges = array_values(array_filter($features, fn ($f) => ($f['properties']['entity'] ?? '') === 'map_path_edge'));
        $locations = array_values(array_filter($features, fn ($f) => ($f['properties']['entity'] ?? '') === 'map_location'));

        $this->info(sprintf(
            'Found %d nodes, %d edges, %d locations in GeoJSON.',
            count($nodes),
            count($edges),
            count($locations),
        ));

        if (MapPathNode::query()->where('is_active', true)->exists() && ! $this->option('force')) {
            if (! $this->confirm('Routing data already exists. Clear and re-import? (use --force to skip this prompt)')) {
                $this->info('Import cancelled.');

                return self::SUCCESS;
            }
        }

        $this->info('Clearing existing routing data...');
        MapPathEdge::query()->delete();
        MapPathNode::query()->delete();

        // ── 1. Import nodes ───────────────────────────────────────────────────
        $this->info('Importing nodes...');
        $nodeKeyToId = [];

        foreach ($nodes as $feature) {
            $props = $feature['properties'];
            [$pixelX, $pixelY] = $feature['geometry']['coordinates'];

            $node = MapPathNode::create([
                'node_key' => $props['node_key'],
                'name' => $props['name'] ?? null,
                'x' => round($pixelX / $imageWidth, 7),
                'y' => round($pixelY / $imageHeight, 7),
                'is_active' => (bool) ($props['is_active'] ?? true),
            ]);

            $nodeKeyToId[$props['node_key']] = $node->id;
        }

        $this->info(count($nodeKeyToId).' nodes imported.');

        // ── 2. Import edges ───────────────────────────────────────────────────
        $this->info('Importing edges...');
        $edgeCount = 0;
        $skipped = 0;

        foreach ($edges as $feature) {
            $props = $feature['properties'];
            $fromId = $nodeKeyToId[$props['from_node_key']] ?? null;
            $toId = $nodeKeyToId[$props['to_node_key']] ?? null;

            if (! $fromId || ! $toId) {
                $skipped++;

                continue;
            }

            // Normalize all geometry waypoints (pixel → 0–1)
            $rawCoords = $feature['geometry']['coordinates'] ?? [];
            $geometry = array_map(fn ($pt) => [
                round($pt[0] / $imageWidth, 7),
                round($pt[1] / $imageHeight, 7),
            ], $rawCoords);

            // Convert pixel distance to approximate metres
            $distanceMeters = max(15, (int) round(($props['distance_units'] ?? 0) * 0.35));

            MapPathEdge::create([
                'edge_key' => $props['edge_key'] ?? null,
                'from_node_id' => $fromId,
                'to_node_id' => $toId,
                'distance_meters' => $distanceMeters,
                'geometry' => $geometry ?: null,
                'is_active' => (bool) ($props['is_active'] ?? true),
                'is_accessible' => (bool) ($props['is_accessible'] ?? true),
            ]);

            $edgeCount++;
        }

        $this->info("{$edgeCount} edges imported" . ($skipped ? " ({$skipped} skipped due to missing node keys)." : '.'));

        // ── 3. Link nodes to MapLocation records ──────────────────────────────
        $this->info('Linking nodes to map locations...');
        $linked = 0;
        $created = 0;

        foreach ($locations as $feature) {
            $props = $feature['properties'];
            $nearestNodeKey = $props['nearest_node_key'] ?? null;
            [$pixelX, $pixelY] = $feature['geometry']['coordinates'];
            $name = trim($props['name'] ?? '');

            $nodeId = $nearestNodeKey ? ($nodeKeyToId[$nearestNodeKey] ?? null) : null;

            if (! $name) {
                continue;
            }

            // Try to find existing MapLocation by name
            $mapLocation = MapLocation::where('name', $name)->first();

            if ($mapLocation) {
                // Update its normalized coordinates to match the GeoJSON
                $mapLocation->update([
                    'latitude' => round($pixelY / $imageHeight, 7),
                    'longitude' => round($pixelX / $imageWidth, 7),
                ]);
            } else {
                // Create a new MapLocation from the GeoJSON data
                $mapLocation = MapLocation::create([
                    'name' => $name,
                    'category' => $this->mapLocationType($props['location_type'] ?? 'service'),
                    'latitude' => round($pixelY / $imageHeight, 7),
                    'longitude' => round($pixelX / $imageWidth, 7),
                    'description' => $props['description'] ?? null,
                    'is_active' => (bool) ($props['is_active'] ?? true),
                ]);
                $created++;
            }

            // Tag the nearest node with this location
            if ($nodeId) {
                MapPathNode::where('id', $nodeId)->update(['map_location_id' => $mapLocation->id]);
                $linked++;
            }
        }

        $this->info("{$linked} nodes linked to locations, {$created} new locations created.");
        $this->newLine();
        $this->info('✓ GeoJSON import complete.');
        $this->info('  Run `php artisan migrate` first if you have not already.');
        $this->newLine();

        return self::SUCCESS;
    }

    private function mapLocationType(string $type): string
    {
        return match ($type) {
            'animal' => 'enclosure',
            'restaurant' => 'dining',
            default => 'service',
        };
    }
}
