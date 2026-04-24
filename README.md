Student Accommodation Manager is a web-based CRUD application designed to help students search for, view and manage accommodation bookings. 
The system includes user authentication, role-based access, and session management to ensure secure access to user accounts and booking features.

Main Features -
User System
Account Management
Booking System
Create, read, update, and delete (CRUD) booking functionality
Users can manage their accommodation bookings after logging in
Search Functionality
Search available accommodation listings

Stack - 
PHP 
phpMyAdmin
HTML
CSS
XAMPP 

Security Features -
Password hashing for secure storage
password_verify() is used during login to validate credentials.
Sessions are used to maintain login state across pages.
Users are redirected to the login page if they attempt to access restricted pages without authentication.
Prepared statements to prevent SQL injection
Session validation for protected pages

Developed as part of a  CRUD assignment for Web Development module
