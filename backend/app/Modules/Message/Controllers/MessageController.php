<?php

namespace App\Modules\Message\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Message\Models\Conversation;
use App\Modules\Message\Models\Message;
use Illuminate\Http\Request;
use App\Modules\Message\Requests\SendMessageRequest;
use App\Modules\Message\Requests\CreateConversationRequest;

/**
 * @group Messages
 *
 * APIs for managing messages
 */
class MessageController extends Controller
{
    /**
     * List user conversations
     */
    public function conversations(Request $request)
    {
        $user = $request->user();
        $conversations = $user->conversations()
            ->with(['participants:id,name,role', 'lastMessage.sender:id,name'])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->take(1)
            )
            ->get();

        return response()->json(['success' => true, 'data' => $conversations]);
    }

    /**
     * Get messages in a conversation
     */
    public function messages(Request $request, int $conversationId)
    {
        $user = $request->user();
        $conv = Conversation::findOrFail($conversationId);

        if (!$conv->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $conv->participants()->updateExistingPivot($user->id, ['last_read_at' => now()]);

        $messages = $conv->messages()->with('sender:id,name,role')->orderBy('created_at')->paginate(50);
        return response()->json(['success' => true, 'data' => $messages]);
    }

    /**
     * Send a message in a conversation
     */
    public function send(SendMessageRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $conv = Conversation::findOrFail($data['conversation_id']);
        if (!$conv->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $message = $conv->messages()->create([
            'sender_id' => $user->id,
            'body' => $data['body'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $message->load('sender:id,name,role'),
            'message' => 'Message sent',
        ], 201);
    }

    /**
     * Create a new conversation
     */
    public function createConversation(CreateConversationRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $ids = array_unique(array_merge($data['participant_ids'], [$user->id]));

        $conv = Conversation::create(['subject' => $data['subject'] ?? null, 'created_by' => $user->id]);
        $conv->participants()->attach($ids);

        return response()->json([
            'success' => true,
            'data' => $conv->load('participants:id,name,role'),
            'message' => 'Conversation created',
        ], 201);
    }
}
