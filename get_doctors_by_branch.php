<?php
// get_doctors_by_branch.php

include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

$db = new Database();

// Get branch ID and search term from request (POST method is expected in JavaScript)
$branchId = $_POST['branch_id'] ?? null;
$searchTerm = $_POST['search_term'] ?? '';

// Validate branch ID (optional, but good practice)
if (empty($branchId) || !is_numeric($branchId)) {
    echo '<option value="">Please select a valid branch first</option>';
    exit;
}

// Fetch doctors based on branch and search term using Database class method
$doctors = $db->getDoctorsByBranchAndSearch($branchId, $searchTerm); // Call the Database class method

// Prepare HTML options for datalist
$options = '<option value="">-- Select Doctor --</option>';
if ($doctors) {
    foreach ($doctors as $doctor) {
        $options .= '<option value="' . htmlspecialchars($doctor['id']) . '">' . htmlspecialchars($doctor['fullname']) . ' - ' . htmlspecialchars($doctor['specialty']) . '</option>';
    }
} else {
    $options .= '<option value="">No doctors found</option>'; // Option if no doctors are found
}

// Output the options - IMPORTANT: Set Content-Type to HTML
header('Content-Type: text/html');
echo $options;
?>