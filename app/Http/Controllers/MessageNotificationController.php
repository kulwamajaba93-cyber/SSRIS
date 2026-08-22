<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageNotificationController extends Controller
{
    public function unreadCount(Request $request)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['student', 'supervisor'], true)) {
            return response()->json(['count' => 0]);
        }

        return response()->json([
            'count' => $user->unreadMessagesCount(),
        ]);
    }

    public function poll(Request $request, ?User $student = null)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['student', 'supervisor'], true)) {
            return response()->json(['count' => 0, 'messages' => []]);
        }

        $count = $user->unreadMessagesCount();
        $messages = [];

        if ($request->boolean('conversation')) {
            $afterId = (int) $request->query('after_id', 0);

            if ($user->isStudent()) {
                $supervisor = $user->supervisor;
                if ($supervisor) {
                    $messages = $this->fetchNewMessages($user->id, $supervisor->id, $afterId);
                    if ($request->boolean('mark_read')) {
                        $user->unreadMessages()->where('sender_id', $supervisor->id)->update([
                            'read' => true,
                            'read_at' => now(),
                        ]);
                        $count = 0;
                    }
                }
            } elseif ($student && $student->supervisor_id === $user->id) {
                $messages = $this->fetchNewMessages($user->id, $student->id, $afterId);
                if ($request->boolean('mark_read')) {
                    $user->unreadMessages()->where('sender_id', $student->id)->update([
                        'read' => true,
                        'read_at' => now(),
                    ]);
                    $count = $user->unreadMessagesCount();
                }
            }
        }

        return response()->json([
            'count' => $count,
            'messages' => $messages,
        ]);
    }

    private function fetchNewMessages(int $user1, int $user2, int $afterId): array
    {
        $query = Message::between($user1, $user2)->orderBy('id');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        return $query->get()->map(fn (Message $message) => [
            'id' => $message->id,
            'message' => $message->message,
            'sender_id' => $message->sender_id,
            'is_mine' => $message->sender_id === auth()->id(),
            'read' => $message->read,
            'created_at' => $message->created_at->format('M j, Y g:i A'),
        ])->values()->all();
    }
}
