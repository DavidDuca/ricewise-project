const startButton = document.getElementById('startButton');
const authButtons = document.getElementById('authButtons');
const signUpButton = document.getElementById('signUpButton');
const loginButton = document.getElementById('loginButton');

startButton.addEventListener('click', () => {
    startButton.style.display = 'none';
    authButtons.style.display = 'flex';
});

signUpButton.addEventListener('click', () => {
    window.location.href = 'signup.html';
});

loginButton.addEventListener('click', () => {
    window.location.href = 'login.html';
});

