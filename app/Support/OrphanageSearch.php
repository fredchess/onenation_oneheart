<?php

namespace App\Support;

use App\Models\Orphanage;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Text search over orphanages.
 *
 * With Typesense (SCOUT_DRIVER=typesense) the search tolerates typos and ignores
 * case and accents ("yaounde", "biyem asi"…). If Typesense is not configured or
 * unreachable, it falls back to a case- and accent-insensitive LIKE in MySQL.
 */
class OrphanageSearch
{
    /**
     * Searchable fields: Typesense field => SQL expression used by the fallback.
     */
    private const FIELDS = [
        'name' => 'orphanages.name',
        'localisation' => "JSON_UNQUOTE(JSON_EXTRACT(orphanages.data_address, '$.localisation'))",
        'city_name' => '(SELECT cities.name FROM cities WHERE cities.id = orphanages.city_id)',
    ];

    /**
     * Ids of the published orphanages matching $term on $fields, best match first.
     *
     * @param  array<int, string>  $fields  keys of self::FIELDS
     * @return array<int, int>
     */
    public static function ids(string $term, array $fields): array
    {
        $fields = array_values(array_intersect($fields, array_keys(self::FIELDS)));

        if (config('scout.driver') === 'typesense') {
            try {
                return Orphanage::search($term)
                    ->options([
                        'query_by' => implode(',', $fields),
                        'filter_by' => 'status:=1',
                        'per_page' => 250,
                    ])
                    ->keys()
                    ->map(fn ($id) => (int) $id)
                    ->all();
            } catch (Throwable $e) {
                Log::warning('Typesense search failed, falling back to SQL.', ['error' => $e->getMessage()]);
            }
        }

        return self::sqlIds($term, $fields);
    }

    /**
     * @param  array<int, string>  $fields
     * @return array<int, int>
     */
    private static function sqlIds(string $term, array $fields): array
    {
        $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], mb_strtolower($term)) . '%';

        return Orphanage::query()
            ->where('status', 1)
            ->where(function ($query) use ($fields, $like) {
                foreach ($fields as $field) {
                    $query->orWhereRaw('LOWER(' . self::FIELDS[$field] . ') COLLATE utf8mb4_unicode_ci LIKE ?', [$like]);
                }
            })
            ->pluck('id')
            ->all();
    }
}
