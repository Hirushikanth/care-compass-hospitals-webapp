<?php
require_once 'config.php';

class Database
{
    private $connection;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
        } catch (Exception $e) {
            // Log the error to a file or error logging system
            error_log("Database connection failed: " . $e->getMessage());

            // Display a user-friendly error message or redirect to an error page
            echo "Oops! Something went wrong. Please try again later.";
            exit; // Stop script execution
        }
    }

    public function prepare($sql)
    {
        return $this->connection->prepare($sql);
    }

    public function query($sql)
    {
        return $this->connection->query($sql);
    }

    public function escape($value)
    {
        return $this->connection->real_escape_string($value);
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getPatientById($userId)
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'patient'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getStaffById($userId)
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'staff'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAdminById($userId)
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'admin'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getUpcomingAppointmentsByPatientId($patientId)
    {
        $stmt = $this->connection->prepare("
            SELECT a.*, u.fullname as doctor_name 
            FROM appointments a
            INNER JOIN doctors d ON a.doctor_id = d.id
            INNER JOIN users u ON d.user_id = u.id
            WHERE a.patient_id = ? AND a.appointment_date >= CURDATE()
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMedicalRecordsByPatientId($patientId)
    {
        $stmt = $this->connection->prepare("
            SELECT mr.*, u.fullname as doctor_name
            FROM medical_records mr
            INNER JOIN users u ON mr.doctor_id = u.id
            WHERE mr.patient_id = ?
            ORDER BY mr.visit_date DESC
        ");
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchPatients($searchQuery)
    {
        $searchQuery = "%" . $searchQuery . "%";
        $stmt = $this->connection->prepare("
            SELECT id, fullname, email, phone, address
            FROM users
            WHERE user_type = 'patient' AND (fullname LIKE ? OR id LIKE ?)
        ");
        $stmt->bind_param("ss", $searchQuery, $searchQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllDoctors()
    {
        $stmt = $this->connection->prepare("SELECT d.id, u.fullname, d.specialty FROM doctors d INNER JOIN users u ON d.user_id = u.id");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBookedTimeSlots($doctorId, $date)
    {
        $stmt = $this->connection->prepare("
            SELECT appointment_time 
            FROM appointments 
            WHERE doctor_id = ? AND appointment_date = ? AND status IN ('pending', 'confirmed')
        ");
        $stmt->bind_param("is", $doctorId, $date);
        $stmt->execute();
        $result = $stmt->get_result();

        $bookedSlots = [];
        while ($row = $result->fetch_assoc()) {
            $bookedSlots[] = $row['appointment_time'];
        }

        return $bookedSlots;
    }

    public function createAppointment($patientId, $doctorId, $appointmentDate, $appointmentTime, $reason)
    {
        $status = 'pending';
        $stmt = $this->connection->prepare("
            INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, reason)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssss", $patientId, $doctorId, $appointmentDate, $appointmentTime, $status, $reason);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    public function getMedicalRecordById($recordId)
    {
        $stmt = $this->connection->prepare("
            SELECT mr.*, p.fullname as patient_name, d.fullname as doctor_name
            FROM medical_records mr
            INNER JOIN users p ON mr.patient_id = p.id
            INNER JOIN users d ON mr.doctor_id = d.id 
            WHERE mr.id = ?
        ");
        $stmt->bind_param("i", $recordId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createMedicalRecord($patientId, $doctorId, $diagnosis, $prescription, $notes, $visitDate)
    {
        $stmt = $this->connection->prepare("
            INSERT INTO medical_records (patient_id, doctor_id, diagnosis, prescription, notes, visit_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissss", $patientId, $doctorId, $diagnosis, $prescription, $notes, $visitDate);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    public function getAllPatients()
    {
        $stmt = $this->connection->prepare("
            SELECT id, fullname
            FROM users
            WHERE user_type = 'patient'
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createLabTest($name, $description, $cost)
    {
        $stmt = $this->connection->prepare("
            INSERT INTO lab_tests (name, description, cost) VALUES (?, ?, ?)
        ");
        $stmt->bind_param("ssd", $name, $description, $cost);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getAllLabTests()
    {
        $stmt = $this->connection->prepare("SELECT * FROM lab_tests");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createTestResult($patientId, $testId, $doctorId)
    {
        $resultDate = date("Y-m-d"); // Today's date
        $status = "pending"; // Initial status
        $stmt = $this->connection->prepare("
            INSERT INTO test_results (patient_id, test_id, result_date, status, doctor_id) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissi", $patientId, $testId, $resultDate, $status, $doctorId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getTestResultById($testResultId)
    {
        $stmt = $this->connection->prepare("
            SELECT tr.*, p.fullname as patient_name, lt.name as test_name
            FROM test_results tr
            INNER JOIN users p ON tr.patient_id = p.id
            INNER JOIN lab_tests lt ON tr.test_id = lt.id
            WHERE tr.id = ?
        ");
        $stmt->bind_param("i", $testResultId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateTestResult($testResultId, $resultDetails, $status)
    {
        $stmt = $this->connection->prepare("
            UPDATE test_results SET result_details = ?, status = ? WHERE id = ?
        ");
        $stmt->bind_param("ssi", $resultDetails, $status, $testResultId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getPendingTestResults()
    {
        $stmt = $this->connection->prepare("
            SELECT tr.*, p.fullname as patient_name, lt.name as test_name
            FROM test_results tr
            INNER JOIN users p ON tr.patient_id = p.id
            INNER JOIN lab_tests lt ON tr.test_id = lt.id
            WHERE tr.status = 'pending'
            ORDER BY tr.result_date ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllUsers()
    {
        $stmt = $this->connection->prepare("SELECT * FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createQuery($userId, $subject, $message)
    {
        $status = 'pending'; // Default status
        $stmt = $this->connection->prepare("
            INSERT INTO queries (user_id, subject, message, status)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isss", $userId, $subject, $message, $status);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getAllQueries()
    {
        $stmt = $this->connection->prepare("
            SELECT q.*, u.fullname as submitted_by
            FROM queries q
            LEFT JOIN users u ON q.user_id = u.id
            ORDER BY q.created_at DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateQueryStatus($queryId, $newStatus)
    {
        $stmt = $this->connection->prepare("
            UPDATE queries SET status = ? WHERE id = ?
        ");
        $stmt->bind_param("si", $newStatus, $queryId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getAppointmentsForToday()
    {
        $today = date("Y-m-d"); // Get today's date
        $stmt = $this->connection->prepare("
            SELECT a.*, p.fullname as patient_name, u.fullname as doctor_name
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors d ON a.doctor_id = d.id
            INNER JOIN users u ON d.user_id = u.id  -- Join with users table again for doctor's name
            WHERE DATE(a.appointment_date) = ?
        ");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserById($userId)
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateUser($userId, $fullname, $email, $userType)
    {
        $stmt = $this->connection->prepare("UPDATE users SET fullname = ?, email = ?, user_type = ? WHERE id = ?");
        $stmt->bind_param("sssi", $fullname, $email, $userType, $userId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function deleteUser($userId)
    {
        $stmt = $this->connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getDoctorById($doctorId) {
        $stmt = $this->connection->prepare("
            SELECT d.*, u.fullname, u.email, u.phone, u.address
            FROM doctors d
            INNER JOIN users u ON d.user_id = u.id
            WHERE d.id = ?
        ");
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateDoctor($doctorId, $fullname, $email, $specialty, $qualifications, $availability, $phone, $address) {
        // Begin transaction
        $this->connection->begin_transaction();
    
        try {
            // Update the user's fullname, email, phone, and address in the users table
            $stmt = $this->connection->prepare("
                UPDATE users SET fullname = ?, email = ?, phone = ?, address = ?
                WHERE id = (SELECT user_id FROM doctors WHERE id = ?)
            ");
            $stmt->bind_param("ssssi", $fullname, $email, $phone, $address, $doctorId);
            $stmt->execute();
    
            // Update the doctor's specialty, qualifications, and availability in the doctors table
            $stmt = $this->connection->prepare("
                UPDATE doctors SET specialty = ?, qualifications = ?, availability = ?
                WHERE id = ?
            ");
            $stmt->bind_param("sssi", $specialty, $qualifications, $availability, $doctorId);
            $stmt->execute();
    
            // Commit transaction
            $this->connection->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->connection->rollback();
            error_log("Error updating doctor: " . $e->getMessage());
            return false;
        }
    }
    
    
    
    public function deleteDoctor($doctorId) {
        // Get the associated user_id before deleting the doctor
        $stmt = $this->connection->prepare("SELECT user_id FROM doctors WHERE id = ?");
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        $userId = $doctor['user_id'];
    
        // Delete the doctor from the doctors table
        $stmt = $this->connection->prepare("DELETE FROM doctors WHERE id = ?");
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
    
        // Optionally, delete the associated user from the users table
        $stmt = $this->connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    
        return true; // Return true if the operations are successful
    }

    public function getSetting($settingName) {
        $stmt = $this->connection->prepare("SELECT value FROM settings WHERE name = ?");
        $stmt->bind_param("s", $settingName);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['value'];
        }
        return null; // Or a default value
    }

    public function updateSetting($settingName, $settingValue)
    {
        $stmt = $this->connection->prepare("UPDATE settings SET value = ? WHERE name = ?");
        $stmt->bind_param("ss", $settingValue, $settingName);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function createUser($fullname, $email, $hashedPassword, $userType, $phone, $address) {
        $stmt = $this->connection->prepare("INSERT INTO users (fullname, email, password, user_type, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fullname, $email, $hashedPassword, $userType, $phone, $address);
        $stmt->execute();
    
        // Return the ID of the newly inserted user
        return $stmt->insert_id;
    }

    public function createDoctor($userId, $specialty, $qualifications, $availability)
    {
        $stmt = $this->connection->prepare("INSERT INTO doctors (user_id, specialty, qualifications, availability) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $specialty, $qualifications, $availability);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getAppointmentById($appointmentId)
    {
        $stmt = $this->connection->prepare("
            SELECT a.*, p.fullname as patient_name, d.fullname as doctor_name, d.specialty as doctor_specialty
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors d ON a.doctor_id = d.user_id
            WHERE a.id = ?
        ");
        $stmt->bind_param("i", $appointmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateAppointmentStatus($appointmentId, $status)
    {
        $stmt = $this->connection->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $appointmentId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function searchDoctors($searchQuery)
    {
        $searchQuery = "%" . $searchQuery . "%"; // Add wildcards for partial matching
        $stmt = $this->connection->prepare("
            SELECT d.id, u.fullname, u.email, d.specialty
            FROM doctors d
            INNER JOIN users u ON d.user_id = u.id
            WHERE u.fullname LIKE ? OR d.specialty LIKE ?
        ");
        $stmt->bind_param("ss", $searchQuery, $searchQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchAppointments($searchQuery, $searchDate)
    {
        $conditions = [];
        $params = [];
        $types = "";

        if (!empty($searchQuery)) {
            $conditions[] = "(p.fullname LIKE ? OR d.fullname LIKE ?)";
            $params[] = "%" . $searchQuery . "%";
            $params[] = "%" . $searchQuery . "%";
            $types .= "ss";
        }

        if (!empty($searchDate)) {
            $conditions[] = "a.appointment_date = ?";
            $params[] = $searchDate;
            $types .= "s";
        }

        $whereClause = "";
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->connection->prepare("
            SELECT a.*, p.fullname as patient_name, d.fullname as doctor_name
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors d ON a.doctor_id = d.user_id
            $whereClause
            ORDER BY a.appointment_date, a.appointment_time
        ");

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLabTestResultsByPatientId($patientId)
    {
        $stmt = $this->connection->prepare("
            SELECT tr.*, lt.name as test_name, u.fullname as doctor_name, a.appointment_date as appointment_date
            FROM test_results tr
            INNER JOIN lab_tests lt ON tr.test_id = lt.id
            INNER JOIN appointments a ON tr.patient_id = a.patient_id
            INNER JOIN doctors d ON a.doctor_id = d.id  -- Join with doctors table using the correct foreign key
            INNER JOIN users u ON d.user_id = u.id      -- Join with users table using d.user_id to get doctor's fullname
            WHERE tr.patient_id = ?
            ORDER BY tr.result_date DESC"
        );
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPastAppointmentsByPatientId($patientId)
    {
        $stmt = $this->connection->prepare("
            SELECT a.*, d.fullname as doctor_name
            FROM appointments a
            INNER JOIN doctors d ON a.doctor_id = d.user_id
            WHERE a.patient_id = ? AND a.appointment_date < CURDATE()
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLabTestById($labTestId)
    {
        $stmt = $this->connection->prepare("SELECT * FROM lab_tests WHERE id = ?");
        $stmt->bind_param("i", $labTestId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateLabTest($labTestId, $name, $description, $cost)
    {
        $stmt = $this->connection->prepare("UPDATE lab_tests SET name = ?, description = ?, cost = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $name, $description, $cost, $labTestId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function deleteLabTest($labTestId)
    {
        $stmt = $this->connection->prepare("DELETE FROM lab_tests WHERE id = ?");
        $stmt->bind_param("i", $labTestId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function updateUserPasswordResetToken($userId, $token, $expires)
    {
        $stmt = $this->connection->prepare("UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expires, $userId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getUserByPasswordResetToken($token)
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE password_reset_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateUserPassword($userId, $hashedPassword)
    {
        $stmt = $this->connection->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function clearUserPasswordResetToken($userId)
    {
        $stmt = $this->connection->prepare("UPDATE users SET password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}