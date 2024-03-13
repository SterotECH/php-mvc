<?php

class NotesSeeder
{
    public function run(): string
    {
    return "INSERT INTO notes (title, slug, content, user_id)
    VALUES
        ('Note Title 1', 'note-title-1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1),
        ('Note Title 2', 'note-title-2', 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 2),
        ('Note Title 3', 'note-title-3', 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 3),
        ('Note Title 4', 'note-title-4', 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', 4),
        ('Note Title 5', 'note-title-5', 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 5);";
    }
}
