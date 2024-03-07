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
        $url = env('APP_URL') . '/storage/app/public/' . $path;
        return response()->json(['url' => $url]);

//        return response()->json(['url' => Storage::url($path)]);
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

        $existingBook = Book::where('title', $validatedData['title'])
            ->where('author', $validatedData['author'])
            ->first();

        if ($existingBook) {
            // Book exists, add it to the user's reading list instead
            $userId = auth()->id();
            $existingBook->users()->attach($userId);
            return response()->json(['message' => 'Book already exists, added to your reading list', 'book' => $existingBook]);
        } else {
            // Book does not exist, create a new book
            $book = Book::create($validatedData);
            $userId = auth()->id();
            $book->users()->attach($userId);
            return response()->json(['message' => 'New book created and added to your reading list', 'book' => $book]);
        }


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
//            'read_at' =>'date|nullable'
            //'cover' => 'nullable|sometimes|image|mimes:jpeg,bmp,png,jpg,svg|max:2000',
        ]);

        if ($request->hasFile('cover')) {
            // Delete the old cover image if it exists
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }

            $cover = $request->file('cover');
            $coverPath = $cover->store('covers', 'public');

            $data['cover'] = $coverPath;
        }

        Log::info('Received update data:', $request->all());

        $book->update($data);
        Log::info('Book data after update:', $book->toArray());

        $bookData = $book->fresh(); // Reload the updated book from the database
        Log::info('Refreshed book data:', $book->toArray());


        return response()->json(['data' => $bookData, 'message' => 'Book updated successfully']);
    }


    public function rate(Request $request, $id)
    {

        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $rating = $request->input('rating');

        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found.'], 404);
        }

        Log::info('Received update data:', ['rating' => $rating]);

        // Assuming you have an authenticated user
        $user = auth()->user();
        Log::info('Book ID:', ['id' => $book->id]);


        if (!$user) {
            return response()->json(['message' => 'Unauthorized access.'], 401);
        }

        try {
            $updateSuccessful = $user->books()->updateExistingPivot($book->id, ['rating' => $rating]);

            if ($updateSuccessful) {
                Log::info("Rating updated successfully for book ID: {$book->id}");
                return response()->json(['message' => 'Rating updated successfully.']);
            } else {
                Log::info("Failed to update rating for book ID: {$book->id}");
                return response()->json(['message' => 'Failed to update rating.'], 500);
            }
        } catch (\Exception $e) {
            Log::error("Error updating rating for book ID: {$book->id}. Error: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the rating.'], 500);
        }
    }

    public function updateStatus(Request $request, string $id)
    {
        $user = auth()->user(); // Get the currently authenticated user
//        $book = Book::find($id);
        $book = $user->books()->findOrFail($id);

        if ($book && $user) {
            $newStatus = $request->input('status');

            // Update the book's status in the pivot table

            $result = $user->books()->updateExistingPivot($book->id, ['status' => $newStatus, 'read_at'=> now()]);
//            dd($result);

//            $book->refresh();
            $book = $user->books()->findOrFail($id);


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
