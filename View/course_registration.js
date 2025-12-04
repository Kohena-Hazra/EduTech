document.getElementById('registrationForm').addEventListener('submit', function(e) {
    let phone = document.getElementById('phone').value;
    if(!/^\d{10}$/.test(phone)) {
        alert("Please enter a valid 10-digit phone number.");
        e.preventDefault();
    }
});
