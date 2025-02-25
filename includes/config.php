<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'care_compass_db');

// Application configuration
define('SITE_URL', 'http://localhost/care-compass');
define('SITE_NAME', 'Care Compass Connect');

// System Settings
define('HOSPITAL_NAME', 'Care Compass Hospital');
define('HOSPITAL_ADDRESS', '123 Main St, Anytown');
define('HOSPITAL_PHONE', '555-123-4567');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mailtrap SMTP Configuration (for testing)
define('MAILTRAP_SMTP_HOST', 'sandbox.smtp.mailtrap.io'); // Replace with your Mailtrap Host
define('MAILTRAP_SMTP_USERNAME', '2d4abdd2258e44'); // Replace with your Mailtrap Username
define('MAILTRAP_SMTP_PASSWORD', '8f8f7c45f62d90'); // Replace with your Mailtrap Password
define('MAILTRAP_SMTP_PORT', 587); // Replace with your Mailtrap Port
define('MAILTRAP_SMTP_ENCRYPTION', 'tls'); // Replace with your Mailtrap Encryption (tls or starttls)
?>