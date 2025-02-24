<?php
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function register_user($db, $fullname, $email, $password, $user_type, $phone = '', $address = '') {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (fullname, email, password, user_type, phone, address) 
            VALUES (?, ?, ?, ?, ?, ?)";
            
    $stmt = $db->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssssss", $fullname, $email, $hashed_password, $user_type, $phone, $address);
        return $stmt->execute();
    }
    return false;
}

function login_user($db, $email, $password) {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $db->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
    }
    return false;
}

function isValidDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function isValidTime($time) {
    return preg_match('/^([0-1]?[0-9]|2[0-3]):([0-5][0-9])$/', $time);
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a strong random token
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token() {
    if (empty($_POST['csrf_token'])) {
        return false; // No token submitted
    }

    $submitted_token = $_POST['csrf_token'];
    $session_token = $_SESSION['csrf_token'] ?? ''; // Get token from session, default to empty string if not set

    if (hash_equals($session_token, $submitted_token)) { // Securely compare tokens
        unset($_SESSION['csrf_token']); // Optionally, consume the token after successful verification (single-use token) - for simplicity, let's not consume for now.
        return true; // Tokens match, verification successful
    } else {
        return false; // Tokens do not match, verification failed
    }
}

function generateAppointmentBillPDF($appointmentData) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'mm', 'A4', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Care Compass Hospitals');
    $pdf->SetTitle('Appointment Bill');
    $pdf->SetSubject('Appointment Bill');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // --- Bill Content ---

    $html = '<h1>Appointment Bill</h1>';
    $html .= '<p>Bill ID: ' . htmlspecialchars($appointmentData['id']) . '</p>';
    $html .= '<p>Date: ' . date('F j, Y') . '</p>'; // Bill generation date
    $html .= '<p>Patient: ' . htmlspecialchars($appointmentData['patient_name']) . '</p>';
    $html .= '<p>Doctor: ' . htmlspecialchars($appointmentData['doctor_name']) . '</p>';
    $html .= '<p>Appointment Date: ' . htmlspecialchars($appointmentData['appointment_date']) . '</p>';
    $html .= '<p>Appointment Time: ' . htmlspecialchars($appointmentData['appointment_time']) . '</p>';
    $html .= '<p>Service: Consultation</p>'; // You can make service dynamic later
    $html .= '<p>Price: $' . number_format($appointmentData['price'], 2) . '</p>'; // Format price

    // Add hospital details from config
    $html .= '<br><hr><br>';
    $html .= '<p><strong>' . HOSPITAL_NAME . '</strong></p>';
    $html .= '<p>' . HOSPITAL_ADDRESS . '</p>';
    $html .= '<p>Phone: ' . HOSPITAL_PHONE . '</p>';

    $pdf->writeHTML($html, true, false, true, false, '');

    // Output PDF to string (you can also save to file directly)
    $pdfContent = $pdf->Output('', 'S'); // 'S' for string output
    return $pdfContent;
}
?>