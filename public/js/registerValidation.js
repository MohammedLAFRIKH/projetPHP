document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const emailError = document.getElementById('email_error');
    const passwordMatchError = document.getElementById('password_match_error');
    const validation = document.getElementById('validation');
    const registerForm = document.getElementById('registerForm');
    validation.style.display = 'none'; // Show the block element

    emailInput.addEventListener('input', function () {
        checkEmailValidity(this.value);
    });

    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    async function checkEmailValidity(email) {
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (!emailPattern.test(email)) {
            emailError.textContent = "Veuillez saisir une adresse e-mail valide.";
            return;
        }

        const baseUrl = window.location.origin;
        const checkEmailUrl = `${baseUrl}/apogee_ens/check-email`;

        try {
            const response = await fetch(checkEmailUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email }),
            });

            if (!response.ok) {
                console.error('Network response was not ok:', response);
                throw new Error('Network response was not ok');
            }

            const dataText = await response.text();

            console.log('Raw response data:', dataText);

            // Handle empty response
            if (dataText.trim() === '') {
                console.error('Empty response data.');
                return;
            }

            handleEmailValidationResponse(dataText);
        } catch (error) {
            console.error('Error during email existence check:', error);
        }
    }

    function handleEmailValidationResponse(dataText) {
        try {
            const data = JSON.parse(dataText);

            // Handle the case where data.exists is undefined or not a boolean
            const exists = data.exists === true;

            console.log('Parsed JSON data:', exists);

            if (exists) {
                emailError.textContent = "Cette adresse e-mail est déjà utilisée.";
            } else {
                emailError.textContent = "";
            }
        } catch (jsonError) {
            console.error('Error parsing JSON data:', jsonError);
        }
    }

    function checkPasswordMatch() {
        var password = passwordInput.value;
        var confirmPassword = confirmPasswordInput.value;

        if (password !== confirmPassword) {
            passwordMatchError.textContent = "Les mots de passe ne correspondent pas.";
        } else {
            passwordMatchError.textContent = "";
        }
    }

    async function handleFormSubmit(event) {
        event.preventDefault(); // Prevent the default form submission
    
        // Check conditions before submitting
        if (emailError.textContent || passwordMatchError.textContent) {
            validation.innerText = 'Veuillez corriger les erreurs avant de soumettre le formulaire.';
            return;
        }
    
        // Get form data
        const formData = new FormData(registerForm);
    
        // Send data to the controller using fetch
        try {
            const response = await fetch('/apogee_ens/register/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                },
                body: new URLSearchParams(formData),
            });
    
            // Check if response status is OK (200)
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
    
            // Get the response body as text
            const dataText = await response.text();
    
            // Check if the response body is not empty
            if (dataText.trim() === '') {
                console.error('Empty response data.');
                return;
            }
    
            // Parse the JSON data
            const data = JSON.parse(dataText);
    
            // Handle the response
            handleResponse(data);
        } catch (error) {
            handleError(error);
        }
    }
    

    function handleResponse(response) {
        // Handle the success response from the server
       // Assuming you have an HTML element with id "validationBlock"

if (response.success) {
    validation.innerText = response.message;
    validation.style.display = 'block'; // Show the block element
    // Optionally, you can redirect the user or perform other actions
} else {
    // Display error messages or handle other scenarios
    validation.innerText = response.message;
    validation.style.display = 'block'; // Show the block element
}

    }

    function handleError(error) {
        // Handle errors
        console.error('Error:', error);
        validation.innerText = 'Une erreur s\'est produite lors du traitement du formulaire.';
    }

    if (registerForm) {
        registerForm.addEventListener('submit', handleFormSubmit);
    }
});
