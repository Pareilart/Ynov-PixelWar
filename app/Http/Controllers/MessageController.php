<?php

namespace App\Http\Controllers;

use App\Events\PostMessageCreateEvent;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with('user')->take(30)->get();

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = auth()->user()->messages()->create($validated)->with('user')->first();

        event(new PostMessageCreateEvent($message));

        return response()->json([
            'message' => 'Message créé avec succès',
            'data' => $message
        ], 201);
    }

    public function show(Message $message)
    {
        return response()->json($message);
    }
}