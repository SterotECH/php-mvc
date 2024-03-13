<?php

class Migration20240310122800Users
{
  public function up(): string
  {
    return "CREATE TABLE IF NOT EXISTS users (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `username` VARCHAR(100) NOT NULL,
      `first_name` VARCHAR(100) NOT NULL,
      `last_name` VARCHAR(100) NOT NULL,
      `other_name` VARCHAR(100) NOT NULL,
      `phone_number` VARCHAR(100) NOT NULL,
      `email` VARCHAR(100) NOT NULL,
      `password` VARCHAR(100) NOT NULL,
      `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
      `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
      `is_superuser` BOOLEAN NOT NULL DEFAULT FALSE,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      CHECK (role IN ('admin', 'user'))
    )";
  }

  public function down(): string
  {
      return "DROP TABLE users";
  }
}
