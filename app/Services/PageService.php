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
            'blocks' => $blocks,
        ];

        return $data;
    }
}
