<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ebookhub";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define upload directory and create it if it doesn't exist
$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Consider more restrictive permissions in production
}

$message = ""; // Initialize message variable

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['title'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $condition = $conn->real_escape_string($_POST['condition']);
    $price = floatval($_POST['price']);

    $imageName = "";
    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
        $imageTmp = $_FILES['book_image']['tmp_name'];
        $originalImageName = basename($_FILES['book_image']['name']);
        
        // Sanitize original image name to prevent issues (e.g., spaces, special chars)
        $sanitizedImageName = preg_replace("/[^a-zA-Z0-9.\-_]/", "", $originalImageName);
        $imageName = time() . "_" . $sanitizedImageName; // Prepend timestamp for uniqueness

        // Basic validation for image file type
        $imageFileType = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif', 'webp'); // Added webp
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        if (!in_array($imageFileType, $allowedTypes)) {
            $message = "❌ Error: Only JPG, JPEG, PNG, GIF, & WEBP files are allowed for images.";
            $imageName = ""; // Reset image name if not allowed
        } elseif ($_FILES['book_image']['size'] > $maxFileSize) {
            $message = "❌ Error: Image file size must be less than 5MB.";
            $imageName = "";
        } else {
            // Attempt to move the uploaded file
            if (!move_uploaded_file($imageTmp, $uploadDir . $imageName)) {
                $message = "❌ Error uploading image. Please try again.";
                $imageName = "";
            }
        }
    } else {
        // If no file was uploaded or an error occurred during upload
        $message = "❌ Error: Please upload a book image.";
    }

    // Only proceed with database insertion if no file upload errors occurred
    if (empty($message) && !empty($imageName)) {
        $sql = "INSERT INTO books_for_sale (title, author, condition_status, price, image)
                VALUES ('$title', '$author', '$condition', '$price', '$imageName')";
        if ($conn->query($sql) === TRUE) {
            $message = "✅ Book listed successfully! It will appear in the carousel shortly.";
            // Clear form fields after successful submission (client-side in JS)
        } else {
            $message = "❌ Error listing book: " . $conn->error;
        }
    }
}

$books = [];
// Fetch books for the carousel, limited and ordered by ID in descending order (latest first)
$result = $conn->query("SELECT * FROM books_for_sale ORDER BY id DESC LIMIT 20"); // Increased limit
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy & Sell Books - EBookHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Custom Properties (Theme Variables) - Unified with Home, Login, Signup */
        :root {
            --primary-color: #3a7bd5;
            --secondary-color: #00d2ff;
            --background-start-color: #f0f4f8; /* For gradient background */
            --background-end-color: #e2e8f0; /* For gradient background */
            --surface-color: rgba(255, 255, 255, 0.9); /* Slightly more opaque glass */
            --text-color: #2c3e50;
            --heading-color: #1a202c; /* Darker heading */
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --border-light: rgba(255, 255, 255, 0.3); /* For glass effect borders */
            --success-color: #28a745;
            --error-color: #dc3545;
        }

        /* General Reset & Body Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-start-color), var(--background-end-color));
            background-size: 200% 200%;
            animation: gradientBackground 15s ease infinite;
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center content horizontally */
            padding: 20px 0; /* Add vertical padding */
        }

        /* Header & Navigation - Consistent with Home, Login, Signup */
        header {
            width: 100%;
            max-width: 1200px; /* Aligned with main content max-width */
            background: var(--surface-color);
            padding: 1rem 2rem;
            text-align: center;
            box-shadow: 0 4px 12px var(--shadow-light);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border-light);
            animation: fadeInDown 0.8s ease-out;
            border-radius: 0 0 15px 15px; /* Rounded bottom corners */
        }

        header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            display: inline-block;
            animation: pulsateTitle 2s infinite alternate;
        }
        
        header h1::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: center;
            animation: lineExpand 2s infinite alternate;
        }

        nav {
            margin-top: 0.8rem;
            display: flex;
            justify-content: center;
        }

        nav a {
            margin: 0 18px;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 600;
            padding: 10px 15px;
            position: relative;
            transition: color 0.3s ease, transform 0.3s ease;
            overflow: hidden;
            border-radius: 5px;
        }

        nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(58, 123, 213, 0.1), rgba(0, 210, 255, 0.1));
            transition: left 0.3s ease-out;
            z-index: -1;
            border-radius: 5px;
        }

        nav a:hover::before {
            left: 0;
        }

        nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 3px;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
            transition: width 0.4s ease-in-out;
        }

        nav a:hover::after,
        nav a.active::after {
            width: 100%;
        }

        nav a:hover,
        nav a.active {
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Form Container Styling - Unified with Login/Signup */
        .form-container {
            width: 95%; /* Wider on small screens */
            max-width: 550px; /* Max width slightly increased */
            margin: 40px auto; /* Increased margin-top for separation */
            background: var(--surface-color);
            padding: 2.8rem; /* Increased padding */
            border-radius: 25px;
            box-shadow: 0 15px 40px var(--shadow-medium); /* Stronger shadow */
            animation: slideInUp 0.8s ease-out;
            border: 1px solid var(--border-light);
            backdrop-filter: blur(5px);
            position: relative; /* For the message box */
        }

        .form-container h2 {
            margin-bottom: 2rem; /* Increased margin */
            color: var(--heading-color);
            font-size: 2.4rem; /* Larger heading */
            text-align: center;
            position: relative;
            font-weight: 700;
        }

        .form-container h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 70px; /* Wider underline */
            height: 4px; /* Thicker underline */
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); /* Gradient underline */
            border-radius: 5px;
            animation: lineDraw 1s ease-out forwards;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"],
        form select { /* Added select for consistency */
            width: 100%;
            padding: 15px 20px; /* More padding */
            margin-bottom: 20px; /* Increased margin */
            border-radius: 12px;
            font-size: 1.05rem;
            border: 1px solid rgba(0, 0, 0, 0.1); /* Subtle border */
            background-color: #fcfdff;
            color: var(--text-color);
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05); /* Inner shadow */
        }

        form input::placeholder,
        form select {
            color: #88aadd;
            opacity: 0.9;
        }

        form input:focus,
        form select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(58, 123, 213, 0.4); /* Primary color glow */
            transform: translateY(-2px);
            background-color: var(--white);
        }
        
        form input[type="file"] {
            padding: 12px; /* Adjust padding for file input */
            cursor: pointer;
            background-color: var(--background-start-color); /* Different background for file input */
        }
        form input[type="file"]::file-selector-button {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        form input[type="file"]::file-selector-button:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #00d2ff, #3a7bd5);
        }

        form button {
            width: 100%;
            padding: 18px; /* More padding for button */
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); /* Matched primary gradient */
            color: var(--white);
            font-weight: 700; /* Bolder text */
            border: none;
            border-radius: 15px; /* More rounded */
            cursor: pointer;
            font-size: 1.2rem; /* Larger font */
            transition: all 0.4s ease;
            letter-spacing: 0.8px; /* More spacing */
            box-shadow: 0 10px 25px rgba(58, 123, 213, 0.3); /* Stronger shadow */
            position: relative;
            overflow: hidden;
            text-transform: uppercase; /* Uppercase for impact */
        }

        form button::before { /* Shine effect for button */
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.5s ease-out;
            z-index: 1;
        }

        form button:hover::before {
            left: 100%;
        }

        form button:hover {
            background: linear-gradient(45deg, #00d2ff, #3a7bd5); /* Reverse gradient on hover */
            transform: translateY(-5px); /* More pronounced lift */
            box-shadow: 0 15px 35px rgba(0, 210, 255, 0.4);
        }

        .message {
            margin-top: 10px; /* Adjusted margin */
            margin-bottom: 25px; /* Space before form */
            font-weight: 600;
            text-align: center;
            padding: 12px 20px; /* Adjusted padding */
            border-radius: 10px; /* More rounded */
            animation: fadeIn 0.6s ease-out;
            font-size: 1rem;
            opacity: 0; /* Start hidden */
            transform: translateY(-10px); /* Slight lift */
            animation: fadeInMessage 0.6s forwards;
            position: absolute; /* Position relative to form-container */
            top: 15px; /* Adjust as needed */
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 60px); /* Consider padding */
            z-index: 5;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .message.success {
            color: var(--success-color);
            background-color: #e6ffe6; /* Lighter success background */
            border: 1px solid var(--success-color);
        }
        .message.error {
            color: var(--error-color);
            background-color: #ffe6e6; /* Lighter error background */
            border: 1px solid var(--error-color);
        }


        /* Carousel Section Styling - Unified with Home Page elements */
        .carousel-section {
            width: 100%;
            max-width: 1200px;
            margin: 60px auto; /* Increased margin */
            padding: 2.5rem; /* Padding for the section itself */
            border-radius: 25px; /* Consistent rounded corners */
            background: var(--surface-color); /* Glassmorphism effect */
            box-shadow: 0 15px 40px var(--shadow-medium);
            border: 1px solid var(--border-light);
            backdrop-filter: blur(5px);
            text-align: center;
            animation: fadeInUp 1s ease forwards;
            opacity: 0;
        }

        .carousel-section h2 {
            font-size: 2.8rem;
            color: var(--heading-color);
            margin-bottom: 2.5rem; /* More space */
            font-weight: 700;
            position: relative;
            display: inline-block;
        }

        .carousel-section h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
            animation: lineDraw 1.5s ease-out forwards;
            animation-delay: 0.3s;
        }

        .carousel-container {
            width: 100%;
            max-width: 1000px; /* Adjusted to better fit 3 slides with margin on larger screens */
            margin: 30px auto;
            position: relative;
            overflow: hidden;
            padding: 0px 0px; /* No top/bottom padding here, only on .carousel-section */
        }

        .carousel-track {
            display: flex;
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            width: max-content;
            padding-bottom: 10px; /* Space for shadow */
            align-items: stretch; /* Ensure equal height cards */
        }

        .book-slide {
            min-width: 310px; /* Consistent slide width */
            max-width: 310px;
            margin: 0 15px; /* Consistent gap */
            background: var(--white);
            border-radius: 20px;
            padding: 25px; /* Increased padding */
            box-shadow: 0 15px 35px var(--shadow-light); /* Stronger but lighter shadow */
            text-align: center;
            animation: slideInRight 0.7s ease-out;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-light); /* Consistent border */
            display: flex; /* Flexbox for internal layout */
            flex-direction: column;
            justify-content: space-between; /* Push button to bottom */
        }
        
        .book-slide:hover {
            transform: translateY(-10px) scale(1.02); /* More pronounced lift */
            box-shadow: 0 25px 60px var(--shadow-medium); /* Stronger shadow */
        }

        .book-slide img {
            width: 100%;
            height: 220px; /* Taller images */
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 18px; /* More space below image */
            border: 1px solid #eee;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08); /* Image specific shadow */
        }

        .book-slide h3 {
            margin: 10px 0 8px; /* Adjusted margins */
            font-size: 1.35rem; /* Larger title */
            color: var(--primary-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 700;
        }

        .book-slide p {
            margin: 5px 0; /* Adjusted margins */
            color: #5a677a; /* Darker text for readability */
            font-size: 0.95rem; /* Slightly larger */
            line-height: 1.5;
        }

        .price {
            font-weight: 700;
            color: var(--success-color); /* Green for price */
            font-size: 1.45rem; /* Larger price */
            margin-top: 15px; /* More space above price */
            margin-bottom: 15px; /* Space before button */
            display: block;
        }

        .buy-btn {
            background: linear-gradient(45deg, var(--success-color), #218838); /* Consistent green gradient */
            color: white;
            border: none;
            padding: 14px 30px; /* Larger padding */
            border-radius: 12px; /* More rounded */
            font-weight: 600;
            margin-top: auto; /* Push to bottom of card */
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.05rem; /* Slightly larger font */
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3); /* Stronger shadow */
            letter-spacing: 0.5px;
        }

        .buy-btn:hover {
            background: linear-gradient(45deg, #218838, var(--success-color)); /* Reverse gradient on hover */
            transform: translateY(-4px); /* More pronounced lift */
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.6); /* Darker, more prominent */
            color: var(--white);
            font-size: 2.8rem; /* Larger arrows */
            border: none;
            border-radius: 50%;
            padding: 15px 20px; /* Increased padding */
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px; /* Larger button */
            height: 60px;
            opacity: 0.8;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: background 0.3s ease, transform 0.3s ease, opacity 0.3s ease;
        }

        .carousel-btn:hover {
            background: rgba(0, 0, 0, 0.9);
            opacity: 1;
            transform: translateY(-50%) scale(1.1); /* More pronounced scale */
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .prev { left: 10px; }
        .next { right: 10px; }

        /* Footer */
        footer {
            width: 100%;
            background: var(--heading-color); /* Darker footer */
            color: rgba(255, 255, 255, 0.9); /* Slightly more opaque text */
            text-align: center;
            padding: 2rem; /* Increased padding */
            font-size: 0.95rem; /* Slightly larger font */
            margin-top: auto; /* Pushes footer to bottom */
            box-shadow: 0 -4px 12px var(--shadow-light); /* Shadow above footer */
            border-radius: 15px 15px 0 0; /* Rounded top corners */
        }


        /* Keyframe Animations */
        @keyframes gradientBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes pulsateTitle {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        @keyframes lineExpand {
            0% { width: 0; }
            50% { width: 100%; }
            100% { width: 0; }
        }

        @keyframes lineDraw {
            from { width: 0; }
            to { width: 70px; }
        }

        @keyframes fadeInMessage {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }


        /* Responsive Design */
        @media (max-width: 1024px) {
            .carousel-container {
                max-width: 680px; /* Adjusted for 2 slides on tablets */
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px 0;
            }
            header {
                padding: 1rem;
                border-radius: 0 0 10px 10px;
            }
            header h1 {
                font-size: 2.2rem;
            }
            nav {
                flex-wrap: wrap;
                margin-top: 0.5rem;
            }
            nav a {
                margin: 5px 8px;
                padding: 8px 12px;
                font-size: 0.9rem;
            }
            .form-container {
                padding: 2rem;
                margin: 30px 15px;
            }
            .form-container h2 {
                font-size: 2rem;
            }
            .form-container h2::after {
                width: 50px;
            }
            form input, form select, form button {
                padding: 12px 15px;
                font-size: 0.95rem;
            }
            .message {
                padding: 10px 15px;
                font-size: 0.9rem;
                width: calc(100% - 40px);
            }
            .carousel-section {
                margin: 40px auto;
                padding: 2rem;
            }
            .carousel-section h2 {
                font-size: 2.4rem;
                margin-bottom: 2rem;
            }
            .carousel-container {
                max-width: 340px; /* Adjusted for 1 slide on mobiles */
            }
            .book-slide {
                min-width: 280px;
                max-width: 280px;
                margin: 0 10px;
                padding: 20px;
            }
            .book-slide img {
                height: 180px;
            }
            .book-slide h3 {
                font-size: 1.15rem;
            }
            .book-slide p {
                font-size: 0.85rem;
            }
            .price {
                font-size: 1.2rem;
            }
            .buy-btn {
                padding: 10px 20px;
                font-size: 0.95rem;
            }
            .carousel-btn {
                font-size: 2rem;
                padding: 8px 12px;
                width: 50px;
                height: 50px;
            }
            .prev { left: 5px; }
            .next { right: 5px; }
            footer {
                padding: 1.5rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 1.8rem;
            }
            nav a {
                flex-basis: 45%;
                margin: 5px;
                font-size: 0.8rem;
                padding: 6px 8px;
            }
            .form-container {
                padding: 1.8rem;
                margin: 20px 10px;
            }
            .form-container h2 {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
            }
            form input, form select, form button {
                padding: 10px 12px;
                font-size: 0.9rem;
            }
            .message {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            .carousel-section {
                padding: 1.5rem;
                margin: 30px auto;
            }
            .carousel-section h2 {
                font-size: 2rem;
            }
            .book-slide {
                min-width: 90vw; /* Use viewport width */
                max-width: 90vw;
                margin: 0 auto; /* Center single slide */
            }
            .carousel-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>EBookHub</h1>
        <nav>
            <a href="home.php">Home</a>
            <a href="buy_sell.php" class="active">Buy/Sell</a>
            <a href="search.php">Search</a>
            <?php // if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php // endif; ?>
        </nav>
    </header>

    <div class="form-container">
        <h2>Sell Your Book</h2>
        <?php if ($message): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Book Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <select name="condition" required>
                <option value="">Select Condition</option>
                <option value="New">New</option>
                <option value="Used - Like New">Used - Like New</option>
                <option value="Used - Very Good">Used - Very Good</option>
                <option value="Used - Good">Used - Good</option>
                <option value="Used - Fair">Used - Fair</option>
            </select>
            <input type="number" name="price" placeholder="Price (₹)" step="0.01" min="0" required>
            <input type="file" name="book_image" accept="image/jpeg, image/png, image/gif, image/webp" required>
            <button type="submit">Post Book</button>
        </form>
    </div>

    <?php if (!empty($books)): ?>
        <section class="carousel-section">
            <h2>Latest Books for Sale</h2>
            <div class="carousel-container">
                <button class="carousel-btn prev" onclick="prevSlide()">❮</button>
                <div class="carousel-track" id="carouselTrack">
                    <?php foreach ($books as $book): ?>
                        <div class="book-slide">
                            <div> <img src="<?= $uploadDir . htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?> Image">
                                <h3><?= htmlspecialchars($book['title']) ?></h3>
                                <p>by <strong><?= htmlspecialchars($book['author']) ?></strong></p>
                                <p>Condition: <?= htmlspecialchars($book['condition_status']) ?></p>
                                <p class="price">₹<?= htmlspecialchars(number_format($book['price'], 2)) ?></p>
                            </div>
                            <form method="GET" action="payment.php">
                                <input type="hidden" name="buy_id" value="<?= $book['id'] ?>">
                                <button type="submit" class="buy-btn">Buy Now</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-btn next" onclick="nextSlide()">❯</button>
            </div>
        </section>
    <?php else: ?>
        <p style="text-align: center; margin-top: 50px; font-size: 1.2rem; color: var(--text-color); opacity: 0.8;">No books currently listed for sale. Be the first to list one!</p>
    <?php endif; ?>

    <footer>
        <p>&copy; 2025 EBookHub. All rights reserved.</p>
    </footer>
<script>
    const track = document.getElementById("carouselTrack");
    const slides = document.querySelectorAll(".book-slide");
    let currentIndex = 0;
    const slideWidth = slides[0]?.offsetWidth + 30; // 30px = margin
    let autoSlideInterval;

    function updateCarousel() {
        track.style.transform = `translateX(${-currentIndex * slideWidth}px)`;
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        updateCarousel();
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        updateCarousel();
    }

    // Auto-slide every 4 seconds
    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 4000);
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }

    // Fade-in effect on page load
    window.addEventListener("load", () => {
        document.querySelectorAll(".carousel-section").forEach(section => {
            section.style.opacity = "1";
            section.style.transform = "translateY(0)";
            section.style.transition = "opacity 0.8s ease, transform 0.8s ease";
        });
        startAutoSlide();
    });

    // Pause autoplay on hover
    track.addEventListener("mouseenter", stopAutoSlide);
    track.addEventListener("mouseleave", startAutoSlide);
</script>


</body>
</html>