<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function uploadImage(Request $request)
    {
//        Log::info('All Request Data:', $request->all());
//        Log::info('File from request file method:', $request->file('image'));
//        Log::info('File from request input method:', $request->input('image'));
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']);

        $path = $request->file('image')->store('covers', 'public');
        return response()->json(['url' => Storage::url($path)]);
    }



    public function index()
    {
        $user = auth()->user(); // Get the currently authenticated user

        $completedBooks = $user->books()->wherePivot('status', 'Read')->get();
        $incompleteBooks = $user->books()->wherePivot('status', 'Unread')->get();
        $inProgressBooks = $user->books()->wherePivot('status', 'Reading')->get();

        return view('start', [
            'completedBooks' => $completedBooks,
            'incompleteBooks' => $incompleteBooks,
            'inProgressBooks' => $inProgressBooks
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view(('create'));
    }

    /**
     * Store a newly created resource in storage.
     */


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function getBook($id) {
        $book = Book::find($id);
        if($book) {
            return response()->json($book);
        }
        return response()->json(['success' => false, 'message' => 'Book not found']);
    }

    public function store(Request $request)
    {
       // dd($request->all());

        $validatedData = $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'series' => 'string|nullable',
//            'cover' => 'nullable|sometimes|image|mimes:jpeg,bmp,png,jpg,svg|max:2000'
            'cover' => 'nullable|string'

        ]);

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $coverPath = $cover->store('covers', 'public');  // Store the uploaded file

            $validatedData['cover'] = $coverPath;
        }

        $book = Book::create($validatedData);

        $userId = auth()->id(); // Get the ID of the authenticated user
        if ($userId) {
            $book->users()->attach($userId);
        }

        Log::info('Book created:', ['book' => $book->toArray()]);
        Log::info('Attaching book to user ID:', ['userId' => $userId]);

        return response()->json(['book' => $book, 'isNew' => true, 'message' => 'Book created successfully']);


    }


    public function updateBook(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found'], 404);
        }

        $data = $request->validate([
            'title' => 'string',
            'author' => 'string',
            'series' => 'string|nullable',
            //'cover' => 'nullable|sometimes|image|mimes:jpeg,bmp,png,jpg,svg|max:2000',
        ]);

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $coverPath = $cover->store('covers', 'public');  // Store the uploaded file

            $data['cover'] = $coverPath;
        }
        Log::info('Received update data:', $request->all());

        $book->update($data);
        Log::info('Book data after update:', $book->toArray());

        $bookData = $book->fresh(); // Reload the updated book from the database
        Log::info('Refreshed book data:', $book->toArray());


        return response()->json(['data' => $bookData, 'message' => 'Book updated successfully']);
    }



    public function updateStatus(Request $request, string $id)
    {
        $user = auth()->user(); // Get the currently authenticated user
        $book = Book::find($id);

        if ($book && $user) {
            $newStatus = $request->input('status');

            // Update the book's status in the pivot table
            $user->books()->updateExistingPivot($book->id, ['status' => $newStatus]);

            return response()->json(['success' => true, "book" => $book]);
        }

        return response()->json(['success' => false, 'message' => 'No book found']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // log the exception message
            Log::error('An error occurred while deleting the book: ' . $e->getMessage());
            return response()->json(['error' => 'Some error occurred'], 500);
        }
    }
}
