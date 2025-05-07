
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("contact-form");
    const statusMessage = document.createElement("div");
    statusMessage.style.marginTop = "1rem";
    form.after(statusMessage);
    
    form.addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent the default form submission
        
        // Show loading message
        statusMessage.textContent = "Sending email...";
        statusMessage.style.color = "blue";
        
        const params = {
            name: document.getElementById("name").value,
            email: document.getElementById("email").value,
            subject: document.getElementById("subject").value,
            message: document.getElementById("message").value,
            // Add any additional parameters your template might need
            to_email: "recipient@example.com" // Replace with recipient email if needed by your template
        };
        
        console.log("Sending email with params:", params);
        
        emailjs.send("service_0tu0drm", "template_jmn8h6h", params)
            .then(function(response) {
                console.log("SUCCESS!", response.status, response.text);
                statusMessage.textContent = "Email sent successfully!";
                statusMessage.style.color = "green";
                form.reset(); // Clear the form fields
            })
            .catch(function(error) {
                console.error("FAILED...", error);
                statusMessage.textContent = "Failed to send email. Error: " + error.text;
                statusMessage.style.color = "red";
            });
    });
});