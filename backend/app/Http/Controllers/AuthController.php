<?php

namespace App\Http\Controllers;

use App\Enums\TokenAbility;
use App\Mail\EmailAddressConfirmation;
use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isNull;

class AuthController extends Controller
{
    public function register(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        do {
            $emailVerificationCode = hash("sha512", rand());
            $codeAlreadyExists = User::where('email_verification_code', $emailVerificationCode)->exists();
        } while ($codeAlreadyExists);

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->is_admin = $request->input('is_admin') ?? false;
        $user->email_verification_code = $emailVerificationCode;
        $user->save();

        Mail::to($user->email)->send(new EmailAddressConfirmation($user));

        return response([
            'name' => $user->name
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $accessToken = $this->createAccessToken($user)->plainTextToken;

        $refreshToken = $this->createRefreshToken($user)->plainTextToken;

        return response()->json([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
            'name' => $user->name,
            'isAdmin' => $user->isAdmin,
            'userId' => $user->id
        ]);
    }

    public function verifyEmail(string $code)
    {
        $user = User::where('email_verification_code', $code)->first();
        if (isNull($user)) {
            return "Nigga";
        }
        $user->email_verification_code = null;
        $user->email_verified_at = time();
        $user->save();

        return 'Verified';
    }

    public function showEmailVerificationView()
    {
        return view('emails.emailAddressConfirmation', ['user' => User::first()]);
    }

    public function getCurrentUser()
    {
        return Auth::user();
    }

    public function refreshToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'accessToken' => $this->createAccessToken($request->user())->plainTextToken,
            'refreshToken' => $this->createRefreshToken($request->user())->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->has('accessToken')) {
            PersonalAccessToken::findToken($request->input('accessToken'))?->delete();
        }
        if ($request->has('refreshToken')) {
            PersonalAccessToken::findToken($request->input('refreshToken'))?->delete();
        }

        return ['message' => 'Logged out successfully.'];
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return
            response()->json(
                ['message' => __($status)],
                $status === Password::RESET_LINK_SENT ? 200 : 404
            );
    }

    public function resetPasswordWithToken(Request $request, string $token)
    {

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                // event(new PasswordReset($user));
            }
        );

        return response()->json(
            ['message' => __($status)],
            $status === Password::PASSWORD_RESET ? 200 : 404
        );
    }

    // Internal functions not availabe to router
    private function createAccessToken(User $user)
    {
        return $user->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value],
            Carbon::now()->addDays(
                config('sanctum.access_token_expiration')
            )
        );
    }

    private function createRefreshToken(User $user)
    {
        return $user->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            Carbon::now()->addDays(
                config('sanctum.refresh_token_expiration')
            )
        );
    }

    private function revokePlaintextToken($user, string $token)
    {
        $tokenId = explode('|', $token)[0];
        $user->tokens()->where('id', $tokenId)->delete();
    }
}
