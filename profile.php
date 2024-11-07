<?php
session_start();
require_once 'config.php';

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
                        
                        // Check if there's a redirect URL in session storage
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
                        echo "Invalid username or password.";
                    }
                }
            } else {
                echo "Invalid username or password.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo $_SESSION["username"]  ?> </h1>
            <p>Your Personal Fitness Profile</p>
        </header>

        <section class="profile">
            <div class="card bmi-card">
                <h2>Your BMI</h2>
                <button onclick="location.href='bmi.html'">Calculate BMI</button>
            </div>

            <div class="card diet-plan">
                <h2>Diet Plans</h2>
                <button onclick="location.href='diet.plan.html'">View Diet Plans</button>
            </div>

            <div class="card purchases">
                <h2>Your Purchases</h2>
                <ul id="purchase-history">
                    <li>Protein Powder - $25.99</li>
                    <li>Multivitamins - $15.50</li>
                    <li>BCAA Supplements - $19.99</li>
                </ul>
            </div>
        </section>
    </div>

    <script src="profile.js"></script>
</body>
</html>
