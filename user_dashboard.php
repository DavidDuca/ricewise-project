<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USER DASHBOARD</title>
    <link rel="icon" href="logo.png">
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/luxon@2.0.2/build/global/luxon.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <div class="nav-bar">
            <input type="checkbox" id="check">
            <label for="check" class="checkbtn">
                <i class="fa-solid fa-bars" style="font-size: 20px;"></i>
            </label>
            <div class="logo">
            <i class="fa-solid fa-user" style="font-size: 20px;"></i>
            <h3 class="hi">Hi, <?php echo $_SESSION['username']; ?>!</h3>
            </div>
            <div class="nav">
                <ul class="show-nav" >
                    <li class="active"><a href="#">DASHBOARD</a></li>
                    <li> <a href="history.php">HISTORY</a> </li>
                    <li> <a href="#">ABOUT</a> </li>
                    <li> <a href="#">DEVELOPER</a> </li>
                    <li> <a href="logout.php">LOG OUT</a> </li>
                </ul>
            </div>
            <div class="logout">
                <button class="logout-btn">SERVER STATUS</button>
                <div id="online" class="online">

                </div>
            </div>
        </div>
        <div class="main-container">
            <div class="main">
            <div class="header">
                <h1 class="headertxt">Today's Sugar Consumption Progress Bar</h1>
            </div>
                <div class="tracker">
                    <div class="progress-container">
                        <div class="progress-labels">
                            <span>1g</span><span>5g</span><span>10g</span><span>15g</span><span>20g</span><span>25g</span>
                            <span>30g</span><span>35g</span><span>40g</span><span>45g</span><span>50g</span>
                        </div>
                        <div class="progress-bar" id="progressBar"></div>
                    </div>
                </div>
                <form action="save_intake.php" method="post">
                    <h2>LOG YOUR RICE INTAKE</h2>
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                    
                    <label for="meal_time">Meal Time:</label>
                    <select name="meal_time" id="meal_time" required>
                        <option value="" disabled selected>Select a Meal Time</option>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                    
                    <label for="rice_cups">Number of Rice Cups:</label>
                    <input type="number" id="rice_cups" class="sugarInput" name="rice_cups" placeholder="Enter the amount of rice in cups" pattern="\d*" title="Numbers only" required>
                    
                    <label for="riceTypes">TYPE OF RICE:</label>
                    <select id="riceTypes" name="rice_type" class="menu1" required>
                        <option value="" disabled selected>Select a type of rice</option>
                        <option value="Jasmine Rice">Jasmine Rice</option>
                        <option value="Basmati Rice">Basmati Rice</option>
                        <option value="White Rice">White Rice</option>
                        <option value="Brown Rice">Brown Rice</option>
                        <option value="Sticky Rice">Sticky Rice</option>
                    </select>

                    <button type="button" class="calcButton" name="calculate" onclick="calcsugar();">CALCULATE</button>
                    
                    <label for="sugar1">The Amount of Sugar Calculated:</label>
                    <input id="sugar1" type="text" name="sugar_amount" value="" readonly>
                    
                    <button type="submit" class="save-intake" id="saveButton">SAVE INTAKE</button>
                    <p id="warningMessage" style="color: red; display: none;">Calculate first before saving!</p>

                </form>
                
                <div class="total">
                    <h1 class="totaltxt" id="current-time">Total Sugar Intake as of: </h1>
                    <input id="totalSugar" type="text" value="" readonly>
                </div>
            </div>
        </div>
    </div>
    <script>
        function updateTime() {
            const DateTime = luxon.DateTime;
            let currentTime = DateTime.now().setZone("Asia/Manila");
            let formattedTime = currentTime.toFormat('hh:mm:ss a');

            document.getElementById("current-time").textContent = "Total Sugar Intake as of " + formattedTime + ":";
        }

        setInterval(updateTime, 1000);
        updateTime();
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("data.php")
                .then(response => response.json()) 
                .then(data => {
                    let statusElement = document.getElementById("statusText");
                    let onlineElement = document.getElementById("online");

                    console.log("Received Data:", data);

                    if (data.status && data.color) {
                        
                        statusElement.innerHTML = data.status;
                        statusElement.style.color = data.color;
                        onlineElement.style.backgroundColor = data.color;
                    } else {
                        console.error("Invalid data received:", data);
                    }
                })
                .catch(error => console.error("Error fetching data:", error));
        });
    </script>
        <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.4.0/firebase-app.js";
        import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/11.4.0/firebase-messaging.js";
        const user_id = <?php echo json_encode($user_id); ?>;
        const firebaseConfig = {
            apiKey: "AIzaSyAAQQYljTV57FT01S11AC1MR9nf5VL35wo",
            authDomain: "mywebapp-ed956.firebaseapp.com",
            projectId: "mywebapp-ed956",
            storageBucket: "mywebapp-ed956.appspot.com",
            messagingSenderId: "1001107767854",
            appId: "1:1001107767854:web:1854137e158f06a60255bf",
            measurementId: "G-1CEB79R07R"
        };

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        navigator.serviceWorker.register("sw.js")
            .then(registration => {
                getToken(messaging, {
                    serviceWorkerRegistration: registration,
                    vapidKey: 'BHcS9xJDxOw9kWTHr7iMaaHytibVkymJhIlKFrMR8bEhEdqGnlmmcU5vVmcbE_zC3ExoL4bXnexap0n-6yyhhwU'
                })
                .then((currentToken) => {
                    if (currentToken) {
                        console.log("Token is: " + currentToken);

                        sendTokenToServer(currentToken);
                    } else {
                        console.log('No registration token available. Request permission to generate one.');
                    }
                })
                .catch((err) => {
                    console.log('An error occurred while retrieving token. ', err);
                });
            })
            .catch((err) => {
                console.log('Service Worker registration failed: ', err);
            });

        function sendTokenToServer(token) {
            const data = {
                user_id: user_id, 
                device_token: token
            };

            fetch('save-token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                console.log('Token saved successfully:', result);
            })
            .catch(error => {
                console.error('Error saving token:', error);
            });
        }
    </script>
    <script src="calcsugar.js"></script>
    <script src="script.js"></script>
    <script src="main.js"></script>

</body>
</html>