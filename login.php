<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$response = array(
    'success' => false,
    'message' => '',
    'redirect' => ''
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        
        if ($stmt->execute()) {
            $stmt->store_result();
            
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username, $hashed_password);
                if ($stmt->fetch()) {
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        
                        $response['success'] = true;
                        $response['redirect'] = 'profile.php';
                    } else {
                        $response['message'] = "Invalid username or password.";
                    }
                }
            } else {
                $response['message'] = "Invalid username or password.";
            }
        } else {
            $response['message'] = "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
    $conn->close();
}

echo json_encode($response);
?>