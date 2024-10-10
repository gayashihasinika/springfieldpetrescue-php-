<?php
session_start();
require_once 'utils/connect.php'; // Make sure the path to this file is correct

$response = [
    'success' => false,
    'message' => 'Failed to save details'
];

try {
    // Make sure these variables ($host, $dbname, $username, $password) are defined correctly in 'connect.php'
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO donations (first_name, last_name, email, phone_number, message, donation_amount) 
            VALUES (:first_name, :last_name, :email, :phone_number, :message, :donation_amount)");

        // Bind parameters
        $stmt->bindParam(':first_name', $_POST['firstName']);
        $stmt->bindParam(':last_name', $_POST['lastName']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':phone_number', $_POST['phoneNumber']);
        $stmt->bindParam(':message', $_POST['message']);
        $stmt->bindParam(':donation_amount', $_POST['donation_amount']);

        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Donation details saved successfully';
        } else {
            $response['message'] = 'Failed to execute the query';
        }
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

// Return the JSON response
echo json_encode($response);
?>

















<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Springfield Pet Rescue and Rehome</title>
    <link rel="stylesheet" href="css/howtodonate.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert Library -->
    <script defer>
        document.addEventListener("DOMContentLoaded", () => {
    const amountButtons = document.querySelectorAll(".amount-buttons button");
    const customAmountInput = document.querySelector(".custom-amount");
    const donationAmountDisplay = document.querySelector(".donation-form h3");
    const donationForm = document.getElementById("donationForm");
    const personalFormSection = document.querySelector(".donation-form");
    const paymentFormSection = document.querySelector(".payment-details-section");

    // Function to update the donation amount
    const updateDonationAmount = (amount) => {
        donationAmountDisplay.textContent = `My donation amount: Rs.${amount}.00`;
    };

    // Add event listeners to each button
    amountButtons.forEach((button) => {
        button.addEventListener("click", () => {
            amountButtons.forEach(btn => btn.classList.remove("selected"));
            button.classList.add("selected");
            updateDonationAmount(button.textContent.replace("Rs.", "").trim());
        });
    });

    // Update donation amount based on custom input
    customAmountInput.addEventListener("input", () => {
        const customAmount = customAmountInput.value;
        if (customAmount && !isNaN(customAmount)) {
            updateDonationAmount(customAmount);
            amountButtons.forEach(btn => btn.classList.remove("selected"));
        }
    });

    // Handle form submission
    donationForm.addEventListener("submit", (event) => {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(donationForm);

        // Send form data using Fetch API
        fetch('process_donation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Display success pop-up
                Swal.fire({
                    title: 'Success!',
                    text: 'Your details have been submitted successfully!',
                    icon: 'success',
                    confirmButtonText: 'Proceed to Payment'
                }).then(() => {
                    personalFormSection.style.display = 'none';
                    paymentFormSection.style.display = 'block';
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'There was a problem submitting your details. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });
});

    </script>
</head>
<body>
    <div class="container">
        <div class="top">
            <img class="imgl" src="images/1.jpg" alt="Springfield Pet Rescue">
            <h1>Springfield Pet Rescue and Rehome</h1>
        </div>
    </div>
    <div class="menu">
        <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="howtodonate.php">Donate</a></li>
        <li class="dropdown">
            <a href="#" class="dropbtn">Adopt</a>
            <div class="dropdown-content">
                <a href="availablepets.php">Available Pets</a>
                <a href="successstories.php">Success Stories</a>
                <a href="adoptionrules.php">Adoption Rules</a>
            </div>
        </li>
        <li><a href="petfacts.php">Pet Facts</a></li>
        <li class="dropdown">
            <a href="kidszone.php" class="dropbtn">Kids Zone</a>
            <div class="dropdown-content">
                <a href="games.php">Games and Activities</a>
                <a href="tips.php">Pet Care Tips for Kids</a>
            </div>
        </li>
        </ul>
    </div>

    <div class="donation-header">
        <div class="impact-stories-section">
            <h2>How<br>Your<br>Donation<br>Helps</h2>
            <div class="stories-container">
                <div class="story-card">
                    <img class="story-image" src="images/dog1.jpg" alt="Rex">
                    <div class="story-content">
                        <h3>Rex's Story</h3>
                        <p>Rex was found injured on the streets and brought to our shelter. Thanks to generous donations, we were able to provide him with the medical care he needed. Today, Rex is healthy and has found his forever home.</p>
                    </div>
                </div>
                <div class="story-card">
                    <img class="story-image" src="images/cat1.webp" alt="Luna">
                    <div class="story-content">
                        <h3>Luna's Journey</h3>
                        <p>Luna was abandoned as a kitten and was very timid. Your donations helped us give Luna a safe place to grow, socialize, and find a loving family that adores her.</p>
                    </div>
                </div>
                <div class="story-card">
                    <img class="story-image" src="images/dog2.jpg" alt="Volunteer Impact">
                    <div class="story-content">
                        <h3>Volunteer Impact</h3>
                        <p>Our volunteers play a vital role in the well-being of our animals. Donations support our volunteer programs, allowing us to train and equip more people to care for pets in need.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="donation-options">
            <h2>Get started by selecting an amount below</h2>
            <p>All donations over Rs.100.00 are tax deductible</p>
            <div class="amount-buttons">
                <button class="selected">Rs.200.00</button>
                <button>Rs.500.00</button>
                <button>Rs.800.00</button>
                <button>Rs.1000.00</button>
                <input type="number" placeholder="Choose your own amount" class="custom-amount">
            </div>

            <div class="donation-form">
                <h3>My donation amount: Rs.800.00</h3>
                <form id="donationForm">
                    <div class="form-group">
                        <label for="firstName">First name</label>
                        <input type="text" placeholder="First name" name="firstName" id="firstName" required minlength="2" maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last name</label>
                        <input type="text" placeholder="Last name" name="lastName" id="lastName" required minlength="2" maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" placeholder="Email" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Phone number</label>
                        <input type="tel" placeholder="070 000 0000" name="phoneNumber" id="phoneNumber">
                    </div>
                    <div class="form-group">
                        <label for="message">Send a message to the PetRescue team</label>
                        <textarea placeholder="E.g., Tell us why donating to help pets is important to you." name="message" id="message" minlength="10"></textarea>
                    </div>
                    <button type="submit" class="donate-button">Donate Now</button>
                </form>
            </div>
        </div>

        <div class="payment-details-section card">
            <button class="back-button">
                <img src="images/images.jfif" alt="Back">
            </button>
            <h2>Payment Details</h2>
            <form class="payment-details-form">
                <div class="form-group">
                    <label for="card-number">Card Number</label>
                    <input type="text" id="card-number" name="cardNumber" placeholder="1234 5678 9123 4567" required>
                </div>
                <div class="form-group">
                    <label for="expiry-date">Expiry Date</label>
                    <input type="text" id="expiry-date" name="expiryDate" placeholder="MM/YY" required>
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" required>
                </div>
                <div class="form-group">
                    <label for="billing-address">Billing Address</label>
                    <input type="text" id="billing-address" name="billingAddress" placeholder="123 Main St, City, Country" required>
                </div>
                <button type="submit" class="payment-button">Complete Payment</button>
            </form>
        </div>

        <div class="donation-faqs-section">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-container">
                <div class="faq-item">
                    <h3>Is my donation tax-deductible?</h3>
                    <p>Yes, all donations are tax-deductible. You will receive a receipt via email that you can use for tax purposes.</p>
                </div>
                <div class="faq-item">
                    <h3>How is my donation used?</h3>
                    <p>Your donation is used to cover medical expenses, food, shelter, and other essential needs for the animals in our care. We ensure that every cent goes towards helping the pets.</p>
                </div>
                <div class="faq-item">
                    <h3>Can I cancel my recurring donation?</h3>
                    <p>Yes, you can cancel your recurring donation at any time by contacting our support team or through your account dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
