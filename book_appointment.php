<?php
// Include necessary files
include('includes/config.php');
include('includes/db.php');
include('includes/functions.php');

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a patient or staff
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'patient' && $_SESSION['user_role'] != 'staff')) {
    header("Location: login.php"); // Redirect to login page if not logged in or not a patient/staff
    exit;
}

$db = new Database();

// Handle form submission
if (isset($_POST['book_appointment'])) {
    $doctorId = $_POST['doctor_id'];
    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];
    $reason = sanitize_input($_POST['reason']);
    $patientId = $_SESSION['user_id']; // Default to logged-in user

    // If staff is booking, they might be booking for another patient.
    if ($_SESSION['user_role'] == 'staff' && isset($_POST['patient_id_for_booking']) && !empty($_POST['patient_id_for_booking'])) {
        $patientId = $_POST['patient_id_for_booking'];
    }

    // Server-side validation
    $errors = [];

    if (empty($doctorId) || empty($appointmentDate) || empty($appointmentTime)) {
        $errors[] = "Please fill in all required fields.";
    }

    // Check if date and time are in the future
    $appointmentDateTime = new DateTime($appointmentDate . ' ' . $appointmentTime);
    $now = new DateTime();

    if ($appointmentDateTime < $now) {
        $errors[] = "Please select a future date and time.";
    }

    // Check if the time slot is valid (HH:MM format)
    if (!preg_match("/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/", $appointmentTime)) {
        $errors[] = "Invalid time format.";
    }

    // If no errors, proceed with booking
    if (empty($errors)) {
        // Check if the selected time slot is still available
        $bookedSlots = $db->getBookedTimeSlots($doctorId, $appointmentDate);
        if (in_array($appointmentTime, $bookedSlots)) {
            $errors[] = "Sorry, that time slot is no longer available.";
        } else {
            // Insert the appointment into the database
            $success = $db->createAppointment($patientId, $doctorId, $appointmentDate, $appointmentTime, $reason);

            if ($success) {
                $_SESSION['booking_success'] = true;
                header("Location: patient/dashboard.php"); // Redirect to patient dashboard after success
                exit;
            } else {
                $_SESSION['booking_error'] = true;
                $_SESSION['error_message'] = "Error booking appointment. Please try again.";
            }
        }
    }

    // Set the error message in session if there are errors
    if (!empty($errors)) {
        $_SESSION['booking_error'] = true;
        $_SESSION['error_message'] = implode("<br>", $errors); // Combine errors into a single string
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Care Compass Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff; /* Light background from the palette */
            font-family: 'Nunito', sans-serif;
            color: #343a40;
        }
        .booking-header {
            background-color: #046A7A; /* Dark teal from the palette */
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }
        .booking-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        .booking-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            overflow: hidden;
            max-width: 700px;
            margin: auto;
        }
        .booking-card:hover {
            transform: scale(1.01);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.07);
        }
        .booking-card-header {
            background-color: #046A7A;
            color: white;
            padding: 1.25rem;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .booking-card-header h3 {
            font-size: 1.5rem;
            margin-bottom: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 0.3rem;
        }
        .btn-primary {
            background-color: #046A7A;
            border-color: #046A7A;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #034e5a;
            border-color: #034e5a;
            box-shadow: 0 0 0 0.2rem rgba(4, 106, 122, 0.5);
        }
        .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
            border-radius: 0.3rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
            border-radius: 0.3rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }
        .form-control:disabled{
            background-color: #ffffff;
        }
    </style>
</head>
<body>
    <header class="booking-header">
        <div class="container">
            <h2>Book an Appointment</h2>
        </div>
    </header>

    <div class="container">
        <div class="booking-card">
            <div class="booking-card-header">
                <h3><i class="bi bi-calendar-plus me-2"></i> Schedule Your Appointment</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['booking_success']) && $_SESSION['booking_success']): ?>
                    <div class="alert alert-success" role="alert">
                        Appointment booked successfully! Redirecting to your dashboard...
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = 'patient/dashboard.php'; // Redirect after 2 seconds
                        }, 2000);
                    </script>
                <?php endif; ?>
                <?php if (isset($_SESSION['booking_error']) && $_SESSION['booking_error']): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['error_message']; ?>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <?php if ($_SESSION['user_role'] == 'staff'): ?>
                        <div class="mb-3">
                            <label for="patient_id_for_booking" class="form-label">Select Patient:</label>
                            <select class="form-control" id="patient_id_for_booking" name="patient_id_for_booking" required>
                                <option value="">-- Select Patient --</option>
                                <?php
                                $patients = $db->getAllPatients();
                                foreach ($patients as $patient):
                                ?>
                                    <option value="<?= $patient['id'] ?>"><?= htmlspecialchars($patient['fullname']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="doctor" class="form-label">Select Doctor:</label>
                        <select class="form-control" id="doctor" name="doctor_id" required>
                            <option value="">-- Select Doctor --</option>
                            <?php
                            // Fetch all doctors from the database
                            $doctors = $db->getAllDoctors();
                            foreach ($doctors as $doctor):
                            ?>
                                <option value="<?= $doctor['id'] ?>"><?= htmlspecialchars($doctor['fullname']) ?> (<?= htmlspecialchars($doctor['specialty']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Select Date:</label>
                        <input type="date" class="form-control" id="date" name="appointment_date" required min="<?= date('Y-m-d'); ?>">
                        <div id="date-error" class="text-danger"></div>
                    </div>

                    <div class="mb-3">
                        <label for="time" class="form-label">Select Time Slot:</label>
                        <select class="form-control" id="time" name="appointment_time" required disabled>
                            <option value="">-- Select Time --</option>
                            <!-- Time slots will be loaded here based on the selected doctor and date -->
                        </select>
                        <div class="mt-2" id="loading-indicator" style="display: none;">Loading...</div>
                        <div id="time-error" class="text-danger"></div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Appointment:</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="book_appointment" class="btn btn-primary">Book Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const doctorSelect = document.getElementById('doctor');
        const dateSelect = document.getElementById('date');
        const timeSelect = document.getElementById('time');
        const loadingIndicator = document.getElementById('loading-indicator');

        // Disable time select initially
        timeSelect.disabled = true;

        doctorSelect.addEventListener('change', loadAvailableTimeSlots);
        dateSelect.addEventListener('change', loadAvailableTimeSlots);

        function loadAvailableTimeSlots() {
            const doctorId = doctorSelect.value;
            const selectedDate = dateSelect.value;

            // Reset and disable time select if doctor or date is not selected
            if (!doctorId || !selectedDate) {
                timeSelect.innerHTML = '<option value="">-- Select Time --</option>';
                timeSelect.disabled = true;
                return;
            }

            // Show loading indicator
            timeSelect.style.display = 'none';
            loadingIndicator.style.display = 'block';

            // Fetch available time slots using AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_available_time_slots.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                loadingIndicator.style.display = 'none';
                timeSelect.style.display = 'block';

                if (xhr.status >= 200 && xhr.status < 300) {
                    timeSelect.innerHTML = xhr.responseText;
                    timeSelect.disabled = false; // Enable time select
                } else {
                    console.error('Request failed. Returned status of ' + xhr.status);
                    timeSelect.innerHTML = '<option value="">Error loading time slots.</option>';
                }
            };
            xhr.onerror = function() {
                console.error("Request failed");
                timeSelect.innerHTML = '<option value="">Error loading time slots.</option>';
            };
            xhr.send('doctor_id=' + doctorId + '&date=' + selectedDate);
        }

        dateSelect.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time to midnight for comparison

            if (selectedDate < today) {
                document.getElementById('date-error').innerText = 'Please select a future date.';
                this.value = ''; // Clear the invalid date
                timeSelect.innerHTML = '<option value="">-- Select Time --</option>'; // Reset time slots
                timeSelect.disabled = true; // Keep time select disabled
            } else {
                document.getElementById('date-error').innerText = '';
                loadAvailableTimeSlots(); // Load time slots if the date is valid
            }
        });

        timeSelect.addEventListener('change', function() {
            const selectedTime = this.value;
            const selectedDateTime = new Date(dateSelect.value + ' ' + selectedTime);
            const now = new Date();

            if (selectedDateTime < now) {
                document.getElementById('time-error').innerText = 'Please select a future time.';
                this.value = ''; // Clear the invalid time
            } else {
                document.getElementById('time-error').innerText = '';
            }
        });
    </script>
</body>
</html>