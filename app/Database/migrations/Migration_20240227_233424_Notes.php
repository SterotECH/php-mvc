<?php
    
class Migration_20240227_233424_Notes
{
    function up(): string
    {
         return "CREATE TABLE IF NOT EXISTS notes (
                    id SERIAL PRIMARY KEY,
                    body TEXT ,
                    user_id INTEGER NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    CONSTRAINT fk_user_notes FOREIGN KEY (user_id) REFERENCES users(id)
                )";
    }

    function down(): string
    {
        return "DROP TABLE notes";
    }
}