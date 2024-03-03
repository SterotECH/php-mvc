<?php
class AlterTable_20240301_060253_Users {
    function up(): string
    {
        return "ALTER TABLE users MODIFY COLUMN id INT AUTO_INCREMENT";
    }

    function down(): string
    {
        return "ALTER TABLE users MODIFY COLUMN id INT NOT NULL";
    }
}