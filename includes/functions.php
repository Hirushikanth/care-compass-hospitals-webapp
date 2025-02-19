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
?>