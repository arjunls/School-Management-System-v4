<?php

namespace App\Modules\Communication\Message\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Communication\Message\Models\Conversation;
use App\Modules\Communication\Message\Models\Message;
use Illuminate\Http\Request;

class MessageWebController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $conversations = $user->conversations()
            ->with(['participants:id,name,role', 'lastMessage.sender:id,name'])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()->take(1)
            )->get();
        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $message)
    {
        $user = auth()->user();
        if (!$message->participants()->where('user_id', $user->id)->exists()) {
            abort(403);
        }
        $message->participants()->updateExistingPivot($user->id, ['last_read_at' => now()]);
        $messages = $message->messages()->with('sender:id,name,role')->orderBy('created_at')->get();
        $conversations = $user->conversations()
            ->with(['participants:id,name,role', 'lastMessage.sender:id,name'])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()->take(1)
            )->get();
        return view('messages.show', compact('message', 'messages', 'conversations'));
    }

    public function create()
    {
        $users = User::where('id', '!=', auth()->id())->orderBy('name')->get();
        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'nullable|string|max:255',
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:users,id',
            'body' => 'required|string',
        ]);

        $user = auth()->user();
        $ids = array_unique(array_merge($data['participant_ids'], [$user->id]));

        $conv = Conversation::create([
            'subject' => $data['subject'] ?? null,
            'created_by' => $user->id,
        ]);
        $conv->participants()->attach($ids);

        $conv->messages()->create([
            'sender_id' => $user->id,
            'body' => $data['body'],
        ]);

        return redirect()->route('messages.show', $conv)->with('success', 'Pesan terkirim');
    }

    public function reply(Request $request, Conversation $message)
    {
        $user = auth()->user();
        if (!$message->participants()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        $data = $request->validate(['body' => 'required|string']);

        $message->messages()->create([
            'sender_id' => $user->id,
            'body' => $data['body'],
        ]);

        return redirect()->route('messages.show', $message)->with('success', 'Balasan terkirim');
    }
}
