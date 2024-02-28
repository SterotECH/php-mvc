<?php
class AlterTable_20240228_002642_Notes {
    function up()
    {
        return "ALTER TABLE notes DROP COLUMN title";
    }

    function down($db): void
    {
        // Define down method to revert the alterations if needed
    }
}