<?php
session_start();
require_once 'config.php';

// Initialize error message variable
$error_message = "";

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
                        
                        // Success redirect with JavaScript
                        echo '<script>
                            const redirectUrl = sessionStorage.getItem("redirectUrl");
                            if (redirectUrl) {
                                sessionStorage.removeItem("redirectUrl");
                                window.location.href = redirectUrl;
                            } else {
                                window.location.href = "profile.php";
                            }
                        </script>';
                        exit();
                    } else {
                        $error_message = "Invalid username or password.";
                    }
                }
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!-- Add this at the top of your HTML body -->
<?php if (!empty($error_message)): ?>
    <div id="error-popup" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); 
                                background-color: #ff4444; color: white; padding: 15px; 
                                border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); 
                                z-index: 1000;">
        <?php echo htmlspecialchars($error_message); ?>
        <button onclick="this.parentElement.style.display='none';" 
                style="background: none; border: none; color: white; float: right; 
                       cursor: pointer; margin-left: 10px; font-weight: bold;">
            ×
        </button>
    </div>
<?php endif; ?>