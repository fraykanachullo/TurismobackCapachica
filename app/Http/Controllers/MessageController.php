<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List all messages in which the authenticated user participates.
     */
    public function index()
    {
        $user = auth()->user();

        $query = Message::query()
            ->where('sender_id',   $user->id)
            ->orWhere('receiver_id', $user->id);

        // If the user is an emprendedor, restrict to their company
        if ($user->company_id) {
            $query->where('company_id', $user->company_id);
        }

        $mensajes = $query->orderBy('created_at', 'desc')->get();

        return response()->json($mensajes, 200);
    }

    /**
     * Store a new message from the authenticated user to another user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id|not_in:' . auth()->id(),
            'message'     => 'required|string',
        ]);

        $sender   = auth()->user();
        $receiver = User::findOrFail($data['receiver_id']);

        // Determine company_id: prefer sender's company, otherwise receiver's
        $companyId = $sender->company_id
            ? $sender->company_id
            : $receiver->company_id;

        $mensaje = Message::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'company_id'  => $companyId,
            'message'     => $data['message'],
            'read'        => false,
        ]);

        return response()->json($mensaje, 201);
    }

    /**
     * Show a specific message if the user is sender or receiver.
     */
    public function show(Message $message)
    {
        $user = auth()->user();

        if ($message->sender_id !== $user->id
            && $message->receiver_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json($message, 200);
    }

    /**
     * Update a message (only sender can edit).
     */
    public function update(Request $request, Message $message)
    {
        $user = auth()->user();

        if ($message->sender_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'message' => 'required|string',
        ]);

        $message->update($data);

        return response()->json($message, 200);
    }

    /**
     * Delete a message (only sender can delete).
     */
    public function destroy(Message $message)
    {
        $user = auth()->user();

        if ($message->sender_id !== $user->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $message->delete();

        return response()->json(null, 204);
    }
}
