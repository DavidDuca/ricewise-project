function calcsugar() {
    let cupsInput = document.getElementById("rice_cups");
    let riceDropdown = document.querySelector(".menu1");
    let sugarOutput = document.getElementById("sugar1");

    let cups = parseFloat(cupsInput.value) || 0;
    let selectedType = riceDropdown.value;
    let sugarValue = 0;

    switch (selectedType) {
        case "Jasmine Rice": sugarValue = 0.2; break;
        case "Basmati Rice": sugarValue = 0.3; break;
        case "White Rice": sugarValue = 0.4; break;
        case "Brown Rice": sugarValue = 0.5; break;
        case "Sticky Rice": sugarValue = 0.6; break;
        default: sugarValue = 0;
    }

    let totalSugar = sugarValue * cups;
    sugarOutput.value = totalSugar.toFixed(2) + " G of Sugar";
}

document.getElementById("saveButton").addEventListener("click", function(event) {
    let sugarOutput = document.getElementById("sugar1");
    let warningMessage = document.getElementById("warningMessage");

    if (!sugarOutput.value.trim() || parseFloat(sugarOutput.value) <= 0) { 
        event.preventDefault();
        warningMessage.style.display = "block"; 
    } else {
        warningMessage.style.display = "none"; 
    }
});

document.addEventListener("DOMContentLoaded", function () {
    fetch("get_sugar_intake.php")
        .then(response => response.json()) 
        .then(data => {
            console.log(data);
            if (Array.isArray(data)) {
                let totalSugar = data.reduce((sum, value) => sum + parseFloat(value), 0);
                document.getElementById("totalSugar").value = totalSugar.toFixed(2) + "g";

                let progressBar = document.getElementById("progressBar");
                progressBar.style.width = (totalSugar * 2) + "%";
                progressBar.style.background = getColor(totalSugar);

                if (totalSugar >= 25) {
                    alert('Warning: You have reached 25g of sugar intake!');
                }

                function getColor(value) {
                    if (value <= 15) {
                        return `rgb(${Math.floor(255 * (value / 15))}, 255, 0)`; 
                    } else if (value <= 30) {
                        return `rgb(255, ${Math.floor(255 * ((30 - value) / 15))}, 0)`;
                    } else {
                        return `rgb(255, 0, ${Math.floor(255 * ((50 - value) / 20))})`;
                    }
                }

            } else {
                console.error("Invalid JSON data received");
            }
        })
        .catch(error => console.error("Error fetching sugar intake:", error));
});
