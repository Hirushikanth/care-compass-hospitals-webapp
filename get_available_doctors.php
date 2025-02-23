<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();

if (isset($_POST['branch_id'])) {
    $branchId = $_POST['branch_id'];

    // Get available doctors for the selected branch
    $availableDoctors = $db->getDoctorsByBranchId($branchId); // You need to add this method to db.php

    // Output available doctors as HTML options
    if (empty($availableDoctors)) {
        echo '<option value="">No doctors available in this branch</option>';
    } else {
        foreach ($availableDoctors as $doctor) {
            echo '<option value="' . $doctor['id'] . '">' . htmlspecialchars($doctor['fullname']) . ' ('. htmlspecialchars($doctor['specialty']) .')</option>';
        }
    }
} else {
    // If branch_id is not set, output an error option
    echo '<option value="">Error loading doctors.</option>';
}
?>