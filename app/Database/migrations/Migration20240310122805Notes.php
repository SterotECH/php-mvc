<?php

class Migration20240310122805Notes
{
  public function up(): string
  {
    return "CREATE TABLE IF NOT EXISTS notes (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `title` VARCHAR(255) NULL,
      `slug` VARCHAR(255) NOT NULL,
      `content` TEXT NOT NULL,
      `user_id` INT NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      CONSTRAINT `fk_notes_users`
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
    )";
  }

  public function down(): string
  {
      return "DROP TABLE notes";
  }
}
