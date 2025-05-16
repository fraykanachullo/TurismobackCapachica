<?php

namespace App\Http\Controllers\Emprendedor;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company ? Auth::user()->company->id : null;

        return Message::where('company_id', $companyId)
                      ->with(['sender', 'receiver'])
                      ->latest()
                      ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $companyId = Auth::user()->company ? Auth::user()->company->id : null;

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'company_id' => $companyId,
        ]);

        return response()->json(['message' => 'Mensaje enviado', 'data' => $message], 201);
    }

    public function show($id)
    {
        $message = Message::with(['sender', 'receiver'])->findOrFail($id);

        if ($message->company_id !== (Auth::user()->company ? Auth::user()->company->id : null)) {
            abort(403);
        }
        return $message;
    }

    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        if ($message->company_id !== (Auth::user()->company ? Auth::user()->company->id : null)) abort(403);
        $message->update($request->only('message'));
        return response()->json(['message' => 'Mensaje actualizado', 'data' => $message]);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        if ($message->company_id !== (Auth::user()->company ? Auth::user()->company->id : null)) abort(403);
        $message->delete();
        return response()->noContent();
    }
}
