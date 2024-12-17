<?php
session_start();
require_once 'config.php';

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}

// Function to handle purchase attempt
function handlePurchase($productId) {
    if (!isLoggedIn()) {
        return json_encode([
            'success' => false,
            'redirect' => 'login.php',
            'message' => 'Please log in to make a purchase'
        ]);
    }
    
    return json_encode([
        'success' => true,
        'message' => 'Purchase successful'
    ]);
}

// Handle AJAX requests
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'check_auth') {
        echo json_encode(['isLoggedIn' => isLoggedIn()]);
        exit;
    } elseif ($_POST['action'] === 'purchase') {
        echo handlePurchase($_POST['productId']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Information</title>
    <style>
        .go-back-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #34db6e;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 1em;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}

.go-back-button:hover {
    background-color: #34495e;
}

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #88a4bd ;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            animation: fadeInDown 0.8s ease-out;
        }
        .product-container {
            display: flex;
            gap: 30px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            opacity: 0;
            animation: fadeIn 1s ease-out 0.5s forwards;
        }
        .product-image {
            max-width: 40%;
            height: auto;
            border-radius: 5px;
            transition: transform 0.3s ease;
        }
        .product-image:hover {
            transform: scale(1.05);
        }
        .product-details {
            flex: 1;
        }
        .product-name {
            color: #6b8aa9;
            margin-top: 0;
        }
        .product-description {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }
        .product-description:hover {
            background-color: #e9e9e9;
        }
        .product-price {
            font-size: 1.2em;
            font-weight: bold;
            color: #1b1312;
            margin-bottom: 20px;
        }
        .buy-button {
            display: inline-block;
            background-color: #34db6e;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .buy-button:hover {
            background-color: #29b997;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .buy-button:active {
            transform: translateY(0);
            box-shadow: none;
        }
        .buy-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        .buy-button:hover::after {
            animation: ripple 1s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 1;
            }
            20% {
                transform: scale(25, 25);
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: scale(40, 40);
            }
        }
    </style>
</head>
<body>
    <h1 id="productTitle">Product Information</h1>
    <div class="product-container">
        <img id="productImage" class="product-image" src="" alt="Product Image">
        <div class="product-details">
            <h2 id="productName" class="product-name"></h2>
            <div id="productDescription" class="product-description"></div>
            <p class="product-price">Price: <span id="productPrice"></span></p>
            <a href="#" class="buy-button" id="buyButton">Buy Now</a>
        </div>
    </div>
    <a href="shop.html" class="go-back-button">Go Back to Shop</a>


    <script>
        const products = {
            1: {
                name: "Musclemate Whey Protein",
                image: "images/whey.jpg",
                description: "Whey protein is a type of protein that is derived from milk, specifically from the watery portion that separates from the curds during cheese production. It is a popular supplement among athletes and fitness enthusiasts due to its high protein content and ability to promote.",
                price: "RS.4500"
            },
            2: {
                name: "Creatine Monohydrate",
                image: "images/pic03.jpeg",
                description: "MuscleMate Creatine Monohydrate is a pure and effective supplement that helps increase muscle mass, strength, and endurance. It provides powerful support for lean muscle, energy, and cognition, and is easy to incorporate into your daily routine. With its high-quality formula, MuscleMate Creatine Monohydrate is a great choice for athletes and fitness enthusiasts looking to take their performance to the next leve.",
                price: "RS.4500"
            },
            3: {
                name: "Pre Workout Lemon Lime Flavour",
                image: "images/pre.jpg",
                description: "Power up your workout with our zesty Lemon Lime pre-workout! This refreshing drink boosts energy, focus, and endurance to help you push harder and go further. With a burst of citrus flavor, it’s the perfect fuel to get you through intense training sessions.",
                price: "RS.1350"
            }
        };

        function getProductIdFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('productId');
        }

        function displayProductInfo(productId) {
            const product = products[productId];
            if (product) {
                document.title = `${product.name} - Product Information`;
                document.getElementById('productTitle').textContent = "Product Information";
                document.getElementById('productName').textContent = product.name;
                document.getElementById('productImage').src = product.image;
                document.getElementById('productImage').alt = product.name;
                document.getElementById('productDescription').textContent = product.description;
                document.getElementById('productPrice').textContent = product.price;
                document.getElementById('buyButton').href = `#buy-${productId}`;
            } else {
                document.getElementById('productTitle').textContent = "Product Not Found";
                document.getElementById('productDescription').textContent = "Sorry, the requested product could not be found.";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const productId = getProductIdFromUrl();
            if (productId) {
                displayProductInfo(productId);
            } else {
                document.getElementById('productTitle').textContent = "No Product Selected";
                document.getElementById('productDescription').textContent = "Please select a product from the shop page.";
            }
            document.getElementById('buyButton').addEventListener('click', function(e) {
                e.preventDefault();
                
                const productId = getProductIdFromUrl();
                
                
                fetch(window.location.pathname, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=check_auth'
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.isLoggedIn) {
                        sessionStorage.setItem('redirectUrl', window.location.href);
                        window.location.href = 'login.php';
                    } else {
                        processPurchase(productId);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });

            function processPurchase(productId) {
                // Use the current page URL for the fetch request
                fetch(window.location.pathname, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=purchase&productId=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // You can redirect to checkout or order confirmation page here
                        // window.location.href = 'checkout.php';
                    } else if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        });
    </script>
</body>
</html>
       