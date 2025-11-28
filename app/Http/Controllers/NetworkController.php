<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FriendRequest;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class NetworkController extends Controller
{
    public function getUsers(Request $request)
    {
        $query = $request->input('query');
        $currentUserId = Auth::id();

        $users = User::where('id', '!=', $currentUserId)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQ) use ($query) {
                    $subQ->where('email', 'like', "%{$query}%")
                         ->orWhere('name', 'like', "%{$query}%");
                });
            })
            ->get();

        $results = $users->map(function ($user) use ($currentUserId) {
            $existingRequest = FriendRequest::where(function($q) use ($user, $currentUserId) {
                $q->where('sender_id', $currentUserId)->where('receiver_id', $user->id);
            })->orWhere(function($q) use ($user, $currentUserId) {
                $q->where('sender_id', $user->id)->where('receiver_id', $currentUserId);
            })->first();

            $status = 'none';
            $direction = null;
            $requestId = null;

            if ($existingRequest) {
                $status = $existingRequest->status;
                $requestId = $existingRequest->id;
                if ($existingRequest->sender_id === $currentUserId) {
                    $direction = 'sent';
                } else {
                    $direction = 'received';
                }
            }

            return [
                'user' => $user,
                'status' => $status,
                'direction' => $direction,
                'request_id' => $requestId
            ];
        });

        return response()->json($results);
    }

    public function sendRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $friendRequest = FriendRequest::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Request sent successfully', 'request' => $friendRequest]);
    }

    public function acceptRequest(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:friend_requests,id',
        ]);

        $friendRequest = FriendRequest::find($request->request_id);
        
        if ($friendRequest->receiver_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $friendRequest->update(['status' => 'accepted']);

        return response()->json(['message' => 'Request accepted']);
    }

    public function getMessages($userId)
    {
        $messages = Message::where(function($q) use ($userId) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $userId);
        })->orWhere(function($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message);
    }
}
