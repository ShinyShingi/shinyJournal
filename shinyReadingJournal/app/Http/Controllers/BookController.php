<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        // Validate the incoming request data
        $validatedData = Validator::make($request->all(), [
            'title' => 'required|string',
            'author' => 'required|string',
            'series' => 'string|nullable',
            'cover' => 'nullable|string'
        ])->validate();

        // Handle file upload if a cover file is provided
        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $coverPath = $cover->store('covers', 'public');
            // Store the relative path for the cover image
            $validatedData['cover'] = Str::replaceFirst('public/', '/storage/', $coverPath);
        } // No need for an else block, if a cover URL is provided, it's already included in $validatedData

        // Check if the book already exists
        $existingBook = Book::where('title', $validatedData['title'])
            ->where('author', $validatedData['author'])
            ->first();

        if ($existingBook) {
            // If the book exists, attach it to the user's reading list
            $userId = auth()->id();
            $existingBook->users()->attach($userId);
            return response()->json(['message' => 'Book already exists, added to your reading list', 'book' => $existingBook]);
        } else {
            // If the book does not exist, create a new book entry
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
//            'read_at' =>'date|nullable',
//            'cover' => 'nullable|string'
        ]);

        if ($request->has('cover')) {
            $data['cover'] = $request->cover;
        }

        $book->update($data);

        Log::info('Updated book data with new cover:', $book->toArray());

        return response()->json(['data' => $book->fresh(), 'message' => 'Book updated successfully']);
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

    public function getPopularBooks()
    {
        $popularBooks = Book::withCount('users')
            ->orderBy('users_count', 'desc')
            ->take(10)
            ->get();

        return response()->json($popularBooks);
    }
    public function getHighestRatedBooks()
    {
        $highestRatedBooks = Book::with('users')
            ->get()
            ->sortByDesc(function ($book) {
                return $book->users->avg('pivot.rating');
            })
            ->take(10);

        return response()->json($highestRatedBooks);
    }



}
