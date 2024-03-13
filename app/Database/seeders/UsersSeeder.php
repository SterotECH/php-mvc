<?php
namespace App\Database\seeders;

class UsersSeeder
{
    public function run(): string
    {
        return "INSERT INTO users (username, first_name, last_name, other_name, phone_number, email, password)
        VALUES
            ('john_doe', 'John', 'Doe', 'M', '+1234567890', 'john.doe@example.com', 'password123'),
            ('jane_smith', 'Jane', 'Smith', '', '+1987654321', 'jane.smith@example.com', 'password456'),
            ('alice_johnson', 'Alice', 'Johnson', '', '+1122334455', 'alice.johnson@example.com', 'password789'),
            ('bob_davis', 'Bob', 'Davis', '', '+1555666777', 'bob.davis@example.com', 'passwordabc'),
            ('emma_wilson', 'Emma', 'Wilson', '', '+1444333222', 'emma.wilson@example.com', 'passworddef');
        ";
    }
}
