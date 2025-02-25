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

    public function beginTransaction()
    {
        $this->connection->begin_transaction();
    }

    public function commitTransaction()
    {
        $this->connection->commit();
    }

    public function rollbackTransaction()
    {
        $this->connection->rollback();
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
        // Option 1: Select all columns and explicitly add user_type (Recommended for clarity)
        $stmt = $this->connection->prepare("SELECT *, user_type FROM users WHERE email = ?");

        // Option 2: Explicitly list all columns you need, including user_type (More verbose, but also clear)
        // $stmt = $this->connection->prepare("SELECT id, fullname, email, password, created_at, password_reset_token, password_reset_expires, user_type FROM users WHERE email = ?");


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
        $stmt = $this->connection->prepare("
            SELECT 
                s.*, 
                u.fullname, 
                u.email,
                b.name AS branch_name  -- Fetch branch name and alias it as branch_name
            FROM staff s
            INNER JOIN users u ON s.user_id = u.id
            LEFT JOIN branches b ON s.staff_department = b.id  -- LEFT JOIN branches table (adjust JOIN condition if needed)
            WHERE s.user_id = ? AND u.user_type = 'staff'
        ");
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
            SELECT 
                a.*, 
                u.fullname as doctor_name,
                b.name AS branch_name  -- ADDED: Fetch branch name and alias it
            FROM appointments a
            INNER JOIN doctors d ON a.doctor_id = d.id
            INNER JOIN users u ON d.user_id = u.id
            LEFT JOIN branches b ON a.branch_id = b.id -- ADDED: LEFT JOIN branches table
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
        $stmt = $this->connection->prepare("
            SELECT 
                d.id, 
                u.fullname, 
                u.email, 
                d.specialty,
                b.name AS branch_name  -- Fetch branch name and alias it as branch_name
            FROM doctors d
            INNER JOIN users u ON d.user_id = u.id
            LEFT JOIN branches b ON d.branch_id = b.id  -- LEFT JOIN branches table
            ORDER BY u.fullname ASC
        ");
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

    public function createAppointment($patientId, $doctorId, $branchId, $appointmentDate, $appointmentTime, $reason)
    {
        $status = 'pending';
        $stmt = $this->connection->prepare("
            INSERT INTO appointments (patient_id, doctor_id, branch_id, appointment_date, appointment_time, status, reason)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiissss", $patientId, $doctorId, $branchId, $appointmentDate, $appointmentTime, $status, $reason);
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
            SELECT
                a.*,
                p.fullname as patient_name,
                u.fullname as doctor_name,
                b.name AS branch_name  -- ADDED: Fetch branch name and alias it
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors d ON a.doctor_id = d.id
            INNER JOIN users u ON d.user_id = u.id  -- Join with users table again for doctor's name
            LEFT JOIN branches b ON a.branch_id = b.id -- ADDED: LEFT JOIN branches table
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

    public function updateUser($userId, $fullname, $email, $userType, $phone, $address)
    {
        $stmt = $this->connection->prepare("UPDATE users SET fullname = ?, email = ?, user_type = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $fullname, $email, $userType, $phone, $address, $userId);
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

    public function updateDoctor($doctorId, $fullname, $email, $specialty, $qualifications, $availability, $phone, $address, $branchId) { // Updated to accept $branchId
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

            // Update the doctor's specialty, qualifications, availability, and branch_id in the doctors table
            $stmt = $this->connection->prepare("
                UPDATE doctors SET specialty = ?, qualifications = ?, availability = ?, branch_id = ?  -- Added branch_id to SET columns
                WHERE id = ?
            ");
            $stmt->bind_param("ssssi", $specialty, $qualifications, $availability, $branchId, $doctorId); // Added "i" for branchId and $branchId to bind_param
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

    public function createStaff($userId, $staffDepartment = null, $staffPosition = null, $branchId = null) // Updated to accept $branchId
    {
        $stmt = $this->connection->prepare("
            INSERT INTO staff (user_id, staff_department, staff_position, branch_id)  -- Added branch_id to INSERT columns
            VALUES (?, ?, ?, ?)  -- Added placeholder for branch_id
        ");
        $stmt->bind_param("issi", $userId, $staffDepartment, $staffPosition, $branchId); // Added "i" for branchId and $branchId to bind_param
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function createDoctor($userId, $specialty, $qualifications, $availability, $branchId)
    {
        $stmt = $this->connection->prepare("
            INSERT INTO doctors (user_id, specialty, qualifications, availability, branch_id)  -- Added branch_id to INSERT columns
            VALUES (?, ?, ?, ?, ?)  -- Added placeholder for branch_id
        ");
        $stmt->bind_param("isssi", $userId, $specialty, $qualifications, $availability, $branchId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getAppointmentById($appointmentId)
    {
        $stmt = $this->connection->prepare("
            SELECT
                a.*,
                p.fullname as patient_name,
                p.email as email,  -- Changed alias to 'email' here
                u.fullname as doctor_name,
                doc.specialty as doctor_specialty,
                b.name AS branch_name
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors doc ON a.doctor_id = doc.id
            INNER JOIN users u ON doc.user_id = u.id
            LEFT JOIN branches b ON a.branch_id = b.id
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
            SELECT 
                d.id, 
                u.fullname, 
                u.email, 
                d.specialty,
                b.name AS branch_name  -- Fetch branch name and alias it as branch_name
            FROM doctors d
            INNER JOIN users u ON d.user_id = u.id
            LEFT JOIN branches b ON d.branch_id = b.id  -- LEFT JOIN branches table
            WHERE u.fullname LIKE ? OR d.specialty LIKE ?
        ");
        $stmt->bind_param("ss", $searchQuery, $searchQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchAppointments($searchQuery, $searchDate, $branchFilter = 0) // Updated to accept $branchFilter
    {
        $conditions = [];
        $params = [];
        $types = "";

        if ($branchFilter > 0) {  // Filter by branch if branchFilter is greater than 0 (valid branch ID)
            $conditions[] = "a.branch_id = ?";
            $params[] = $branchFilter;
            $types .= "i";
        }

        if (!empty($searchQuery)) {
            $conditions[] = "(p.fullname LIKE ? OR u.fullname LIKE ?)";
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
            SELECT 
                a.*, 
                p.fullname as patient_name, 
                u.fullname as doctor_name,
                b.name AS branch_name   -- ADDED: Fetch branch name and alias it
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors doc ON a.doctor_id = doc.id 
            INNER JOIN users u ON doc.user_id = u.id        
            LEFT JOIN branches b ON a.branch_id = b.id    -- ADDED: LEFT JOIN to fetch branch name
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
            SELECT 
                a.*, 
                u.fullname as doctor_name,
                b.name AS branch_name  -- ADDED: Fetch branch name and alias it
            FROM appointments a
            INNER JOIN doctors d ON a.doctor_id = d.user_id
            INNER JOIN users u ON d.user_id = u.id
            LEFT JOIN branches b ON a.branch_id = b.id -- ADDED: LEFT JOIN branches table
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

    public function getAllBlogPosts()
    {
    $stmt = $this->connection->prepare("
        SELECT * FROM blog_posts ORDER BY created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBlogPostById($postId)
    {
        $stmt = $this->connection->prepare("
            SELECT * FROM blog_posts WHERE id = ?
        ");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAllBranches()  // New methods for branches - ADDED HERE
    {
        $stmt = $this->connection->prepare("SELECT * FROM branches ORDER BY name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBranchById($branchId)
    {
        $stmt = $this->connection->prepare("SELECT * FROM branches WHERE id = ?");
        $stmt->bind_param("i", $branchId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createBranch($name, $address, $city, $phone)
    {
        $stmt = $this->connection->prepare("
            INSERT INTO branches (name, address, city, phone)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $name, $address, $city, $phone);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function updateBranch($branchId, $name, $address, $city, $phone)
    {
        $stmt = $this->connection->prepare("
            UPDATE branches SET name = ?, address = ?, city = ?, phone = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssssi", $name, $address, $city, $phone, $branchId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function deleteBranch($branchId)
    {
        $stmt = $this->connection->prepare("DELETE FROM branches WHERE id = ?");
        $stmt->bind_param("i", $branchId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }


    public function getAllPendingAppointments()
    {
        $stmt = $this->connection->prepare("
            SELECT a.*, p.fullname as patient_name, u.fullname as doctor_name, doc.specialty as doctor_specialty
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors doc ON a.doctor_id = doc.id  -- Alias doctors table as 'doc'
            INNER JOIN users u ON doc.user_id = u.id         -- Join doctors 'doc' with users 'u' to get doctor's fullname
            WHERE a.status = 'pending'
            ORDER BY a.appointment_date, a.appointment_time
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchPendingAppointments($searchQuery)
    {
        $searchQuery = "%" . $searchQuery . "%";
        $stmt = $this->connection->prepare("
            SELECT a.*, p.fullname as patient_name, d.fullname as doctor_name, d.specialty as doctor_specialty
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors d ON a.doctor_id = d.user_id
            WHERE a.status = 'pending' AND (p.fullname LIKE ? OR d.fullname LIKE ?)
            ORDER BY a.appointment_date, a.appointment_time
        ");
        $stmt->bind_param("ss", $searchQuery, $searchQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllAppointments()
    {
        $stmt = $this->connection->prepare("
            SELECT a.*, p.fullname as patient_name, u.fullname as doctor_name, b.name AS branch_name
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.id
            INNER JOIN doctors d ON a.doctor_id = d.id
            INNER JOIN users u ON d.user_id = u.id
            LEFT JOIN branches b ON a.branch_id = b.id
            ORDER BY a.appointment_date, a.appointment_time DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDoctorsByBranchAndSearch($branchId = null, $searchTerm = '')
    {
        $conditions = [];
        $params = [];
        $types = "";

        if ($branchId !== null) {
            $conditions[] = "d.branch_id = ?";
            $params[] = $branchId;
            $types .= "i";
        }

        if (!empty($searchTerm)) {
            $searchTermParam = "%" . $searchTerm . "%";
            $conditions[] = "(u.fullname LIKE ? OR d.specialty LIKE ?)";
            $params[] = $searchTermParam;
            $params[] = $searchTermParam;
            $types .= "ss";
        }

        $whereClause = "";
        if (!empty($conditions)) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->connection->prepare("
            SELECT
                d.id,
                u.fullname,
                d.specialty,
                b.name AS branch_name  -- ADDED: Fetch branch name and alias it
            FROM doctors d
            INNER JOIN users u ON d.user_id = u.id
            LEFT JOIN branches b ON d.branch_id = b.id  -- ADDED: LEFT JOIN to fetch branch name
            $whereClause
            ORDER BY u.fullname ASC
        ");

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        if ($stmt->error) {
            error_log("Database query error in getDoctorsByBranchAndSearch: " . $stmt->error);
            return false; // Indicate query failure
        }

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStaffUser($userId, $fullname, $email, $userType, $phone, $address, $staffDepartment, $staffPosition, $branchId)
    {
        // Begin transaction to ensure atomicity
        $this->connection->begin_transaction();

        try {
            // 1. Update general user information in the 'users' table
            $stmtUser = $this->connection->prepare("
                UPDATE users SET fullname = ?, email = ?, user_type = ?, phone = ?, address = ?
                WHERE id = ?
            ");
            $stmtUser->bind_param("sssssi", $fullname, $email, $userType, $phone, $address, $userId);
            if (!$stmtUser->execute()) {
                throw new Exception("Error updating user record."); // Throw exception if user update fails
            }

            // 2. Update staff-specific information in the 'staff' table, including branch_id
            $stmtStaff = $this->connection->prepare("
                UPDATE staff SET staff_department = ?, staff_position = ?, branch_id = ?
                WHERE user_id = ?
            ");
            $stmtStaff->bind_param("ssii", $staffDepartment, $staffPosition, $branchId, $userId); // Include branchId in update
            if (!$stmtStaff->execute()) {
                throw new Exception("Error updating staff record."); // Throw exception if staff update fails
            }

            // Commit transaction if both updates are successful
            $this->connection->commit();
            return true;

        } catch (Exception $e) {
            // Rollback transaction in case of any error
            $this->connection->rollback();
            error_log("Error updating staff user: " . $e->getMessage()); // Log error for debugging
            return false;
        }
    }

    public function updateAppointmentStatusAndPrice($appointmentId, $status, $price)
    {
        $stmt = $this->connection->prepare("UPDATE appointments SET status = ?, price = ? WHERE id = ?");
        $stmt->bind_param("sdi", $status, $price, $appointmentId); // "s" for status, "d" for price (decimal), "i" for appointmentId
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}