// document.addEventListener('DOMContentLoaded', function() {
//     // Toggle password visibility
//     const togglePassword = document.getElementById('togglePassword');
//     const passwordInput = document.getElementById('password');
    
//     togglePassword.addEventListener('click', function() {
//         const isPassword = passwordInput.type === 'password';
//         passwordInput.type = isPassword ? 'text' : 'password';
        
//         // Toggle eye and eye-off icons
//         const passwordIcon = document.getElementById('password_icon');
//         if (passwordIcon) {
//             passwordIcon.classList.toggle('icon-eye', !isPassword);
//             passwordIcon.classList.toggle('icon-eye-off', isPassword);
//         }
//     });
    
//     // Form validation
//     const loginForm = document.getElementById('loginForm');
    
//     loginForm.addEventListener('submit', function(event) {
//         event.preventDefault();
        
//         const email = document.getElementById('email').value;
//         const password = document.getElementById('password').value;
        
//         // Basic validation
//         if (!email || !password) {
//             alert('Please fill in all fields');
//             return;
//         }
        
//         // Email format validation
//         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//         if (!emailRegex.test(email)) {
//             alert('Please enter a valid email address');
//             return;
//         }
        
//         // If validation passes, submit the form
//         this.submit();
//     });
    
//     // Social login buttons
//     document.getElementById('githubLogin').addEventListener('click', function() {
//         // Redirect to GitHub OAuth login
//         window.location.href = 'github_oauth.php';
//     });
    
//     document.getElementById('twitterLogin').addEventListener('click', function() {
//         // Redirect to Twitter OAuth login
//         window.location.href = 'twitter_oauth.php';
//     });
// });



document.addEventListener('DOMContentLoaded', function () {
    // Reusable password visibility toggle
    function createToggleIcon(inputElement) {
        if (!inputElement) return;
        const toggleBtn = document.createElement('span');
        toggleBtn.className = 'icon-eye';
        toggleBtn.style.cursor = 'pointer';
        toggleBtn.style.marginLeft = '8px';

        toggleBtn.addEventListener('click', function () {
            const isPassword = inputElement.type === 'password';
            inputElement.type = isPassword ? 'text' : 'password';
            toggleBtn.className = isPassword ? 'icon-eye-off' : 'icon-eye';
        });

        inputElement.parentElement.appendChild(toggleBtn);
    }

    // Apply toggle icons if password fields exist
    const passwordInput = document.getElementById('password');
    createToggleIcon(passwordInput);

    const confirmPasswordInput = document.getElementById('confirm_password');
    createToggleIcon(confirmPasswordInput);

    // Form detection (register or login)
    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');
    const form = registerForm || loginForm;

    if (form) {
        form.addEventListener('submit', function (event) {
            const email = document.getElementById('email')?.value.trim();
            const password = passwordInput?.value.trim();
            const confirmPassword = confirmPasswordInput?.value.trim();
            const phone = document.getElementById('phone')?.value.trim();

            // Email format validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                event.preventDefault();
                return;
            }

            // If on register page: validate phone, confirm password, password length
            if (registerForm) {
                const phoneRegex = /^[+0-9]{10,15}$/;
                if (!phoneRegex.test(phone)) {
                    alert('Please enter a valid phone number.');
                    event.preventDefault();
                    return;
                }

                if (password !== confirmPassword) {
                    alert('Passwords do not match.');
                    event.preventDefault();
                    return;
                }

                if (password.length < 6) {
                    alert('Password should be at least 6 characters.');
                    event.preventDefault();
                    return;
                }
            }
        });
    }

    // Social login buttons
    const twitterBtn = document.querySelector('.icon-twitter');
    if (twitterBtn) {
        twitterBtn.closest('button').addEventListener('click', function () {
            window.location.href = 'twitter_oauth.php';
        });
    }

    const facebookBtn = document.querySelector('.icon-facebook');
    if (facebookBtn) {
        facebookBtn.closest('button').addEventListener('click', function () {
            window.location.href = 'facebook_oauth.php';
        });
    }
});
