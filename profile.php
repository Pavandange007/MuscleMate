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
    <style>
        :root {
            --dark-grey: #2A2D3E;
            --medium-grey: #363B4D;
            --light-grey: #404557;
            --cyan: #00BCD4;
            --cyan-hover: #00ACC1;
            --text-primary: #FFFFFF;
            --text-secondary: #B4B6BE;
            --shadow: rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--dark-grey);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }

        .welcome {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            color: var(--text-primary);
            border-left: 4px solid var(--cyan);
            padding-left: 15px;
        }

        .dashboard {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .card {
            background: var(--medium-grey);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px var(--shadow);
        }

        .card-header {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--cyan);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-secondary);
        }

        input {
            width: 100%;
            padding: 12px;
            background: var(--dark-grey);
            border: 1px solid var(--light-grey);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: var(--cyan);
        }

        .stats-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 20px;
            background: var(--dark-grey);
        }

        select {
            width: 100%;
            padding: 12px;
            background: var(--dark-grey);
            border: 1px solid var(--light-grey);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2300BCD4' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        select:focus {
            outline: none;
            border-color: var(--cyan);
        }

        .monthly-challenge {
            grid-column: span 2;
            background: var(--medium-grey);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid var(--light-grey);
            position: relative;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            box-shadow: 0 4px 6px var(--shadow);
        }

        .challenge-header {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--cyan);
            width: 100%;
        }

        .challenge-progress {
            background: var(--dark-grey);
            padding: 12px 20px;
            border-radius: 8px;
            display: inline-block;
        }

        .mark-complete {
            background: var(--cyan);
            color: var(--text-primary);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .mark-complete:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .mark-complete:not(:disabled):hover {
            background: var(--cyan-hover);
        }

        .cooldown-timer {
            color: var(--text-secondary);
            font-size: 14px;
            margin-left: 10px;
        }

        .reset-button {
            padding: 12px 24px;
            background: var(--medium-grey);
            border: 1px solid var(--light-grey);
            border-radius: 8px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s;
        }

        .reset-button:hover {
            background: var(--light-grey);
        }

        .bmi-section {
            grid-column: span 2;
            display: flex;
            gap: 20px;
            margin-top: 25px;
            justify-content: center;
        }

        .action-button {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--cyan);
            color: var(--text-primary);
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px var(--shadow);
        }

        .action-button:hover {
            background: var(--cyan-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px var(--shadow);
        }

        .button-icon {
            font-size: 20px;
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            .monthly-challenge {
                grid-column: span 1;
            }
            .bmi-section {
                grid-column: span 1;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="welcome">Welcome!</h1>
        
        <div class="dashboard">
            <div class="card">
                <div class="card-header">Profile Overview</div>
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" value="25">
                </div>
                <div class="form-group">
                    <label for="targetWeight">Target Weight (kg)</label>
                    <input type="number" id="targetWeight" value="70">
                </div>
            </div>

            <div class="card">
                <div class="card-header">Quick Stats</div>
                <div class="stats-item">
                    <div class="icon">🏆</div>
                    <div class="form-group">
                        <label for="workoutStreak">Workout Streak (days)</label>
                        <input type="number" id="workoutStreak" value="5">
                    </div>
                </div>
                
                <div class="stats-item">
                    <div class="icon">💪</div>
                    <div class="form-group">
                        <label for="lastWorkout">Last Workout</label>
                        <select id="lastWorkout">
                            <option value="fullBody">Full Body Workout</option>
                            <option value="upperBody">Upper Body</option>
                            <option value="lowerBody">Lower Body</option>
                            <option value="cardio">Cardio</option>
                            <option value="hiit">HIIT</option>
                        </select>
                    </div>
                </div>

                <div class="stats-item">
                    <div class="icon">🎯</div>
                    <div class="form-group">
                        <label for="currentGoal">Current Goal</label>
                        <select id="currentGoal">
                            <option value="weightLoss">Weight Loss</option>
                            <option value="muscleGain">Muscle Gain</option>
                            <option value="endurance">Endurance</option>
                            <option value="strength">Strength</option>
                        </select>
                    </div>
                </div>

                <div class="stats-item">
                    <div class="icon">📅</div>
                    <div class="form-group">
                        <label for="nextWorkout">Next Workout</label>
                        <select id="nextWorkout">
                            <option value="upperBody">Upper Body</option>
                            <option value="lowerBody">Lower Body</option>
                            <option value="fullBody">Full Body Workout</option>
                            <option value="cardio">Cardio</option>
                            <option value="hiit">HIIT</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="monthly-challenge">
                <div class="challenge-header">20 Pushups Daily Challenge</div>
                <div class="challenge-progress">
                    <span>Progress: </span>
                    <strong>0/30 days</strong>
                </div>
                <button class="mark-complete">Mark Today Complete</button>
                <span class="cooldown-timer"></span>
                <button class="reset-button">Reset Progress</button>
            </div>

            <div class="bmi-section">
                <a href="bmi.html" class="action-button">
                    <span class="button-icon">📊</span>
                    Calculate BMI
                </a>
                <a href="workout-plan.html" class="action-button">
                    <span class="button-icon">💪</span>
                    Workout Plans
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Save select values to localStorage
            const selects = document.querySelectorAll('select');
            selects.forEach(select => {
                // Load saved value if exists
                const savedValue = localStorage.getItem(select.id);
                if (savedValue) {
                    select.value = savedValue;
                }

                // Save on change
                select.addEventListener('change', function() {
                    localStorage.setItem(this.id, this.value);
                });
            });

            // Initialize pushup challenge elements
            const challengeProgress = document.querySelector('.challenge-progress strong');
            const completeButton = document.querySelector('.mark-complete');
            const resetButton = document.querySelector('.reset-button');
            const cooldownSpan = document.querySelector('.cooldown-timer');

            // Initialize progress from localStorage
            let pushupProgress = parseInt(localStorage.getItem('pushupProgress')) || 0;
            let lastPushupDate = localStorage.getItem('lastPushupDate') || null;

            // Update initial display
            challengeProgress.textContent = `${pushupProgress}/30 days`;

            function canComplete() {
                if (!lastPushupDate) return true;
                const lastDate = new Date(lastPushupDate);
                const now = new Date();
                const hoursDiff = (now - lastDate) / (1000 * 60 * 60);
                return hoursDiff >= 24;
            }

            function updateCooldownDisplay() {
                if (!lastPushupDate) {
                    cooldownSpan.textContent = '';
                    enableButton();
                    return;
                }

                const now = new Date();
                const lastDate = new Date(lastPushupDate);
                const timeLeft = 24 * 60 * 60 * 1000 - (now - lastDate);

                if (timeLeft <= 0) {
                    cooldownSpan.textContent = '';
                    enableButton();
                    return;
                }

                const hoursLeft = Math.floor(timeLeft / (1000 * 60 * 60));
                const minutesLeft = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                cooldownSpan.textContent = `Next available in: ${hoursLeft}h ${minutesLeft}m`;
                disableButton();
            }

            function enableButton() {
                completeButton.disabled = false;
                completeButton.style.opacity = '1';
                completeButton.style.cursor = 'pointer';
            }

            function disableButton() {
                completeButton.disabled = true;
                completeButton.style.opacity = '0.5';
                completeButton.style.cursor = 'not-allowed';
            }

            // Update cooldown display every minute
            setInterval(updateCooldownDisplay, 60000);
            updateCooldownDisplay();

            completeButton.addEventListener('click', function() {
                if (!canComplete()) return;

                pushupProgress++;
                if (pushupProgress > 30) pushupProgress = 30;
                
                lastPushupDate = new Date().toISOString();

                localStorage.setItem('pushupProgress', pushupProgress);
                localStorage.setItem('lastPushupDate', lastPushupDate);

                challengeProgress.textContent = `${pushupProgress}/30 days`;
                updateCooldownDisplay();

                const originalText = this.textContent;
                this.textContent = 'Completed!';
                setTimeout(() => {
                    this.textContent = originalText;
                }, 1000);
            });

            resetButton.addEventListener('click', function() {
                pushupProgress = 0;
                lastPushupDate = null;
                localStorage.removeItem('pushupProgress');
                localStorage.removeItem('lastPushupDate');
                challengeProgress.textContent = '0/30 days';
                updateCooldownDisplay();
            });
        });
    </script>
</body>
</html>