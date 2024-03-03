<?php

class NotesSeeder
{
    public function run(): string
    {

        return "
            INSERT INTO notes (id, body, user_id, created_at, updated_at) 
                VALUES ('Sample id 0', 'Sample body 0', 'Sample user_id 0', 'Sample created_at 0', 'Sample updated_at 0'), 
                       ('Sample id 1', 'Sample body 1', 'Sample user_id 1', 'Sample created_at 1', 'Sample updated_at 1'), 
                       ('Sample id 2', 'Sample body 2', 'Sample user_id 2', 'Sample created_at 2', 'Sample updated_at 2'), 
                       ('Sample id 3', 'Sample body 3', 'Sample user_id 3', 'Sample created_at 3', 'Sample updated_at 3'), 
                       ('Sample id 4', 'Sample body 4', 'Sample user_id 4', 'Sample created_at 4', 'Sample updated_at 4'), 
                       ('Sample id 5', 'Sample body 5', 'Sample user_id 5', 'Sample created_at 5', 'Sample updated_at 5'), 
                       ('Sample id 6', 'Sample body 6', 'Sample user_id 6', 'Sample created_at 6', 'Sample updated_at 6'), 
                       ('Sample id 7', 'Sample body 7', 'Sample user_id 7', 'Sample created_at 7', 'Sample updated_at 7'), 
                       ('Sample id 8', 'Sample body 8', 'Sample user_id 8', 'Sample created_at 8', 'Sample updated_at 8'), 
                       ('Sample id 9', 'Sample body 9', 'Sample user_id 9', 'Sample created_at 9', 'Sample updated_at 9')";
        
    }
}