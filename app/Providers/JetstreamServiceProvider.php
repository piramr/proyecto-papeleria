<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Vite::prefetch(concurrency: 3);
        
        // Pass Security Question to Reset Password View
        \Laravel\Fortify\Fortify::resetPasswordView(function ($request) {
            $user = \App\Models\User::where('email', $request->email)->first();
            $question = null;
            
            if ($user) {
                $question = \Illuminate\Support\Facades\DB::table('user_security_answers')
                                ->join('security_questions', 'user_security_answers.security_question_id', '=', 'security_questions.id')
                                ->where('user_id', $user->id)
                                ->select('security_questions.question')
                                ->first();
            }

            return view('auth.reset-password', ['request' => $request, 'question' => $question?->question]);
        });
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
