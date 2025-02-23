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

if (isset($_POST['doctor_id']) && isset($_POST['date'])) {
    $doctorId = $_POST['doctor_id'];
    $selectedDate = $_POST['date'];

    // Get booked time slots for the selected doctor and date
    $bookedSlots = $db->getBookedTimeSlots($doctorId, $selectedDate);

    // Generate available time slots (e.g., from 9 AM to 5 PM with 30-minute intervals)
    $start = strtotime('09:00');
    $end = strtotime('22:00');
    $interval = 900; // 15 minutes in seconds

    $availableSlots = [];
    for ($time = $start; $time < $end; $time += $interval) {
        $slot = date('H:i', $time);
        if (!in_array($slot, $bookedSlots)) {
            $availableSlots[] = $slot;
        }
    }

    // Output available time slots as HTML options
    if (empty($availableSlots)) {
        echo '<option value="">No slots available</option>';
    } else {
        foreach ($availableSlots as $slot) {
            echo '<option value="' . $slot . '">' . $slot . '</option>';
        }
    }
}
?>