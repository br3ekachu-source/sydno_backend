<?php

namespace App\Http\Controllers;

use App\Http\Services\Files;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function get(Request $request)
    {
        $user = $request->user();
        $response = [];
        $response['id'] = $user->id;
        $response['name'] = $user->name;
        $response['phone_number'] = $user->phone_number;
        $response['email'] = $user->email;
        $response['email_verified_at'] = $user->email_verified_at;
        $response['avatar'] = isset($user->avatar) && Storage::exists($user->avatar) ? Files::getUrl($user->avatar) : null;
        $response['created_at'] = $user->created_at;

        return response()->json($response);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['string', 'max:255'],
            'phone_number' => ['max:20']
        ]);

        $user = User::find($request->user()->id);

        if ($request['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $request);
        } else {
            if (isset($input['name'])) {
                $user->name = $request['name'];
            }
            if (isset($input['phone_number'])) {
                $user->phone_number = $request['phone_number'];
            }
            $user->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, Request $request): void
    {
        if (isset($request['name'])) {
            $user->name = $request['name'];
        }
        if (isset($request['phone_number'])) {
            $user->phone_number = $request['phone_number'];
        }
        $user->forceFill([
            'email_verified_at' => null
        ])->save();

        $user->sendEmailVerificationNotification();
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request['password_confirmation'] != $request['password']) {
            return 'The provided password does not match password confirmation.';
        }

        $user = User::find($request->user()->id);

        $user->forceFill([
            'password' => Hash::make($request['password']),
        ])->save();
    }

    public function show($id)
    {
        $user = User::find($id);
        $response = [];
        $response['id'] = $user->id;
        $response['name'] = $user->name;
        $response['phone_number'] = $user->phone_number;
        $response['email'] = $user->email;
        $response['email_verified_at'] = $user->email_verified_at;
        $response['avatar'] = isset($user->avatar) && Storage::exists($user->avatar) ? Files::getUrl($user->avatar) : null;
        $response['created_at'] = $user->created_at;

        return response()->json($response);
    }
}
