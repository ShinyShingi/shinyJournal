<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BookApiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $completedBooks = $user->books()->wherePivot('status', 'read')->get();
        $incompleteBooks = $user->books()->wherePivot('status', 'unread')->get();
        $inProgressBooks = $user->books()->wherePivot('status', 'reading')->get();

        return view('start', [
            'completedBooks' => $completedBooks,
            'incompleteBooks' => $incompleteBooks,
            'inProgressBooks' => $inProgressBooks
        ]);
    }

    public function store(Request $request) {
        $book = Book::create($request->all());
    }
    public function show( $id)
    {
        $book = Book::findOrFail($id);
        return $book;
    }


    public function openlibrary(Request $request) {
        $searchTerm = $request->input('query', '');

        // Search in the local database
        $localBooks = Book::where('title', 'like', "%{$searchTerm}%")
            ->orWhere('author', 'like', "%{$searchTerm}%")
            ->get();

        // Initialize an array to hold all the books
        $allBooks = $localBooks->toArray();

        // If fewer than 5 books are found in local database, search in Open Library
        if ($localBooks->count() < 5) {
            $response = Http::get("https://openlibrary.org/search.json?q=" . urlencode($searchTerm));

            if ($response->successful()) {
                $booksData = $response->json();
                $openLibraryBooks = $booksData['docs'] ?? [];

                foreach ($openLibraryBooks as &$book) {
                    // Process book data from Open Library
                    $book['cover_url'] = isset($book['cover_i'])
                        ? 'https://covers.openlibrary.org/b/id/' . $book['cover_i'] . '-L.jpg'
                        : Storage::url('covers/Default_image.jpg');
                }

                // Add Open Library books to the array of all books
                $allBooks = array_merge($allBooks, $openLibraryBooks);
            } else {
                return response()->json(['error' => 'Unable to fetch data from Open Library', 'statusCode' => $response->status()], 500);
            }
        }

        return response()->json(['docs' => $allBooks]);
    }




}
