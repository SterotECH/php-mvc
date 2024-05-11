<?php

class Migration20240310122800Users
{
  public function up(): string
  {
    return "CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(16) NOT NULL UNIQUE,
      first_name VARCHAR(100) NOT NULL,
      last_name VARCHAR(100) NOT NULL,
      other_name VARCHAR(100) NOT NULL,
      phone_number VARCHAR(20) NOT NULL UNIQUE,
      email VARCHAR(100) NOT NULL UNIQUE,
      gender ENUM('male', 'female', 'other') NOT NULL,
      date_of_birth DATE NOT NULL,
      address VARCHAR(255) NOT NULL NOT NULL,
      profile_picture VARCHAR(255) DEFAULT '/images/users/default/default_profile_picture.jpg',
      password VARCHAR(255) NOT NULL,
      role ENUM('admin', 'customer', 'barber') NOT NULL DEFAULT 'customer',
      is_active BOOLEAN NOT NULL DEFAULT TRUE,
      is_superuser BOOLEAN NOT NULL DEFAULT FALSE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      CHECK (role IN ('admin', 'customer', 'barber')),
      CHECK (gender IN ('male', 'female', 'other')),
      CHECK (is_active IN (0, 1)),
      CHECK (is_superuser IN (0, 1)),
      CONSTRAINT users_username_unique UNIQUE INDEX (username),
      CONSTRAINT users_phone_number_unique UNIQUE INDEX (phone_number),
      CONSTRAINT users_email_unique UNIQUE INDEX (email)
    )";
  }

  public function down(): string
  {
      return "DROP TABLE users";
  }
}
