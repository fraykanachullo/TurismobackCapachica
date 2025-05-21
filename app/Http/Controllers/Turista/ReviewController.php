<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        return Review::where('user_id', Auth::id())->with('service')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'service_id' => $request->service_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'published_at' => now(),
        ]);

        return response()->json(['message' => 'Reseña enviada', 'review' => $review], 201);
    }

    public function show($id)
    {
        $review = Review::with('service')->findOrFail($id);
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }
        return $review;
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }
        $review->update($request->only('rating', 'comment'));
        return response()->json(['message' => 'Reseña actualizada', 'review' => $review]);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }
        $review->delete();
        return response()->noContent();
    }
}
