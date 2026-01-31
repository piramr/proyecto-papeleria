<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        $hasQuestion = \Illuminate\Support\Facades\DB::table('user_security_answers')->where('user_id', $user->id)->exists();

        Validator::make($input, [
            'password' => $this->passwordRules(),
            'security_answer' => $hasQuestion ? ['required', 'string'] : ['nullable'],
        ])->after(function ($validator) use ($user, $input, $hasQuestion) {
            if ($hasQuestion) {
                 $record = \Illuminate\Support\Facades\DB::table('user_security_answers')->where('user_id', $user->id)->first();
                 if (!Hash::check(strtolower(trim($input['security_answer'] ?? '')), $record->answer)) {
                     $validator->errors()->add('security_answer', 'La respuesta de seguridad es incorrecta.');
                 }
            }
        })->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
