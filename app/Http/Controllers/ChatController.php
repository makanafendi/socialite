<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function followingList()
    {
        $authUserId = Auth::id();
        $following = Auth::user()->following;

        // Get the last message for each user
        $following = $following->map(function ($user) use ($authUserId) {
            // Find the last message between the current user and this user
            $lastMessage = Message::where(function ($query) use ($user, $authUserId) {
                $query->where('sender_id', $authUserId)->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($user, $authUserId) {
                $query->where('sender_id', $user->id)->where('receiver_id', $authUserId);
            })->latest()->first();

            // Add the last message to the user object
            $user->last_message = $lastMessage ? $lastMessage->message : null;
            $user->last_message_time = $lastMessage ? $lastMessage->created_at : null;
            $user->is_sender = $lastMessage ? ($lastMessage->sender_id == $authUserId) : false;

            return $user;
        });

        return view('chat.following', compact('following'));
    }

    public function chatWithUser($userId)
    {
        $user = User::findOrFail($userId);
        return view('chat.conversation', compact('user'));
    }

    public function fetchMessages($userId)
    {
        return Message::where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message);
    }
}
