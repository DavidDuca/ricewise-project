const checkBox = document.getElementById("check");
const navMenu = document.querySelector(".nav ul");

navMenu.classList.remove("show-nav");

checkBox.addEventListener("change", function() {
    if (this.checked) {
        navMenu.classList.add("show-nav");
    } else {
        navMenu.classList.remove("show-nav");
    }
});
