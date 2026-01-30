# Assignment : 02

### Name : Md Abul Bashar  
### Email : hmbashar@gmail.com

---

## Project Description

A secure **User Authentication and Profile Management System** built using:
- **PHP OOP** (Object-Oriented Programming)
- **MySQL** with **PDO** (Prepared Statements)
- **HTML/CSS** with **Tailwind CSS**
- **PHP Sessions** for authentication

### Features
- User Registration with validation
- User Login with secure authentication
- Profile View and Update
- Password Change functionality
- Secure Logout
- SQL Injection Prevention
- Session-based access control

---

## Technology Stack

- **Backend:** PHP 7.4+ (OOP)
- **Database:** MySQL with PDO
- **Frontend:** HTML5, CSS3, Tailwind CSS
- **Security:** password_hash(), password_verify(), Prepared Statements

---

## Project Structure

```
assignment02/
├── config/
│   └── Database.php          # PDO database connection
├── classes/
│   ├── User.php              # User management
│   └── Auth.php              # Authentication
├── index.php                 # Entry point
├── register.php              # User registration
├── login.php                 # User login
├── profile.php               # User profile
├── logout.php                # Logout
└── database.sql              # Database schema
```

---

## Setup Instructions

### Step 1: Database Setup

1. **Create Database:**
   - Open phpMyAdmin or MySQL client
   - Import the `database.sql` file

   **OR manually run:**
   ```sql
   CREATE DATABASE assignment02_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE assignment02_db;
   
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       email VARCHAR(255) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       INDEX idx_email (email)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```

### Step 2: Database Configuration

Edit `config/Database.php` and update credentials if needed:

```php
private const DB_HOST = 'localhost';
private const DB_NAME = 'assignment02_db';
private const DB_USER = 'root';
private const DB_PASS = '';  // Your MySQL password
```

### Step 3: Access the Application

**Using Laravel Herd:**
```
http://assignment02.test/
```

**Using XAMPP/WAMP:**
```
http://localhost/assignment02/
```

**Using PHP Built-in Server:**
```bash
cd assignment02
php -S localhost:8000
```
Then open: `http://localhost:8000/`

---

## Usage

1. **Register:** Create a new account at `register.php`
2. **Login:** Sign in with your credentials at `login.php`
3. **Profile:** View and update your profile
4. **Logout:** Securely end your session

---

## Login Credentials (Test User)

To create a test user, run this SQL:

```sql
INSERT INTO users (name, email, password) VALUES 
('Test User', 'test@example.com', '$2y$10$c5/x1cXFWrFZ8jjywnfwvOXcZ0mPmJ7z5SV.T5r5PyiZCiYtHJqfa');
```

**Test Credentials:**
- Email: `test@example.com`
- Password: `password123`

---

## Security Features

- ✅ Password hashing with `password_hash()`
- ✅ Password verification with `password_verify()`
- ✅ PDO Prepared Statements (SQL Injection Prevention)
- ✅ Session-based Authentication
- ✅ Input Validation and Sanitization
- ✅ Access Control on Protected Pages

---

## Requirements Met

- ✅ PHP OOP (Classes, Constructors, Methods)
- ✅ MySQL with PDO (No mysqli)
- ✅ Prepared Statements for all queries
- ✅ password_hash() and password_verify()
- ✅ SQL Injection Prevention
- ✅ Session Management
- ✅ User Registration with Validation
- ✅ User Login with Authentication
- ✅ Profile Management (View & Update)
- ✅ Secure Logout
- ✅ Clean Folder Structure
- ✅ No Frameworks Used

---

## Author

**Md Abul Bashar**  
Email: hmbashar@gmail.com  
Assignment: 02 - Authentication & Profile Management
