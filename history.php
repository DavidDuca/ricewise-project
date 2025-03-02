<?php
session_start();
if (isset($_SESSION['user_id'])) {
    echo "<script>const userId = " . $_SESSION['user_id'] . ";</script>";
} else {
    echo "<script>const userId = null;</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rice Intake History</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
    <link rel="stylesheet" href="history.css">
    <link rel="icon" href="logo.png">
    <style>
        .selected-date {
            background-color: #D8BFD8 !important;
            border-radius: 5px;
        }
        .fc-header-toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 6px;
        }

        .fc-button {
            padding: 6px 12px !important; 
            font-size: 14px !important; 
            min-width: 60px; 
        }

        .fc-toolbar-title {
            font-size: 1.75rem !important; 
            text-align: center;
            flex-grow: 1;
        }
    </style>
</head>
<body class="bg-gray-100 m-4">
<div class="nav-bar">
            <input type="checkbox" id="check">
            <label for="check" class="checkbtn">
                <i class="fa-solid fa-bars" style="font-size: 20px;"></i>
            </label>
            <div class="logo">
            <i class="fa-solid fa-user" style="font-size: 20px; margin:10px;"></i>
            <h3 class="hi">Hi, <?php echo $_SESSION['username']; ?>!</h3>
            </div>
            <div class="nav">
                <ul class="show-nav" >
                    <li ><a href="user_dashboard.php">DASHBOARD</a></li>
                    <li class="active" > <a href="history.php">HISTORY</a> </li>
                    <li> <a href="#">ABOUT</a> </li>
                    <li> <a href="#">DEVELOPER</a> </li>
                    <li> <a href="logout.php">LOG OUT</a> </li>
                </ul>
            </div>
            <div class="logout">
                <button class="logout-btn">SERVER STATUS</button>
                <p id="statusText" ></p>
                <div id="online" class="online">

                </div>
            </div>
        </div>

    <h1 class="text-2xl font-bold text-center text-gray-800 mb-3 mt-6">Rice & Sugar Intake History</h1>

    <div class="flex flex-col lg:flex-row gap-6">
        <div class="w-full lg:w-1/3 bg-white rounded-lg shadow-lg p-4">
            <div id="calendar"></div>
        </div>

        <div id="history-details" class="w-full lg:w-2/3 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Daily Intake Details - <span id="selected-date"></span>
            </h2>
            <div id="history-content" class="space-y-4">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const historyContentEl = document.getElementById('history-content');
            const selectedDateEl = document.getElementById('selected-date');
            
            let today = new Date().toISOString().split('T')[0]; 
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                dateClick: function(info) {
                    document.querySelectorAll('.fc-daygrid-day').forEach(day => {
                        day.classList.remove('selected-date');
                    });

                    let clickedDay = document.querySelector(`[data-date="${info.dateStr}"]`);
                    if (clickedDay) {
                        clickedDay.classList.add('selected-date');
                    }

                    selectedDateEl.textContent = info.dateStr;
                    fetchHistory(info.dateStr);
                },
                themeSystem: 'standard',
                height: 'auto'
            });

            calendar.render();

            setTimeout(() => {
                let todayCell = document.querySelector(`[data-date="${today}"]`);
                if (todayCell) todayCell.classList.add('selected-date');
                selectedDateEl.textContent = today;
                fetchHistory(today);
            }, 500);

            function fetchHistory(date) {
                fetch(`get_history.php?date=${date}&user_id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        displayHistory(data);
                    })
                    .catch(error => console.error('Error fetching history:', error));
            }

            function displayHistory(data) {
                if (data.length === 0) {
                    historyContentEl.innerHTML = '<p class="text-gray-600">No data found for this date.</p>';
                    return;
                }

                let breakfast = data.filter(entry => entry.meal_time === 'breakfast');
                let lunch = data.filter(entry => entry.meal_time === 'lunch');
                let dinner = data.filter(entry => entry.meal_time === 'dinner');
                let totalSugar = data.reduce((sum, entry) => sum + parseFloat(entry.sugar_amount), 0);

                let html = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Breakfast</h3>
                            ${breakfast.length > 0 ? breakfast.map(entry => `
                                <p class="text-gray-700"><span class="font-medium">Rice:</span> ${entry.rice_cups} cups (${entry.rice_type})</p>
                                <p class="text-gray-700"><span class="font-medium">Sugar:</span> ${entry.sugar_amount}g</p>
                            `).join('') : '<p class="text-gray-600">No data.</p>'}
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Lunch</h3>
                            ${lunch.length > 0 ? lunch.map(entry => `
                                <p class="text-gray-700"><span class="font-medium">Rice:</span> ${entry.rice_cups} cups (${entry.rice_type})</p>
                                <p class="text-gray-700"><span class="font-medium">Sugar:</span> ${entry.sugar_amount}g</p>
                            `).join('') : '<p class="text-gray-600">No data.</p>'}
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Dinner</h3>
                            ${dinner.length > 0 ? dinner.map(entry => `
                                <p class="text-gray-700"><span class="font-medium">Rice:</span> ${entry.rice_cups} cups (${entry.rice_type})</p>
                                <p class="text-gray-700"><span class="font-medium">Sugar:</span> ${entry.sugar_amount}g</p>
                            `).join('') : '<p class="text-gray-600">No data.</p>'}
                        </div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg mt-4">
                        <h3 class="text-xl font-semibold text-blue-800">Total Sugar Intake</h3>
                        <p class="text-blue-700">${totalSugar.toFixed(2)}g</p>
                    </div>
                `;

                historyContentEl.innerHTML = html;
            }
        });
    </script>
    <script src="main.js"></script>
</body>
</html>
