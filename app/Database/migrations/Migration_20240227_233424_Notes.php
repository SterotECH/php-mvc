<?php
    
class Migration_20240227_233424_Notes
{
    function up(): string
    {
        return "CREATE TABLE IF NOT EXISTS notes (
                    id INT AUTO_INCREMENT,
                    body TEXT ,
                    user_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    CONSTRAINT fk_notes_user
                    FOREIGN KEY (user_id)
                                 REFERENCES users(id)
                                 ON DELETE CASCADE 
                                 ON UPDATE CASCADE 
                )";

    }

    function down(): string
    {
        return "DROP TABLE notes";
    }
}