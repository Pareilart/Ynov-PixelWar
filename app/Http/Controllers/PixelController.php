<?php

namespace App\Http\Controllers;

use App\Events\PostPixelCreateEvent;
use App\Events\PostPixelDeleteEvent;
use App\Models\Pixel;
use App\Models\User;
use Illuminate\Http\Request;

class PixelController extends Controller
{
    public function index()
    {
        $pixels = Pixel::all();
        return response()->json($pixels);
    }

    public function store(Request $request)
    {
        $user = User::find($request->user()->id);

        $validated = $request->validate([
            'color' => 'required|string',
            'x' => 'required|integer',
            'y' => 'required|integer',
        ]);

        $pixel = $user->pixels()->create($validated);
        event(new PostPixelCreateEvent($user, $pixel));

        return response()->json($pixel, 201);
    }

    public function show(Pixel $pixel)
    {
        return response()->json($pixel->load('user'));
    }

    public function update(Request $request, Pixel $pixel)
    {
        $validated = $request->validate([
            'color' => 'required|string',
            'x' => 'required|integer',
            'y' => 'required|integer',
        ]);

        $pixel->update($validated);
        return response()->json($pixel);
    }

    public function destroy(Request $request, Pixel $pixel)
    {
        if ($pixel->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $pixelId = $pixel->id;          
            event(new PostPixelDeleteEvent($request->user(), $pixelId));
            
            $pixel->delete();
            
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete pixel'], 500);
        }
    }
}
