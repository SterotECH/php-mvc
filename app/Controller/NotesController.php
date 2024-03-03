<?php

namespace App\Controller;

class NotesController extends Controller
{
    public function index(): void
    {
        $notes = [];

        $this->render('notes.index', ['notes' => $notes]);
    }

    public function create(): void
    {
        $this->render('notes/create', [], 'base');
    }

    public function store()
    {
        // Validate and store the new note in the database
        // Redirect back to the index page
    }

    public function edit($id): void
    {
        // Fetch the note with the given ID from the database
        $note = null; // Fetch the note from the database, e.g., using a model

        // Render the view for editing the note
        $this->render('notes.edit', ['note' => $note], 'base');
    }

    public function update($id)
    {
        // Validate and update the note in the database
        // Redirect back to the index page
    }

    public function destroy($id)
    {
        // Delete the note with the given ID from the database
        // Redirect back to the index page
    }
}
