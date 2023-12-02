<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $completedBooks = Book::where('status', 'read')->get();
        $incompleteBooks = Book::where('status', 'unread')->get();
        $inProgressBooks = Book::where('status', 'reading')->get();


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
//            'cover' => 'string|nullable',
        ]);

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $coverPath = $cover->store('covers', 'public');  // Store the uploaded file

            $validatedData['cover'] = $coverPath;
        }

        $book = Book::create($validatedData);

        return response()->json(['data' => $book, 'message' => 'Book created successfully']);
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
            'cover' => 'string|nullable',
        ]);

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $coverPath = $cover->store('covers', 'public');  // Store the uploaded file

            $data['cover'] = $coverPath;
        }

        $book->update($data);

        return response()->json(['data' => $book, 'message' => 'Book updated successfully']);
    }


    public function updateStatus(Request $request, string $id)
    {

        $existingBook = Book::find($id);
        if ($existingBook) {
            $newStatus = $request->input('status');

            // Update the book's status in the database
            $existingBook->status = $newStatus;


            $existingBook->save();

            return response()->json(['success' => true]);
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
