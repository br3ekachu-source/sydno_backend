<?php

namespace App\Http\Controllers;

use App\Http\Services\Files;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;

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
        $input = $request->post();
        if ($request['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            if (isset($input['name'])) {
                $user->name = $input['name'];
            }
            if (isset($input['phone_number'])) {
                $user->phone_number = $input['phone_number'];
            }
            $user->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        if (isset($input['name'])) {
            $user->name = $input['name'];
        }
        if (isset($input['phone_number'])) {
            $user->phone_number = $input['phone_number'];
        }
        $user->forceFill([
            'email_verified_at' => null
        ])->save();

        $user->sendEmailVerificationNotification();
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
