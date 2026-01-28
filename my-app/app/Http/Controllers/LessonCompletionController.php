<?php

namespace App\Http\Controllers;

use App\Models\LessonCompletion;
use App\Models\TrainingLesson;
use App\Models\TrainingModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonCompletionController extends Controller
{
    public function toggle(Request $request, TrainingModule $trainingModule, TrainingLesson $lesson)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'PARTICIPANT') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($lesson->training_module_id !== $trainingModule->id) {
            return response()->json(['message' => 'Invalid lesson for module'], 422);
        }

        $request->validate([
            'completed' => ['required', 'boolean'],
        ]);

        if ($request->boolean('completed')) {
            $completion = LessonCompletion::firstOrNew([
                'user_id' => $user->id,
                'training_module_id' => $trainingModule->id,
                'training_lesson_id' => $lesson->id,
            ]);

            if (! $completion->exists) {
                $completion->completed_at = now();
                $completion->save();
            }
        } else {
            LessonCompletion::where('user_id', $user->id)
                ->where('training_module_id', $trainingModule->id)
                ->where('training_lesson_id', $lesson->id)
                ->delete();
        }

        return response()->json([
            'message' => 'Lesson completion updated',
        ]);
    }
}


