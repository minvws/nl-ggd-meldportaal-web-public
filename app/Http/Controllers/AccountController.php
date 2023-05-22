<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccountController extends BaseController
{
    use PasswordValidationRules;

    /**
     * @param Request $request
     * @return RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if (is_null($user)) {
            return redirect(route('home'));
        }

        if (! $user->canChangePassword()) {
            return redirect(route('home'));
        }

        // Validate if password information is correct
        $input = $request->all();
        Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($user, $input) {
            if (!Hash::check($input['current_password'] ?? '', $user->password ?? '')) {
                $validator->errors()->add(
                    'current_password',
                    __('The provided password does not match your current password.')
                );
            } elseif ($input['current_password'] === $input['password']) {
                $validator->errors()->add(
                    'password',
                    __('The chosen password is identical to the current password')
                );
            } elseif ($input['password_confirmation'] !== $input['password']) {
                $validator->errors()->add(
                    'password_confirmation',
                    __('The password confirmation does not match the password.')
                );
            }
        })->validate();

        // Add new password, and set the password updated_at so we can actually do something on the site
        $user->forceFill([
            'password' => Hash::make($input['password']),
            'password_updated_at' => now(),
        ])->save();

        return redirect("/");
    }
}
