<?php

namespace App\Modules\Learning\Quiz\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Learning\Quiz\Models\Quiz;
use App\Modules\Learning\Quiz\Models\QuizQuestion;
use App\Modules\Learning\Quiz\Models\QuizAttempt;
use App\Modules\Learning\Quiz\Models\QuizAnswer;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use Illuminate\Http\Request;

class QuizWebController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'student' || $user->role === 'siswa') {
            $quizzes = Quiz::with(['class', 'subject'])
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->paginate(25);
        } else {
            $quizzes = Quiz::with(['class', 'subject'])
                ->orderBy('created_at', 'desc')
                ->paginate(25);
        }
        return view('kuis.index', compact('quizzes'));
    }

    public function create()
    {
        $classes = Kelas::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('kuis.form', compact('classes', 'subjects'));
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:kelas,id',
            'subject_id' => 'required|exists:subjects,id',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:draft,published',
        ]);
        $d['teacher_id'] = auth()->id();
        Quiz::create($d);
        return redirect()->route('kuis.index')->with('success', 'Kuis berhasil dibuat');
    }

    public function edit(Quiz $kuis)
    {
        $classes = Kelas::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        return view('kuis.form', compact('kuis', 'classes', 'subjects'));
    }

    public function update(Request $r, Quiz $kuis)
    {
        $d = $r->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:kelas,id',
            'subject_id' => 'required|exists:subjects,id',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:draft,published',
        ]);
        $kuis->update($d);
        return redirect()->route('kuis.index')->with('success', 'Kuis diperbarui');
    }

    public function destroy(Quiz $kuis)
    {
        $kuis->delete();
        return redirect()->route('kuis.index')->with('success', 'Kuis dihapus');
    }

    public function questions(Quiz $kuis)
    {
        $kuis->load('questions');
        return view('kuis.questions', compact('kuis'));
    }

    public function storeQuestion(Request $r, Quiz $kuis)
    {
        $d = $r->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,essay,true_false',
            'options' => 'nullable|json',
            'correct_answer' => 'nullable|string',
            'points' => 'required|integer|min:1',
        ]);
        $d['question_text'] = $d['question'];
        unset($d['question']);
        $kuis->questions()->create($d);
        return redirect()->route('kuis.questions', $kuis)->with('success', 'Soal ditambahkan');
    }

    public function destroyQuestion(QuizQuestion $soal)
    {
        $soal->delete();
        return redirect()->back()->with('success', 'Soal dihapus');
    }

    public function take(Quiz $kuis)
    {
        $user = auth()->user();
        if ($kuis->status !== 'published') {
            return redirect()->route('kuis.index')->with('error', 'Kuis belum dipublikasikan.');
        }

        if ($kuis->due_date && now()->gt($kuis->due_date)) {
            return redirect()->route('kuis.index')->with('error', 'Batas waktu pengerjaan kuis sudah berakhir.');
        }

        $existing = QuizAttempt::where('quiz_id', $kuis->id)
            ->where('student_id', $user->id)
            ->first();

        if ($existing && $existing->status === 'submitted') {
            return redirect()->route('kuis.result', $existing)->with('info', 'Anda sudah mengumpulkan kuis ini.');
        }

        if ($existing && $existing->status === 'in_progress') {
            $attempt = $existing;
        } else {
            $attempt = QuizAttempt::create([
                'quiz_id' => $kuis->id,
                'student_id' => $user->id,
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        }

        $quiz = Quiz::with(['questions' => function ($q) {
            $q->inRandomOrder();
        }, 'class', 'subject'])->findOrFail($kuis->id);

        return view('kuis.take', compact('quiz', 'attempt'));
    }

    public function submit(Request $r, QuizAttempt $attempt)
    {
        $user = auth()->user();
        if ($attempt->student_id !== $user->id) {
            abort(403);
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('kuis.result', $attempt);
        }

        $answers = $r->input('answers', []);
        foreach ($answers as $questionId => $answerText) {
            QuizAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                ],
                ['answer_text' => $answerText]
            );
        }

        $attempt->load('answers');
        $score = $attempt->calculateScore();
        $attempt->update([
            'submitted_at' => now(),
            'status' => 'submitted',
            'score' => $score,
        ]);

        return redirect()->route('kuis.result', $attempt)->with('success', 'Jawaban berhasil dikumpulkan.');
    }

    public function result(QuizAttempt $attempt)
    {
        $user = auth()->user();
        if ($attempt->student_id !== $user->id && $user->role !== 'admin' && $user->role !== 'teacher') {
            abort(403);
        }

        $attempt->load(['quiz.questions', 'answers']);
        $answers = $attempt->answers->keyBy('question_id');
        $totalPoints = $attempt->quiz->questions->sum('points');

        return view('kuis.result', compact('attempt', 'answers', 'totalPoints'));
    }

    public function grades(Quiz $kuis)
    {
        $attempts = QuizAttempt::with('student')
            ->where('quiz_id', $kuis->id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        $quiz = $kuis;

        return view('kuis.grades', compact('quiz', 'attempts'));
    }

    public function gradeAttempt(QuizAttempt $attempt)
    {
        $attempt->load(['quiz.questions', 'answers', 'student']);
        $answers = $attempt->answers->keyBy('question_id');
        $totalPoints = $attempt->quiz->questions->sum('points');

        return view('kuis.grade-attempt', compact('attempt', 'answers', 'totalPoints'));
    }

    public function gradeQuestion(Request $r, QuizAttempt $attempt, QuizQuestion $question)
    {
        $data = $r->validate(['score' => 'required|integer|min:0']);
        $answer = QuizAnswer::where('attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->firstOrFail();
        $answer->update(['score' => $data['score']]);

        $attempt->load('answers');
        $score = $attempt->calculateScore();
        $attempt->update(['score' => $score, 'status' => 'submitted']);

        return redirect()->route('kuis.gradeAttempt', $attempt)->with('success', 'Nilai essay disimpan.');
    }
}
