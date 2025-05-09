document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        
        // Toggle eye and eye-off icons
        const passwordIcon = document.getElementById('password_icon');
        if (passwordIcon) {
            passwordIcon.classList.toggle('icon-eye', !isPassword);
            passwordIcon.classList.toggle('icon-eye-off', isPassword);
        }
    });
    
    // Form validation
    const loginForm = document.getElementById('loginForm');
    
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        // Basic validation
        if (!email || !password) {
            alert('Please fill in all fields');
            return;
        }
        
        // Email format validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address');
            return;
        }
        
        // If validation passes, submit the form
        this.submit();
    });
    
    // Social login buttons
    document.getElementById('githubLogin').addEventListener('click', function() {
        // Redirect to GitHub OAuth login
        window.location.href = 'github_oauth.php';
    });
    
    document.getElementById('twitterLogin').addEventListener('click', function() {
        // Redirect to Twitter OAuth login
        window.location.href = 'twitter_oauth.php';
    });
});
