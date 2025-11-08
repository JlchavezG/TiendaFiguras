// js/login.js (solo transición)
document.addEventListener('DOMContentLoaded', () => {
  const loginPanel = document.getElementById('loginPanel');
  const registerPanel = document.getElementById('registerPanel');
  const goToRegister = document.getElementById('goToRegister');
  const goToLogin = document.getElementById('goToLogin');

  goToRegister.addEventListener('click', (e) => {
    e.preventDefault();
    loginPanel.classList.remove('active');
    setTimeout(() => registerPanel.classList.add('active'), 300);
  });

  goToLogin.addEventListener('click', (e) => {
    e.preventDefault();
    registerPanel.classList.remove('active');
    setTimeout(() => loginPanel.classList.add('active'), 300);
  });
});