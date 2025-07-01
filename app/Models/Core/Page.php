<?php

namespace App\Models\Core;

use App\Traits\HasIsActive;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes, HasTranslations, HasIsActive;

    protected $guarded = [];

    public $translatable = ['name', 'content', 'metadata'];

    public $mediaFieldsByBlockType = [ // 'type' => ['fields']
        'info_with_images' => ['images'],
    ];

    public function getContentAttribute($value)
    {
        $blocks = is_array($value) ? $value : json_decode($value, true);

        // Full storage path
        $blocksWithMedia = array_keys($this->mediaFieldsByBlockType);
        foreach ($blocks as $index => $block) {
            if (!in_array($block['type'], $blocksWithMedia)) {
                continue;
            }

            foreach ($this->mediaFieldsByBlockType[$block['type']] as $field) {
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
