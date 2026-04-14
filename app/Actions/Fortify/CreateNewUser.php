<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'no_wa' => ['required', 'string', 'max:20'],
            'password' => $this->passwordRules(),
        ])->validate();

        $pemilikKosRole = Role::where('name', 'pemilik_kos')->first();

        return User::create([
            'username' => $input['username'],
            'email' => $input['email'],
            'no_wa' => $input['no_wa'],
            'password' => $input['password'],
            'role_id' => $pemilikKosRole ? $pemilikKosRole->id : null,
        ]);
    }
}
