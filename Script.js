document.addEventListener("DOMContentLoaded", function () {
  // Toggle password visibility
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("password");

  togglePassword.addEventListener("click", function () {
    const isPassword = passwordInput.type === "password";
    passwordInput.type = isPassword ? "text" : "password";

    // Toggle eye and eye-off icons
    const passwordIcon = document.getElementById("password_icon");
    if (passwordIcon) {
      passwordIcon.classList.toggle("icon-eye", !isPassword);
      passwordIcon.classList.toggle("icon-eye-off", isPassword);
    }
  });

  // Form validation
  const loginForm = document.getElementById("loginForm");

  loginForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    // Basic validation
    if (!email || !password) {
      alert("Please fill in all fields");
      return;
    }

    // Email format validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert("Please enter a valid email address");
      return;
    }

    // If validation passes, submit the form
    this.submit();
  });

  // Social login buttons
  document.getElementById("githubLogin").addEventListener("click", function () {
    // Redirect to GitHub OAuth login
    window.location.href = "github_oauth.php";
  });

  document
    .getElementById("twitterLogin")
    .addEventListener("click", function () {
      // Redirect to Twitter OAuth login
      window.location.href = "twitter_oauth.php";
    });

  // Donation form handling - separate initialization
  if (document.getElementById("donationForm")) {
    const donationForm = document.getElementById("donationForm");
    const submitHandler = function (event) {
      event.preventDefault();
      const campaignIdInput = document.getElementById("campaignId");
      console.log(
        "Campaign ID:",
        campaignIdInput ? campaignIdInput.value : "not found"
      );

      const data = {
        donorName: document.getElementById("donorName").value,
        donorEmail: document.getElementById("donorEmail").value,
        donationAmount: document.getElementById("donationAmount").value,
        paymentMethod: document.querySelector(
          'input[name="paymentMethod"]:checked'
        )?.value,
        donationType: document.querySelector(
          'input[name="donationType"]:checked'
        )?.value,
        recurringFrequency:
          document.querySelector('input[name="recurringFrequency"]:checked')
            ?.value || null,
        campaignId: campaignIdInput ? campaignIdInput.value : null,
      };

      console.log("Sending donation data:", data);

      fetch("process_donation.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      })
        .then((response) => response.json())
        .then((result) => {
          console.log("Server response:", result);
          if (result.status === "success") {
            alert(result.message);
            donationForm.reset();
          } else {
            throw new Error(result.message || "Failed to process donation");
          }
        })
        .catch((error) => {
          console.error("Donation error:", error);
          alert(
            "Error: " +
              (error.message || "Failed to process donation. Please try again.")
          );
        });
    };

    donationForm.addEventListener("submit", submitHandler);

    // Initialize other donation form functionality
    paymentMethodSelection();
    donationTypeSelection();
  }
});

// event_details.js
function fetchEventDetails(eventId) {
  fetch(`get_event_details.php?event_id=${eventId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data) {
        document.getElementById("eventName").textContent = data.name;
        document.getElementById("eventDate").textContent = data.date;
        document.getElementById("eventTime").textContent = data.time;
        document.getElementById("eventLocation").textContent = data.location;
        document.getElementById("eventDescription").textContent =
          data.description;
        document.getElementById("eventOrganizer").textContent = data.organizer;

        // Add data attribute to the campaign button
        const campaignBtn = document.getElementById("campaignBtn");
        campaignBtn.setAttribute("data-campaign-id", data.campaign_id);
        // Event listener for campaign button
        campaignBtn.addEventListener("click", () => {
          const campaignId = campaignBtn.getAttribute("data-campaign-id");
          if (campaignId) {
            window.location.href = `campaign_details.html?campaign_id=${campaignId}`;
          }
        });
      } else {
        console.error("Event data not found.");
      }
    })
    .catch((error) => console.error("Error fetching event data:", error));
}

// event_page.js
const params = new URLSearchParams(window.location.search);
const campaignId = params.get("campaign_id");

const url = campaignId
  ? `event_data.php?json=1&campaign_id=${campaignId}`
  : `event_data.php?json=1`;

fetch(url)
  .then((response) => response.json())
  .then((events) => {
    const tbody = document.getElementById("eventTableBody");
    tbody.innerHTML = "";
    events.forEach((event) => {
      const row = document.createElement("tr");
      row.innerHTML = `
            <td>${event.id}</td>
            <td>${event.name}</td>
            <td>${event.description}</td>
            <td>${event.campaign_id}</td>
          `;
      tbody.appendChild(row);
    });
  })
  .catch((error) => {
    console.error("Error fetching event data:", error);
  });

// campaign_page.js
document.addEventListener("DOMContentLoaded", () => {
  fetch("campaign_data.php")
    .then((response) => response.json())
    .then((data) => {
      const campaignTableBody = document.getElementById("campaignTableBody");
      if (data && data.length > 0) {
        data.forEach((campaign) => {
          const row = campaignTableBody.insertRow();
          const idCell = row.insertCell();

          const nameCell = row.insertCell();
          const descriptionCell = row.insertCell();
          const progressCell = row.insertCell();
          idCell.textContent = campaign.id;
          nameCell.textContent = campaign.name;
          descriptionCell.textContent = campaign.description;
          progressCell.textContent = campaign.progress;
        });
      } else {
        campaignTableBody.innerHTML =
          '<tr><td colspan="4">No campaigns found.</td></tr>';
      }
      const showDonationFormButton =
        document.getElementById("showDonationForm");
      const donationFormContainer = document.getElementById(
        "donationFormContainer"
      );
      if (showDonationFormButton && donationFormContainer) {
        showDonationFormButton.addEventListener("click", () =>
          donationFormContainer.classList.remove("hidden")
        );
      }
    })
    .catch((error) => console.error("Error fetching campaigns:", error));
  // donation_form.js
  function sendDonation(event) {
    event.preventDefault();
    const campaignIdInput = document.getElementById("campaignId");
    const data = {
      donorName: document.getElementById("donorName").value,
      donorEmail: document.getElementById("donorEmail").value,
      donationAmount: document.getElementById("donationAmount").value,
      paymentMethod: document.querySelector(
        'input[name="paymentMethod"]:checked'
      )?.value,
      donationType: document.querySelector('input[name="donationType"]:checked')
        ?.value,
      recurringFrequency:
        document.querySelector('input[name="recurringFrequency"]:checked')
          ?.value || null,
      campaignId: campaignIdInput ? campaignIdInput.value : null,
      cardNumber: null,
      cardExpiry: null,
      cardCvv: null,
      paypalEmail: null,
      paypalPassword: null,
    };

    if (data.paymentMethod === "credit_card") {
      data.cardNumber = document.getElementById("cardNumber").value;
      data.cardExpiry = document.getElementById("cardExpiry").value;
      data.cardCvv = document.getElementById("cardCvv").value;
    } else if (data.paymentMethod === "paypal") {
      data.paypalEmail = document.getElementById("paypalEmail").value;
      data.paypalPassword = document.getElementById("paypalPassword").value;
    }

    // Send the data to the PHP backend via POST request
    fetch("donation.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then(async (response) => {
        const result = await response.json();
        if (response.ok && result.status === "success") {
          alert(result.message);
          document.getElementById("donationForm").reset();
        } else {
          throw new Error(result.message || "Failed to process donation");
        }
      })
      .catch((error) => {
        console.error("Donation error:", error);
        alert(
          "Error: " +
            (error.message || "Failed to process donation. Please try again.")
        );
      });
  }

  function displayErrorMessage(fieldId, message) {
    const fieldElement = document.getElementById(fieldId);
    if (fieldElement) {
      const errorMessage = document.createElement("div");
      errorMessage.classList.add("error-message");
      errorMessage.textContent = message;
      fieldElement.parentNode.insertBefore(
        errorMessage,
        fieldElement.nextSibling
      );
    }
  }

  function paymentMethodSelection() {
    const paymentRadioButtons = document.querySelectorAll(
      'input[name="paymentMethod"]'
    );
    const paypalFields = document.getElementById("paypalFields");
    const creditCardFields = document.getElementById("creditCardFields");

    if (paypalFields) paypalFields.style.display = "none";
    if (creditCardFields) creditCardFields.style.display = "none";

    paymentRadioButtons.forEach((radioButton) => {
      radioButton.addEventListener("change", function () {
        document.querySelectorAll(".payment-options label").forEach((label) => {
          label.classList.remove("selected-payment");
        });
        this.parentElement.classList.add("selected-payment");
        creditCardFields.style.display =
          this.value === "credit_card" ? "block" : "none";
        if (paypalFields)
          paypalFields.style.display =
            this.value === "paypal" ? "block" : "none";
      });
    });
  }

  function validateDonationForm(event) {
    event.preventDefault();

    // Remove all previous error messages
    document.querySelectorAll(".error-message").forEach((e) => e.remove());

    let isValid = true;

    const paymentMethod = document.querySelector(
      'input[name="paymentMethod"]:checked'
    );
    if (!paymentMethod) {
      displayErrorMessage("donationAmount", "Please select a payment method.");
      isValid = false;
    }

    if (paymentMethod) {
      if (paymentMethod.value === "credit_card") {
        const cardNumber = document.getElementById("cardNumber").value.trim();
        const cardExpiry = document.getElementById("cardExpiry").value.trim();
        const cardCvv = document.getElementById("cardCvv").value.trim();

        // Validate Card Number (exactly 16 digits)
        if (!/^[0-9]{16}$/.test(cardNumber)) {
          displayErrorMessage(
            "cardNumber",
            "Card Number must be exactly 16 digits"
          );
          isValid = false;
        }

        // Validate Expiry Date (MM/YY format)
        if (!/^(0[1-9]|1[0-2])\/[0-9]{2}$/.test(cardExpiry)) {
          displayErrorMessage(
            "cardExpiry",
            "Expiry date must be in MM/YY format"
          );
          isValid = false;
        }

        // Validate CVV (exactly 3 digits)
        if (!/^[0-9]{3}$/.test(cardCvv)) {
          displayErrorMessage("cardCvv", "CVV must be exactly 3 digits");
          isValid = false;
        }
      } else if (paymentMethod.value === "paypal") {
        if (document.getElementById("paypalEmail").value.trim().length < 1) {
          displayErrorMessage("paypalEmail", "PayPal Email is required");
          isValid = false;
        }
        if (document.getElementById("paypalPassword").value.trim().length < 1) {
          displayErrorMessage("paypalPassword", "PayPal Password is required");
          isValid = false;
        }
      }
    }

    const donorName = document.getElementById("donorName").value.trim();
    if (!donorName) {
      displayErrorMessage("donorName", "Name is required");
      isValid = false;
    }

    const donorEmail = document.getElementById("donorEmail").value.trim();
    if (!donorEmail) {
      displayErrorMessage("donorEmail", "Email is required");
      isValid = false;
    }

    const donationAmount = document
      .getElementById("donationAmount")
      .value.trim();
    if (!donationAmount) {
      displayErrorMessage("donationAmount", "Amount is required");
      isValid = false;
    }

    const donationType = document.querySelector(
      'input[name="donationType"]:checked'
    );
    if (!donationType) {
      displayErrorMessage("donationAmount", "Please select a donation type.");
      isValid = false;
    }

    if (isValid) {
      sendDonation(event);
    }
  }

  const donationForm = document.getElementById("donationForm");
  if (donationForm) {
    donationForm.addEventListener("submit", function (event) {
      event.preventDefault(); // Prevent any default form submission
      validateDonationForm(event);
    });
  }

  paymentMethodSelection();
  donationTypeSelection();

  if (document.getElementById("goToDonation")) {
    document.getElementById("goToDonation").addEventListener("click", () => {
      window.location.href = "donation_page.html";
    });
  }

  function donationTypeSelection() {
    const donationTypeRadios = document.querySelectorAll(
      'input[name="donationType"]'
    );
    const recurringOptions = document.getElementById("recurringOptions");

    if (recurringOptions) recurringOptions.style.display = "none";

    donationTypeRadios.forEach((radioButton) => {
      radioButton.addEventListener("change", function () {
        document
          .querySelectorAll(".donation-type-options label")
          .forEach((label) => {
            label.classList.remove("selected-donation-type");
          });
        this.parentElement.classList.add("selected-donation-type");

        recurringOptions.style.display =
          this.value === "recurring" ? "block" : "none";
      });
    });
  }
});
