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

$searchResultsHtml = ""; // Renamed to avoid conflict with 'searchResults' in JS if any
$keyword = ""; // Initialize keyword for displaying in "no results" message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $keyword = $_POST["keyword"]; // Get keyword
    
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, title, author, condition_status, price, image FROM books_for_sale 
                            WHERE title LIKE ? OR author LIKE ? OR condition_status LIKE ?");
    
    // Add wildcards for LIKE operator
    $searchKeyword = '%' . $keyword . '%';
    $stmt->bind_param("sss", $searchKeyword, $searchKeyword, $searchKeyword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $searchResultsHtml .= "<h3 class='results-heading'><i class='fas fa-search'></i> Search Results for '<strong>" . htmlspecialchars($keyword) . "</strong>':</h3><div class='book-grid'>";
        while ($row = $result->fetch_assoc()) {
            $imagePath = !empty($row["image"]) ? "uploads/" . htmlspecialchars($row["image"]) : "https://placehold.co/250x200/e0e0e0/555555?text=No+Image"; // Placeholder image
            $searchResultsHtml .= "
                <div class='book-card'>
                    <img src='$imagePath' alt='Book Image' onerror=\"this.onerror=null;this.src='https://placehold.co/250x200/e0e0e0/555555?text=No+Image';\">
                    <div class='book-info'>
                        <h4>" . htmlspecialchars($row["title"]) . "</h4>
                        <p><strong>Author:</strong> " . htmlspecialchars($row["author"]) . "</p>
                        <p><strong>Condition:</strong> " . htmlspecialchars($row["condition_status"]) . "</p>
                        <p class='price'><strong>Price:</strong> ₹" . number_format($row["price"], 2) . "</p>
                        <a href='payment.php?buy_id=" . htmlspecialchars($row["id"]) . "' class='buy-link'>Buy Now</a>
                    </div>
                </div>
            ";
        }
        $searchResultsHtml .= "</div>";
    } else {
        $searchResultsHtml = "<p class='no-results-message'><i class='fas fa-exclamation-circle'></i> No books found matching '<strong>" . htmlspecialchars($keyword) . "</strong>'</p>";
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - EBookHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Custom Properties (Theme Variables) - Unified with Home, Buy/Sell, Login, Signup */
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
            position: sticky; /* Makes it stick to the top */
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
            text-align: center;
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

        form input[type="text"] {
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

        form input::placeholder {
            color: #88aadd;
            opacity: 0.9;
        }

        form input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(58, 123, 213, 0.4); /* Primary color glow */
            transform: translateY(-2px);
            background-color: var(--white);
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
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color)); /* Reverse gradient on hover */
            transform: translateY(-5px); /* More pronounced lift */
            box-shadow: 0 15px 35px rgba(0, 210, 255, 0.4);
        }

        /* Results Section Styling */
        .results-section {
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
            min-height: 200px; /* Ensure it has some height even if empty */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .results-heading {
            font-size: 2rem;
            color: var(--heading-color);
            margin-bottom: 2.5rem;
            font-weight: 700;
            position: relative;
            display: inline-block;
        }
        .results-heading i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        .results-heading::after {
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

        .no-results-message {
            font-size: 1.2rem;
            color: var(--text-color);
            opacity: 0.8;
            padding: 20px;
            background-color: #fef2f2; /* Light red background for error */
            border: 1px solid var(--error-color);
            border-radius: 10px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .no-results-message i {
            color: var(--error-color);
            font-size: 1.5rem;
        }

        .book-grid {
            display: grid; /* Use CSS Grid for layout */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Responsive columns */
            gap: 30px; /* Increased gap */
            justify-items: center; /* Center items in grid cells */
            width: 100%; /* Take full width of parent */
            margin-top: 30px; /* Space from heading */
        }

        .book-card {
            background: var(--white);
            border-radius: 20px; /* More rounded */
            overflow: hidden;
            box-shadow: 0 12px 30px var(--shadow-light); /* Stronger but lighter shadow */
            width: 100%; /* Take full grid cell width */
            max-width: 320px; /* Max width for individual card */
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex; /* Flexbox for internal layout */
            flex-direction: column;
            justify-content: space-between; /* Push buy button to bottom */
            border: 1px solid var(--border-light); /* Consistent border */
        }

        .book-card:hover {
            transform: translateY(-10px); /* More pronounced lift */
            box-shadow: 0 20px 50px var(--shadow-medium); /* Stronger shadow */
        }

        .book-card img {
            width: 100%;
            height: 220px; /* Taller images */
            object-fit: cover;
            border-radius: 15px 15px 0 0; /* Rounded top corners only */
            box-shadow: 0 5px 15px rgba(0,0,0,0.08); /* Image specific shadow */
        }

        .book-info {
            padding: 20px; /* Increased padding */
            flex-grow: 1; /* Allow info to grow */
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .book-info h4 {
            margin-bottom: 10px; /* Adjusted margin */
            font-size: 1.3rem; /* Larger title */
            color: var(--primary-color);
            font-weight: 700;
            line-height: 1.3;
        }

        .book-info p {
            margin: 6px 0; /* Adjusted margin */
            font-size: 0.95rem; /* Consistent font size */
            color: #5a677a; /* Darker text for readability */
        }
        .book-info p.price {
            font-weight: 700;
            color: var(--success-color); /* Green for price */
            font-size: 1.2rem; /* Larger price */
            margin-top: 15px; /* Space above price */
        }

        .buy-link {
            display: inline-block;
            background: linear-gradient(45deg, var(--success-color), #218838); /* Consistent green gradient */
            color: white;
            padding: 12px 25px; /* Adjusted padding */
            border-radius: 10px; /* More rounded */
            font-weight: 600;
            text-decoration: none;
            margin-top: 15px; /* Space from content above */
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
            text-align: center;
            width: calc(100% - 40px); /* Adjust for padding of book-info */
            margin-left: 20px; /* Align with book-info padding */
            margin-right: 20px;
        }

        .buy-link:hover {
            background: linear-gradient(45deg, #218838, var(--success-color)); /* Reverse gradient on hover */
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
        }

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

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            header h1 {
                font-size: 2.2rem;
            }
            nav a {
                margin: 0 10px;
                padding: 8px 10px;
            }
            .form-container {
                padding: 2rem;
            }
            .form-container h2 {
                font-size: 2rem;
            }
            .results-section {
                padding: 2rem;
            }
            .results-heading {
                font-size: 1.8rem;
            }
            .book-grid {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 20px;
            }
            .book-card {
                max-width: none; /* Allow cards to fill grid space */
            }
            .book-card img {
                height: 180px;
            }
            .book-info h4 {
                font-size: 1.15rem;
            }
            .book-info p {
                font-size: 0.9rem;
            }
            .book-info p.price {
                font-size: 1.1rem;
            }
            .buy-link {
                padding: 10px 20px;
                font-size: 0.9rem;
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
                font-size: 2rem;
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
                padding: 1.8rem;
                margin: 30px 15px;
            }
            .form-container h2 {
                font-size: 1.8rem;
            }
            .form-container h2::after {
                width: 50px;
            }
            form input, form button {
                padding: 12px 15px;
                font-size: 0.95rem;
            }
            .results-section {
                padding: 1.5rem;
                margin: 40px auto;
            }
            .results-heading {
                font-size: 1.6rem;
                margin-bottom: 20px;
            }
            .results-heading::after {
                width: 50px;
            }
            .no-results-message {
                font-size: 1rem;
                padding: 15px;
            }
            .book-grid {
                grid-template-columns: 1fr; /* Single column on small screens */
                gap: 25px;
            }
            .book-card {
                max-width: 300px; /* Constrain single card width */
                margin: 0 auto; /* Center single card */
            }
            .book-card img {
                height: 200px;
            }
            footer {
                padding: 1.5rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 1.6rem;
            }
            nav a {
                flex-basis: 45%;
                margin: 5px;
                font-size: 0.8rem;
                padding: 6px 8px;
            }
            .form-container {
                padding: 1.5rem;
                margin: 20px 10px;
            }
            .form-container h2 {
                font-size: 1.6rem;
                margin-bottom: 1.5rem;
            }
            form input, form button {
                padding: 10px 12px;
                font-size: 0.9rem;
            }
            .results-section {
                padding: 1rem;
                margin: 30px auto;
            }
            .results-heading {
                font-size: 1.4rem;
                margin-bottom: 15px;
            }
            .no-results-message {
                font-size: 0.9rem;
                padding: 10px;
            }
            .book-card img {
                height: 160px;
            }
            .book-info h4 {
                font-size: 1rem;
            }
            .book-info p {
                font-size: 0.85rem;
            }
            .book-info p.price {
                font-size: 1rem;
            }
            .buy-link {
                padding: 8px 15px;
                font-size: 0.85rem;
            }
            footer {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

    <header>
        <h1>EBookHub</h1>
        <nav>
            <a href="home.php">Home</a>
            <a href="buy_sell.php">Buy/Sell</a>
            <a href="search.php" class="active">Search</a>
            
        </nav>
    </header>

    <div class="form-container">
        <h2>Search for Books</h2>
        <form method="POST" action="">
            <input type="text" name="keyword" placeholder="Enter title, author, or keyword" value="<?= htmlspecialchars($keyword) ?>" required>
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="results-section">
        <?= $searchResultsHtml ? $searchResultsHtml : "<p class='initial-search-prompt'><i class='fas fa-info-circle'></i> Enter a keyword above to find books. Try 'fiction', 'history', or an author's name!</p>"; ?>
    </div>

    <footer>
        <p>&copy; 2025 EBookHub. All rights reserved.</p>
    </footer>

</body>
</html>