<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    

    //     public function index()
    // {
    //     event(new \App\Events\MyEvent('hello world'));

    //     return view('index');
    // }
    
    public function index()
    {
        $user = Auth::user();
        $conversations = $this->chatService->getUserConversations($user);
        $users = User::all();

        return view('chat.index', compact('conversations', 'user', 'users'));
    }


    public function openChat(User $user)
    {
        $conversation = $this->chatService->getOrCreateConversationWith($user);
        $this->chatService->markMessagesAsRead($conversation);
        $messages = $this->chatService->getMessages($conversation);
        $otherUser = $user;
        return view('chat.open-chat', compact('messages', 'conversation', 'otherUser'));
    }

    // public function storeMessage(MessageRequest $request)
    // {
    //     $this->chatService->storeMessage($request);

    //     return redirect()->back()->with('success', 'تم إرسال الرسالة');
    // }



}
