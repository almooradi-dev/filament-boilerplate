<?php

namespace App\Services;

use App\Models\Core\Page;

class PageService
{
    /**
     * Get page data by key
     *
     * @param [type] $key
     * @return array
     */
    public static function get($key): array
    {
        $page = Page::where('key', $key)->first()?->translate();
        if (!$page) {
            return [];
        }

        $blocks = [];
        foreach ($page['content'] ?? [] as $block) {
            if (isset($block['data']['key'])) {
                $blocks[$block['data']['key']] = $block;
            }
        }

        $data = [
            'general' => [
                'name' => $page['name'],
                'key' => $page['key'],
                'is_active' => $page['is_active'],
            ],
            'metadata' => $page['metadata'] ?? [],
            'blocks' => static::fixBlocksMediaLink($blocks),
        ];

        return $data;
    }

    private static function fixBlocksMediaLink($blocks)
    {
        // Full storage path
        $blocksWithMedia = array_keys(Page::$mediaFieldsByBlockType);
        foreach ($blocks as $index => $block) {
            if (!in_array($block['type'], $blocksWithMedia)) {
                continue;
            }

            foreach (Page::$mediaFieldsByBlockType[$block['type']] as $field) {
                $fieldData = $block['data'][$field];
                if (is_array($fieldData)) {
                    foreach ($fieldData as $rowIndex => $value) {
                        $fieldData[$rowIndex] = url('storage/' . ltrim($value, '/'));
                    }
                } else {
                    $fieldData = url('storage/' . ltrim($fieldData, '/'));
                }
                $blocks[$index]['data'][$field] = $fieldData;
            }
        }

        return $blocks;
    }
}
