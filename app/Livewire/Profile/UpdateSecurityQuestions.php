<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateSecurityQuestions extends Component
{
    public $question_id;
    public $answer;

    public function mount()
    {
        $user = Auth::user();
        $current = DB::table('user_security_answers')
                    ->where('user_id', $user->id)
                    ->first();
        
        if ($current) {
            $this->question_id = $current->security_question_id;
        }
    }

    public function updateSecurityQuestion()
    {
        $this->validate([
            'question_id' => 'required|exists:security_questions,id',
            'answer' => 'required|string|min:3',
        ]);

        $user = Auth::user();

        // Update or Insert
        DB::table('user_security_answers')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'security_question_id' => $this->question_id,
                'answer' => Hash::make(strtolower(trim($this->answer))), // Store hashed, lowercase for consistency
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->answer = ''; // Clear answer
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.profile.update-security-questions', [
            'questions' => DB::table('security_questions')->get()
        ]);
    }
}
