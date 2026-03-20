# Attendance Management System (PHP + MySQL)

A simple DBMS mini project to manage students and daily attendance using HTML, CSS, PHP, and MySQL.

## Features

- Add student
- Update student details
- Delete student
- Search students
- Mark daily attendance (Present/Absent)
- Generate attendance report

## Tech Stack

- Frontend: HTML, CSS
- Backend: PHP (mysqli)
- DBMS: MySQL

## Setup Steps

1. Start Apache and MySQL in XAMPP/WAMP.
2. Create a database by running `database.sql` in phpMyAdmin or MySQL CLI.
3. Place this project folder inside your server root (`htdocs` for XAMPP).
4. Update DB credentials in `config.php` if needed.
5. Open: `http://localhost/attendance-management/index.php`

## SQL Schema

The SQL schema is in `database.sql`.

### Primary Keys

- `students.student_id`
- `subjects.subject_id`
- `attendance.attendance_id`

### Foreign Keys

- `attendance.student_id` references `students.student_id`
- `attendance.subject_id` references `subjects.subject_id`

## Relationships

- `students -> attendance` : 1:M
  - One student can have many attendance records.
- `subjects -> attendance` : 1:M
  - One subject can have many attendance records.
- `students <-> subjects` : M:N (implemented through `attendance`)
  - Many students attend many subjects.

## ER Diagram

```mermaid
erDiagram
    STUDENTS ||--o{ ATTENDANCE : has
    SUBJECTS ||--o{ ATTENDANCE : contains

    STUDENTS {
        int student_id PK
        string name
        string usn UNIQUE
        string department
        int semester
    }

    SUBJECTS {
        int subject_id PK
        string subject_name
        string faculty_name
    }

    ATTENDANCE {
        int attendance_id PK
        int student_id FK
        int subject_id FK
        date date
        enum status
    }
```

## Normalization (Up to 3NF)

- 1NF:
  - All columns contain atomic values.
  - No repeating groups.
- 2NF:
  - Each non-key attribute fully depends on the primary key.
  - Attendance attributes depend on the attendance record key.
- 3NF:
  - No transitive dependencies.
  - Student details are only in `students`; subject details only in `subjects`; attendance facts only in `attendance`.

This schema is normalized up to Third Normal Form (3NF).
