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
        $response = Http::get("https://openlibrary.org/search.json?q=" . urlencode($searchTerm));

        if ($response->successful()) {
            $booksData = $response->json();
            $books = $booksData['docs'] ?? [];

            foreach ($books as &$book) {
                // Add cover image URL or default image if not available
                if (isset($book['cover_i'])) {
                    $book['cover_url'] = 'https://covers.openlibrary.org/b/id/' . $book['cover_i'] . '-L.jpg';
                } else {
                    // Set the default cover image URL here
                    $book['cover_url'] = Storage::url('covers/Default_image.jpg');
                }

                // ... additional data processing
            }

            return response()->json(['docs' => $books]);
        } else {
            return response()->json(['error' => 'Unable to fetch data from Open Library', 'statusCode' => $response->status()], 500);
        }
    }


}
