<?php

namespace App\Traits;

use Spatie\Translatable\HasTranslations as HasTranslationsBase;

trait HasTranslations
{
	use HasTranslationsBase;

	public function translate($locale = null): array
	{
		$locale = $locale ?? app()->getLocale();

		$translatableColumns = $this->translatable ?? [];

		$itemArray = $this->toArray();

		foreach ($translatableColumns ?? [] as $column) {
			$itemArray[$column] = $this->getTranslation($column, $locale);
		}


		return $itemArray;
	}
}
