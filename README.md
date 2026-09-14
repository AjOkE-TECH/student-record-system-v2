# Student Record Management System

## Project Description

A web-based system for managing student records, courses and results

## Features

* Admin login, registration and secure logout
* Add, edit, view, search and archive students
* Automatic matriculation number generation (per department and admission year)
* Student profile pages with passport photo upload
* Course management: add, edit, search, filter by session, archive and restore
* Session management: create and manage academic sessions (e.g. `2025/2026`, `2026/2027`)
* Result entry with cascade selection of session and course
* Email notifications (SMTP via PHPMailer)

## Technologies Used

* PHP
* MySQL
* HTML / CSS / JavaScript

## Installation

1. Clone the repository
2. Move project to XAMPP htdocs
3. Create the `record_system` database and import the schema (the schema ships with seed data for departments and academic sessions)
4. Copy `config/mail_settings.example.php` to `config/mail_settings.php` and enter real SMTP credentials (required for email notifications)
5. Start Apache and MySQL
6. Open `localhost/students_record_system` in your browser

## Directory Structure

* `config/` - database connection, matric generation, mail settings
* `assets/` - CSS, JS, images and uploaded student passport photos
* Root-level PHP pages - application modules (students, courses, sessions, results)

## Author

Sekinat Mutolib