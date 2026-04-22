<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Facades\Message as MessageFacade;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages for the authenticated user.
     */
    public function index()
    {
        $messages = Message::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Messages/Index', [
            'messages' => $messages
        ]);
    }

    /**
     * Mark the specified message as read.
     */
    public function update(Request $request, Message $message)
    {
        $this->authorize('update', $message);

        MessageFacade::markAsRead($message);

        return back();
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);

        $message->delete();

        return back()->with('message', 'deleted');
    }
}
