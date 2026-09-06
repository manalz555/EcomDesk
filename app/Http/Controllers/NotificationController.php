<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

/** Handles clicking a notification in the bell dropdown: marks it read, then routes to what it was about. */
class NotificationController extends Controller
{
    public function read(Request $request, string $notification)
    {
        $record = $request->user()->notifications()->findOrFail($notification);
        $record->markAsRead();

        $conversationId = $record->data['conversation_id'] ?? null;

        // La conversation visee a pu etre supprimee depuis : on evite une 404 brute
        // et on redirige simplement vers la liste avec un message clair.
        if ($conversationId && Conversation::find($conversationId)) {
            return redirect()->route('conversations.show', $conversationId);
        }

        if ($conversationId) {
            return redirect()->route('conversations.index')->with('error', 'Cette conversation a été supprimée depuis.');
        }

        return redirect()->route('conversations.index');
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
