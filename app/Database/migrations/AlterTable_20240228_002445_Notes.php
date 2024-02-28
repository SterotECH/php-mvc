<?php
class AlterTable_20240228_002445_Notes {
    function up()
    {
        return "ALTER TABLE notes ADD COLUMN title VARCHAR(100) NOT NULL". ';';
    }

    function down($db): void
    {
        // Define down method to revert the alterations if needed
    }
}