document.addEventListener('DOMContentLoaded', function() {
    // Example: Alert when user clicks on BMI
    const bmiElement = document.getElementById('bmi');
    bmiElement.addEventListener('click', function() {
        alert('Your BMI is ' + bmiElement.innerText);
    });
});