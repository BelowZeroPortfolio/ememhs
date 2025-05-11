function confirmPassword() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const registerButton = document.getElementById('register');
    const passwordMatchStatus = document.getElementById('password_match_status');
    // Only check if both fields have values
    if (password && confirmPassword) {
        if (password !== confirmPassword) {
            document.getElementById('confirm_password').style.border = '2px solid red';
            registerButton.disabled = true;
            registerButton.style.opacity = '0.5'; // Visual feedback
            registerButton.title = 'Passwords do not match'; // Tooltip message
            passwordMatchStatus.textContent = 'Passwords do not match';
            passwordMatchStatus.style.color = 'red';
        } else {
            document.getElementById('confirm_password').style.border = '2px solid green';
            registerButton.disabled = false;
            registerButton.style.opacity = '1';
            registerButton.title = ''; // Remove tooltip
            passwordMatchStatus.textContent = '';
            passwordMatchStatus.textContent = 'Passwords matched';
            passwordMatchStatus.style.color = 'green';
        }
    } else {
        // Reset states if either field is empty
        document.getElementById('confirm_password').style.border = '1px solid #d1d5db';
        registerButton.disabled = false;
        registerButton.style.opacity = '1';
        registerButton.title = '';
        passwordMatchStatus.textContent = '';
    }
}

function validateEmail() {
    const email = document.getElementById('email').value;
    const emailStatus = document.getElementById('email_status');
    const sendCodeButton = document.getElementById('getCode');
    const gmailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

    if (!gmailRegex.test(email)) {
        // If email is NOT a valid Gmail address
        emailStatus.style.color = 'red';
        emailStatus.innerHTML = 'Please enter a valid Gmail address (example@gmail.com)';
        sendCodeButton.disabled = true;
        return; // Stop here, don't send AJAX
    }

    // Create AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../logic/ajaxLogic/check_email_if_exist.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // What to do when response is received
    xhr.onload = function() {
        if (this.status === 200) {
            console.log("this.responseText", this.responseText);
            if (this.responseText === "1") {
                document.getElementById('email_status').innerHTML = '';
                sendCodeButton.disabled = false;
            }else if (this.responseText === "0"){
                sendCodeButton.disabled = true;
                document.getElementById('email_status').style.color = 'red';
                document.getElementById('email_status').innerHTML = 'Email is already taken';
            }
            
        }
    };

    // Send the data
    xhr.send('email=' + encodeURIComponent(email));
}

function validateCode() {
    const code = document.getElementById('code').value;
    const realCode = localStorage.getItem("code");
    const codeStatus = document.getElementById('code_status');
    const registerButton = document.getElementById('register');
    if (code) {
        if (realCode !== code) {
            document.getElementById('code').style.border = '2px solid red';
            registerButton.disabled = true;
            registerButton.style.opacity = '0.5'; // Visual feedback
            registerButton.title = 'verification code is incorrect'; // Tooltip message
            codeStatus.textContent = 'verification code is incorrect';
            codeStatus.style.color = 'red';
        } else {
            document.getElementById('code').style.border = '2px solid green';
            registerButton.disabled = false;
            registerButton.style.opacity = '1';
            registerButton.title = ''; // Remove tooltip
            codeStatus.textContent = '';
            codeStatus.textContent = 'verification code is correct';
            codeStatus.style.color = 'green';
        }
    } else {
        // Reset states if either field is empty
        document.getElementById('code').style.border = '1px solid #d1d5db';
        registerButton.disabled = false;
        registerButton.style.opacity = '1';
        registerButton.title = '';
        codeStatus.textContent = '';
    }
}
function executeSendCode() {
    const email = document.getElementById('email').value;
    const verificationCode = Math.floor(100000 + Math.random() * 900000);
    sendCode(email, verificationCode);
}
// Function to send the verification code via email
function sendCode(email, code) {
    var params = {
        sendername: "Guidance System",
        to: email,
        subject: "Email Confirmation Code",
        replyto: "noreply@example.com",
        message: "Your email verification code: " + code,
    };

    emailjs.send("service_8jh4949", "template_gr1vonw", params)
        .then(function (response) {
            localStorage.setItem('code', code);
            alert('Email sent successfully!', response.status, response.text);
        }, function (error) {
            alert('Failed to send email:', error);
        });
}

emailjs.init("GRi35_90k4gj9Es_f");