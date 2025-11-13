<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;

class LessonController extends Controller
{
    public function index()
    {
        return response()->json(Lesson::with('uploader')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:2048',
        ]);

        $path = $request->file('file') ? $request->file('file')->store('lessons', 'public') : null;

        $lesson = Lesson::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json($lesson, 201);
    }

    public function update(Request $request, $id)
{
    $lesson = Lesson::findOrFail($id);

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'file' => 'nullable|file|max:2048',
    ]);

    if ($request->hasFile('file')) {
        if ($lesson->file_path) {
            \Storage::disk('public')->delete($lesson->file_path);
        }
        $lesson->file_path = $request->file('file')->store('lessons', 'public');
    }

    $lesson->title = $request->title;
    $lesson->description = $request->description;
    $lesson->save();

    return response()->json([
        'success' => true,
        'message' => 'Lesson updated successfully!',
        'lesson' => $lesson
    ]);
}


public function destroy($id)
{
    $lesson = Lesson::findOrFail($id);

    // Delete file from storage
    if ($lesson->file_path) {
        \Storage::disk('public')->delete($lesson->file_path);
    }

    $lesson->delete();

    return response()->json(['message' => 'Lesson deleted successfully.']);
}

}
