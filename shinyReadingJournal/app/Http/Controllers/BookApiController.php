<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookApiController extends Controller
{
    public function index()
    {
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
}
