<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class BookApiController extends Controller
{
    public function index()
    {
        // dd(auth()->check());


        $books = Book::all();
        $completedBooks = Book::where('status', 'read')->get();
        $incompleteBooks = Book::where('status', 'unread')->get();
        $inProgressBooks = Book::where('status', 'reading')->get();


        return response()->json([
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
        $response = Http::get("https://openlibrary.org/search.json?q=" . urlencode($searchTerm));

        if ($response->successful()) {
            $booksData = $response->json();
            $books = $booksData['docs'] ?? []; // Fallback to an empty array if 'docs' is not set

            foreach ($books as &$book) {
                // Add cover image URL
                if (isset($book['cover_i'])) {
                    $book['cover_url'] = 'https://covers.openlibrary.org/b/id/' . $book['cover_i'] . '-L.jpg';
                } else {
                    $book['cover_url'] = null; // Handle books without cover images
                }

                // Placeholder for additional data processing (e.g., series, genres)
                // ...
            }

            return response()->json(['docs' => $books]); // Ensure the structure matches your Vue component's expectation
        } else {
            return response()->json(['error' => 'Unable to fetch data from Open Library', 'statusCode' => $response->status()], 500);
        }
    }

}
