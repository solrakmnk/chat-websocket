<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function show(Chat $chat)
    {
        abort_unless($chat->users->contains(auth()->id()), 403);

        return view('chat', [
            'chat' => $chat
        ]);

    }

    public function chat_with(User $user)
    {

        $user_a = auth()->user();

        $user_b = $user;

        $chat = $user_a->chats()->whereHas('users', function ($q) use ($user_b) {

            $q->where('chat_user.user_id', $user_b->id);

        })->first();

        if (!$chat) {

            $chat = \App\Models\Chat::create([]);

            $chat->users()->sync([$user_a->id, $user_b->id]);

        }

        return redirect()->route('chat.show', $chat);

    }
    public function get_messages(Chat $chat)
    {

        $messages = $chat->messages()->with('user')->get();

        return response()->json([
            'messages' => $messages
        ]);

    }
    public function get_users(Chat $chat)
    {
        $users = $chat->users;

        return response()->json([
            'users' => $users
        ]);
    }
}
