<?php

namespace App\Http\Controllers\Core\API;

use App\Http\Controllers\APIController;
use App\Services\PageService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PageAPIController extends APIController
{
	public function get(string $key): JsonResponse
	{
		try {
			$page = PageService::get($key);

			return $this->sendResponse($page);
		} catch (Throwable $th) {
			throw $th;
		}
	}
}
