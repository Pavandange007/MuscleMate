// Get the links
const bmiLink = document.querySelector('[data-href="bmi.html"]');
const workoutLink = document.querySelector('[data-href="workout-vidoes.html"]');
const shopLink = document.querySelector('[data-href="shop.html"]');

// Add event listeners to each link
bmiLink.addEventListener('click', function() {
  window.location.href = 'bmi.html';
});

workoutLink.addEventListener('click', function() {
  window.location.href = 'workout-vidoes.html';
});

shopLink.addEventListener('click', function() {
  window.location.href = 'shop.html';
});