-- Base de données CRL avec système d'authentification
CREATE DATABASE IF NOT EXISTS crl_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crl_db;

-- Table users pour admins
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender VARCHAR(30) NOT NULL,
    birth_date DATE NOT NULL,
    birth_place VARCHAR(255) NOT NULL,
    birth_certificate_number VARCHAR(100) NOT NULL,
    previous_school VARCHAR(255) DEFAULT NULL,
    nationality VARCHAR(100) NOT NULL,
    class_id INT NOT NULL,
    enrollment_type VARCHAR(50) NOT NULL,
    guardian_name VARCHAR(255) NOT NULL,
    guardian_relationship VARCHAR(100) NOT NULL,
    guardian_phone VARCHAR(50) NOT NULL,
    guardian_email VARCHAR(255) NOT NULL,
    guardian_address TEXT NOT NULL,
    guardian_profession VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    level VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender VARCHAR(50) NOT NULL,
    birth_date DATE NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    class_id INT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
