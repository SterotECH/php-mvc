<?php
    
class Migration_20240228_004524_Blog
{
    function up(): string
    {
         return "CREATE TABLE IF NOT EXISTS blog (
                    id SERIAL PRIMARY KEY,
                   -- add your table definition here
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    --  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
    }

    function down(): string
    {
        return "DROP TABLE blog";
    }
}