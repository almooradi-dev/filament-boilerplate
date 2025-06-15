<?php

namespace App\Traits;

use App\Constants\Core\NotificationType;
use App\Models\Core\UserNotification;
use Exception;

trait HasNotifications
{
	/**
	 * Send notification to user's devices
	 *
	 * @param string $title
	 * @param string $body
	 * @return void
	 */
	// TODO: Add updates to boilerplate
	public function sendNotification(string|array $title, string|array|null $body = null, ?int $type = null, ?array $data = null): void
	{
		$this->storeNotification($title, $body, $type, $data);
	}

	public function storeNotification(string|array $title, string|array|null $body = null, ?int $type = null, ?array $data = null)
	{
		try {
			$notification = UserNotification::create([
				'title' => $title,
				'body' => $body,
				'user_id' => $this->id,
				'type' => $type,
				'data' => $data,
			]);

			return $notification;
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
}
