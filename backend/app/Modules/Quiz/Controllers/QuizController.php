<?php

namespace App\Modules\Quiz\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Quiz\Models\Quiz;
use App\Modules\Quiz\Models\QuizQuestion;
use App\Modules\Quiz\Models\QuizAttempt;
use App\Modules\Quiz\Models\QuizAnswer;
use Illuminate\Http\Request;
use App\Modules\Quiz\Requests\StoreQuizRequest;
use App\Modules\Quiz\Requests\AddQuestionRequest;
use App\Modules\Quiz\Requests\SubmitQuizRequest;
use App\Modules\Quiz\Requests\GradeEssayRequest;

/**
 * @group Quizzes
 *
 * APIs for managing quizzes
 */
class QuizController extends Controller
{
    /**
     * List all quizzes
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Quiz::with(['class:id,name', 'subject:id,name', 'teacher:id,name']);

        if ($user->role === 'teacher') $query->where('teacher_id', $user->id);
        if ($user->role === 'student') $query->where('status', 'published');

        if ($c = $request->class_id) $query->where('class_id', $c);
        if ($s = $request->subject_id) $query->where('subject_id', $s);

        return response()->json(['success' => true, 'data' => $query->orderByDesc('created_at')->paginate($request->per_page ?? 20)]);
    }

    /**
     * Get a quiz by ID
     */
    public function show(int $id)
    {
        $quiz = Quiz::with(['class:id,name', 'subject:id,name', 'teacher:id,name', 'questions'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $quiz]);
    }

    /**
     * Create a new quiz
     */
    public function store(StoreQuizRequest $request)
    {
        $data = $request->validated();
        $data['teacher_id'] = $request->user()->id;
        $quiz = Quiz::create($data);
        return response()->json(['success' => true, 'data' => $quiz, 'message' => 'Quiz created'], 201);
    }

    /**
     * Update an existing quiz
     */
    public function update(Request $request, int $id)
    {
        $quiz = Quiz::where('teacher_id', $request->user()->id)->findOrFail($id);
        $quiz->update($request->only(['title', 'description', 'time_limit', 'passing_score', 'due_date', 'status']));
        return response()->json(['success' => true, 'data' => $quiz, 'message' => 'Updated']);
    }

    /**
     * Delete a quiz
     */
    public function destroy(int $id)
    {
        $quiz = Quiz::findOrFail($id);
        if (request()->user()->role !== 'admin' && $quiz->teacher_id !== request()->user()->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        $quiz->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }

    // Questions
    /**
     * Add a question to a quiz
     */
    public function addQuestion(AddQuestionRequest $request, int $quizId)
    {
        $quiz = Quiz::where('teacher_id', $request->user()->id)->findOrFail($quizId);

        $data = $request->validated();
        if ($data['type'] === 'multiple_choice' && !$data['correct_answer']) {
            return response()->json(['success' => false, 'message' => 'Multiple choice requires correct_answer'], 422);
        }

        $data['quiz_id'] = $quizId;
        $question = QuizQuestion::create($data);
        return response()->json(['success' => true, 'data' => $question, 'message' => 'Question added'], 201);
    }

    /**
     * Update a quiz question
     */
    public function updateQuestion(Request $request, int $id)
    {
        $question = QuizQuestion::findOrFail($id);
        $question->update($request->only(['question_text', 'type', 'options', 'correct_answer', 'points']));
        return response()->json(['success' => true, 'data' => $question, 'message' => 'Question updated']);
    }

    /**
     * Delete a quiz question
     */
    public function deleteQuestion(int $id)
    {
        QuizQuestion::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Question deleted']);
    }

    // Attempts
    /**
     * Start a quiz attempt
     */
    public function start(int $quizId)
    {
        $user = request()->user();
        $quiz = Quiz::where('status', 'published')->findOrFail($quizId);

        $existing = QuizAttempt::where('quiz_id', $quizId)->where('student_id', $user->id)->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Already attempted'], 400);
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'student_id' => $user->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $quiz->load('questions');
        return response()->json(['success' => true, 'data' => ['attempt' => $attempt, 'quiz' => $quiz]]);
    }

    /**
     * Submit quiz answers
     */
    public function submit(SubmitQuizRequest $request, int $attemptId)
    {
        $attempt = QuizAttempt::with(['quiz.questions'])->where('student_id', $request->user()->id)->findOrFail($attemptId);
        if ($attempt->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Already submitted'], 400);
        }

        foreach ($request->answers as $ans) {
            $question = $attempt->quiz->questions->firstWhere('id', $ans['question_id']);
            if (!$question) continue;

            QuizAnswer::updateOrCreate(
                ['attempt_id' => $attemptId, 'question_id' => $ans['question_id']],
                ['answer_text' => $ans['answer_text'] ?? '']
            );
        }

        $attempt->load('answers');
        $attempt->update(['submitted_at' => now(), 'status' => 'submitted', 'score' => $attempt->calculateScore()]);

        return response()->json(['success' => true, 'data' => $attempt, 'message' => 'Submitted']);
    }

    /**
     * List quiz attempts
     */
    public function attemptsList(Request $request)
    {
        $user = $request->user();
        $query = QuizAttempt::with(['quiz:id,title', 'student:id,name']);

        if ($user->role === 'student') $query->where('student_id', $user->id);
        if ($qId = $request->quiz_id) $query->where('quiz_id', $qId);

        return response()->json(['success' => true, 'data' => $query->orderByDesc('created_at')->paginate($request->per_page ?? 20)]);
    }

    /**
     * Grade an essay question
     */
    public function gradeEssay(GradeEssayRequest $request, int $attemptId, int $questionId)
    {
        $answer = QuizAnswer::where('attempt_id', $attemptId)->where('question_id', $questionId)->firstOrFail();
        $answer->update(['score' => $request->score]);

        $attempt = QuizAttempt::with('answers')->find($attemptId);
        $attempt->update(['score' => $attempt->calculateScore()]);

        return response()->json(['success' => true, 'data' => $answer, 'message' => 'Graded']);
    }
}
