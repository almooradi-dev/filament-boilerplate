<?php

namespace App\Http\Controllers\Core\API\Auth;

use App\Http\Controllers\APIController;
use App\Http\Requests\Core\API\Auth\LoginRequest;
use App\Http\Requests\Core\API\Auth\RegisterRequest;
use App\Http\Requests\Core\API\Auth\ResetPasswordRequest;
use App\Http\Resources\Core\API\User\UserAuthResource;
use App\Models\Core\Auth\FirebaseDeviceToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Auth\UserRecord;
use Throwable;

class AuthAPIController extends APIController
{
	/**
	 * Register
	 *
	 * @param RegisterRequest $request
	 * @return JsonResponse
	 */
	public function register(RegisterRequest $request): JsonResponse
	{
		DB::beginTransaction();

		try {
			$insertData = collect($request->validated())->except([
				'password',
				'password_confirmation',
				'termas_and_conditions'
			])->toArray();

			// Hash the password
			$insertData['password'] = Hash::make($request->password);

			// Store the user's data
			$user = User::create($insertData);
			$user->update(['email_verified_at' => now()]); // FIXME: Temp until the OTP is fixed
			$user->update(['phone_verified_at' => now()]); // FIXME: Temp until the OTP is fixed

			// Assign role
			$user->assignRole('user');

			$data['user'] = new UserAuthResource($user);

			DB::commit();

			return $this->sendResponse($data, __('auth.register.success'));
		} catch (Throwable $th) {
			DB::rollBack();

			throw $th;
		}
	}

	/**
	 * Login
	 *
	 * @param LoginRequest $request
	 * @return JsonResponse
	 */
	public function login(LoginRequest $request): JsonResponse
	{
		try {
			$postData = $request->only(['email', 'username', 'password']);

			if (!Auth::attempt($postData)) {
				return $this->sendError(__('auth.failed'), [], 404);
			}

			$user = Auth::user();

			$data['user'] = new UserAuthResource($user);

			// Check if account is verified
            // TODO:
			// if (!$user->account_verified) {
			// 	return $this->sendError(__('auth.login.fail.verification.account'), ['user' => $data['user']], 403, 'account_verification_failed');
			// }

			// TODO: Delete expired token

			// Store Firebase Token for this device
			if ($request->firebase_device_token) {
				FirebaseDeviceToken::updateOrCreate(
                    // TODO: Fix this for multiple device, because this will override tokens from other devices
					['token' => $request->firebase_device_token],
					[
						'user_id' => $user->id,
					]
				);
			}

			$data['token'] = $user->createToken("API TOKEN", expiresAt: Carbon::now()->addMinutes(config('sanctum.expiration')))->plainTextToken;

			return $this->sendResponse($data, __('auth.login.success'));
		} catch (Throwable $th) {
			throw $th;
		}
	}

	/**
	 * Verify phone address
	 * 
	 * @param VerifyPhoneRequest $request
	 * @return JsonResponse
	 */
	// public function verifyPhone(VerifyPhoneRequest $request): JsonResponse
	// {
	// 	try {
	// 		$user = User::query()
	// 			->where('country_code', $request->country_code)
	// 			->where('phone', $request->phone)
	// 			->first();

	// 		if (!$user) {
	// 			return $this->sendError(__('auth.verify.phone.fail.user_not_found'), code: 404);
	// 		} else if ($user->phone_otp != $request->otp) {
	// 			return $this->sendError(__('auth.send-otp.fail.otp_wrong'), code: 404);
	// 		} else if ($user->phone_otp_expires_at <= Carbon::now()) {
	// 			return $this->sendError(__('auth.send-otp.fail.otp_expired'), code: 404);
	// 		}

	// 		$user->update(['phone_verified_at' => Carbon::now()]);

	// 		$data['user'] = new UserAuthResource($user);

	// 		return $this->sendResponse($data, __('auth.verify.phone.success'));
	// 	} catch (Throwable $e) {
	// 		return $this->sendError(__('auth.verify.phone.fail'), $e->getMessage(), code: 400);
	// 	}
	// }

	/**
	 * Verify email address
	 * 
	 * @param VerifyEmailRequest $request
	 * @return JsonResponse
	 */
	// public function verifyEmail(VerifyEmailRequest $request): JsonResponse
	// {
	// 	try {
	// 		$user = User::query()
	// 			->where('email', $request->email)
	// 			->first();

	// 		if (!$user) {
	// 			return $this->sendError(__('auth.verify.email.fail.user_not_found'), code: 404);
	// 		} else if ($user->email_otp != $request->otp) {
	// 			return $this->sendError(__('auth.send-otp.fail.otp_wrong'), code: 404);
	// 		} else if ($user->email_otp_expires_at <= Carbon::now()) {
	// 			return $this->sendError(__('auth.send-otp.fail.otp_expired'), code: 404);
	// 		}

	// 		$user->update(['email_verified_at' => Carbon::now()]);

	// 		$data['user'] = new UserAuthResource($user);

	// 		return $this->sendResponse($data, __('auth.verify.email.success'));
	// 	} catch (Throwable $e) {
	// 		return $this->sendError(__('auth.verify.email.fail'), $e->getMessage(), code: 400);
	// 	}
	// }

	/**
	 * Logout from current device
	 *
	 * @return JsonResponse
	 */
	public function logout(): JsonResponse
	{
		auth()->user()?->logout();

		return $this->sendResponse([], __('auth.logout.success'));
	}

	/**
	 * Reset password
	 *
	 * @return JsonResponse
	 */
	public function resetPassword(ResetPasswordRequest $request): JsonResponse
	{
        // TODO:
		try {
			$isByEmail = isset($request->email);
			$user = User::query()
				->when($isByEmail, fn ($query) => $query->where('email', $request->email))
				->when(!$isByEmail, function ($query) use ($request) {
					$query->where('country_code', $request->country_code)
					->where('phone', $request->phone);
				})
				->first();

			$userOTP = $isByEmail ? $user?->email_otp : $user?->phone_otp;
			$userOTPExpiry = $isByEmail ? $user?->email_otp_expires_at : $user?->phone_otp_expires_at;
			if (!$user) {
				return $this->sendError(__('auth.password.reset.fail.user_not_found'), code: 404);
			} else if ($userOTP != $request->otp) {
				return $this->sendError(__('auth.password.reset.fail.otp_wrong'), code: 404);
			} else if ($userOTPExpiry <= Carbon::now()) {
				return $this->sendError(__('auth.password.reset.fail.otp_expired'), code: 404);
			}

			$user->update(['password' => Hash::make($request->password)]);

			return $this->sendResponse(message: __('auth.password.reset.success'));
		} catch (Throwable $e) {
			return $this->sendError(__('auth.password.reset.fail'), $e->getMessage(), code: 400);
		}
	}

	/**
	 * Get Firebase User
	 *
	 * @param string $tokenID
	 * @param boolean $checkIfRevoked
	 * @param boolean $revokeAfterFetch
	 * @return UserRecord
	 */
	// private function getFirebaseUser(string $tokenID, $checkIfRevoked = true, $revokeAfterFetch = false, $throwError = false): UserRecord|null
	// {
	// 	try {
	// 		$auth = app('firebase.auth');

	// 		$verifiedIdToken = $auth->verifyIdToken($tokenID, $checkIfRevoked);

	// 		$uid = $verifiedIdToken->claims()->get('sub');

	// 		$firebaseUser = $auth->getUser($uid);

	// 		if ($revokeAfterFetch) {
	// 			$auth->revokeRefreshTokens($uid);
	// 		}

	// 		return $firebaseUser;
	// 	} catch (RevokedIdToken | FailedToVerifyToken $e) {
	// 		if ($throwError) {
	// 			throw $e;
	// 		}

	// 		return null;
	// 	}
	// }

	/**
	 * Validate user's token
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	// public function validateToken(Request $request): JsonResponse
	// {
	// 	$request->validate([
	// 		'token' => 'required',
	// 		'user_id' => 'required'
	// 	]);

	// 	$token = PersonalAccessToken::findToken($request->token);
	// 	$user = $token?->tokenable;

	// 	// $isValid = $token ? $token->expires_at >= Carbon::now() && $user?->id == $request->user_id && $token?->mac_address == getMAC() : false;
	// 	$isValid = $token ? $token->expires_at >= Carbon::now() && $user?->id == $request->user_id : false;
	// 	$data['valid'] = $isValid;
	// 	$data['user'] = $isValid ? new UserAuthResource($user) : null;
	// 	$data['token'] = $request->token;

	// 	return $this->sendResponse($data);
	// }

	/**
	 * Send phone OTP
	 *
	 * @param SendPhoneOTPRequest $request
	 * @return JsonResponse
	 */
	// public function sendPhoneOTP(SendPhoneOTPRequest $request): JsonResponse
	// {
	// 	$response = (new AuthService())->sendPhoneOTP($request->country_code, $request->phone);

	// 	if (isset($response['error']) && $response['error'] && $response['key'] == 'user_not_found') {
	// 		return $this->sendError(__('auth.send-otp.fail.user_not_found'), code: 404);
	// 	}

	// 	return $this->sendResponse($response);
	// }

	/**
	 * Send email OTP
	 *
	 * @param SendEmailOTPRequest $request
	 * @return JsonResponse
	 */
	// public function sendEmailOTP(SendEmailOTPRequest $request): JsonResponse
	// {
	// 	$response = (new AuthService())->sendEmailOTP($request->email);

	// 	if (isset($response['error']) && $response['error'] && $response['key'] == 'user_not_found') {
	// 		return $this->sendError(__('auth.send-otp.fail.user_not_found'), code: 404);
	// 	}

	// 	return $this->sendResponse($response);
	// }
}