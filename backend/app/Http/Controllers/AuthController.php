<?php

namespace App\Http\Controllers;

use App\Enums\TokenAbility;
use App\Jobs\SendConfirmationEmail;
use App\Mail\EmailAddressConfirmation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
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
            'password' => 'required|string|min:8',
            'avatar' => 'image|max:1024|dimensions:max_width=1000,max_height=1000'
        ]);

        do {
            $emailVerificationCode = hash("sha512", rand());
            $codeAlreadyExists = User::where('email_verification_code', $emailVerificationCode)->exists();
        } while ($codeAlreadyExists);

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->is_admin = false;
        $user->email_verification_code = $emailVerificationCode;
        if ($request->file('avatar') != null) {
            $aviPath = $request->file('avatar')->storePublicly('public');
            $pathParts = explode('/', $aviPath);
            $user->avatar_url = 'storage/' . end($pathParts);
        }
        $user->save();

        Mail::to($user->email)->send(new EmailAddressConfirmation($user));
        // SendConfirmationEmail::dispatch($user);

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

        // $request->session()->regenerate();

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

    public function testUpload(Request $request)
    {
        $request->validate([
            'avatar' => 'image|max:1024|dimensions:max_width=1000,max_height=1000'
        ]);

        $filePath = $request->file('avatar')->storePublicly('public');

        return response()->json([
            'partialFilePath' => $filePath,
            'filePath' => asset($filePath)
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'email',
            'avatar' => 'image|max:1024|dimensions:max_width=1000,max_height=1000'
        ]);

        $user = User::find(Auth::user()->id);

        if ($request->email != null) {
            $user->email = $request->email;
        }

        if ($request->name != null) {
            $user->name = $request->name;
        }

        if ($request->file('avatar') != null) {
            $aviPath = $request->file('avatar')->storePublicly('public');
            $pathParts = explode('/', $aviPath);
            $user->avatar_url = 'storage/' . end($pathParts);
        }
        $user->save();

        return response($user);
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

    public function getUserById(User $user)
    {
        return $user;
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

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed'
        ]);

        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully.'
        ]);
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

    public function delete(User $user)
    {
        if ($user !== null) {
            $user->delete();
        }
        return response()->json(['message' => 'User has been successfully deleted.']);
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
