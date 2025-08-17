<?php
session_start();

// Database credentials
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ebookhub";

// Initialize message variable
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish database connection
    $conn = new mysqli($host, $user, $pass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $message = "Database connection failed: " . $conn->connect_error;
    } else {
        // Sanitize and get input
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT fullname, password FROM users WHERE email = ?");
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                // Verify password
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user'] = $user['fullname'];
                    $_SESSION['user_email'] = $email; // Store email for potential future use
                    header("Location: search.php"); // Redirect to a dashboard or search page
                    exit;
                } else {
                    $message = "Invalid password. Please try again.";
                }
            } else {
                $message = "No account found with that email address.";
            }
            $stmt->close();
        } else {
            $message = "Failed to prepare statement: " . $conn->error;
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - EBookHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Custom Properties (Theme Variables) */
        :root {
            --primary-color: #3a7bd5;
            --secondary-color: #00d2ff;
            --background-start-color: #f0f4f8;
            --background-end-color: #e2e8f0;
            --surface-color: rgba(255, 255, 255, 0.9); /* Slightly more opaque glass */
            --text-color: #2c3e50;
            --heading-color: #1a202c;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --border-light: rgba(255, 255, 255, 0.3);
            --input-bg: #fdfefe;
            --input-border: #dde2e7;
            --error-color: #dc3545;
            --error-bg: #fdd;
        }

        /* General Reset & Body Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-start-color), var(--background-end-color));
            background-size: 200% 200%;
            animation: gradientBackground 15s ease infinite;
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        header {
            background: var(--surface-color); /* Use surface color for a cohesive look */
            padding: 1rem 2rem;
            text-align: center;
            box-shadow: 0 4px 12px var(--shadow-light);
            width: 100%;
            z-index: 10;
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border-light);
            animation: fadeInDown 0.8s ease-out;
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
            overflow: hidden; /* For the background slide effect */
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

        /* Main Container */
        .main-container {
            display: flex;
            flex-grow: 1;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 950px; /* Slightly wider */
            background: var(--surface-color);
            border-radius: 25px; /* More rounded */
            box-shadow: 0 20px 50px var(--shadow-medium);
            overflow: hidden;
            backdrop-filter: blur(12px); /* Stronger blur */
            border: 1px solid var(--border-light);
            animation: popIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transition: transform 0.3s ease;
        }
        .login-wrapper:hover {
            transform: translateY(-5px);
        }
        
        /* Info Panel (Left Side) */
        .info-panel {
            flex-basis: 40%; /* Slightly smaller info panel */
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            animation: slideInLeft 0.8s ease-out;
        }

        .info-panel h2 {
            font-size: 2.3rem;
            margin-bottom: 1.2rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .info-panel p {
            font-size: 1.05rem;
            line-height: 1.6;
            opacity: 0.9;
        }

        /* Form Container (Right Side) */
        .form-container {
            flex-basis: 60%; /* Larger form area */
            padding: 3.5rem 3rem;
            text-align: center;
            animation: fadeInRight 0.8s ease-out;
        }

        .form-container h2 {
            margin-bottom: 2rem;
            color: var(--heading-color);
            font-size: 2.4rem;
            font-weight: 700;
            position: relative;
            display: inline-block;
        }

        .form-container h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .input-group {
            position: relative;
            margin-bottom: 2rem; /* Increased margin */
        }

        .input-group i {
            position: absolute;
            left: 18px; /* Adjusted icon position */
            top: 50%;
            transform: translateY(-50%);
            color: #aab8c2; /* Lighter icon color */
            transition: color 0.3s ease;
            font-size: 1.1rem;
        }

        form input {
            width: 100%;
            padding: 15px 15px 15px 55px; /* Increased padding for icon */
            border: 1px solid var(--input-border);
            border-radius: 10px; /* More rounded inputs */
            font-size: 1.05rem;
            background-color: var(--input-bg);
            color: var(--text-color);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        form input::placeholder {
            color: #9baac4;
            opacity: 0.9;
        }

        form input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(58, 123, 213, 0.2); /* Softer focus ring */
            background-color: #ffffff;
        }

        form input:focus + i {
            color: var(--primary-color);
        }

        form button {
            width: 100%;
            padding: 16px; /* Larger button */
            background-image: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            background-size: 200% auto;
            color: white;
            font-size: 1.2rem;
            font-weight: 700; /* Bolder text */
            border: none;
            border-radius: 10px; /* More rounded button */
            transition: all 0.5s ease;
            cursor: pointer;
            box-shadow: 0 8px 20px var(--shadow-light); /* Stronger shadow */
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        form button::before {
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
            background-position: right center; /* Moves gradient on hover */
            transform: translateY(-5px); /* More pronounced lift */
            box-shadow: 0 12px 25px var(--shadow-medium);
        }

        .form-container .signup-link {
            margin-top: 2rem; /* Increased margin */
            font-size: 1rem;
            color: var(--text-color);
        }

        .form-container .signup-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .form-container .signup-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        /* Message Styling */
        .message {
            padding: 12px;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            color: var(--error-color);
            background-color: var(--error-bg);
            border: 1px solid rgba(220, 53, 69, 0.4);
            animation: fadeIn 0.5s ease-out;
        }

        /* Animations */
        @keyframes gradientBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes popIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeInRight {
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .login-wrapper {
                max-width: 700px;
            }
            .info-panel {
                padding: 2.5rem 2rem;
            }
            .info-panel h2 {
                font-size: 1.8rem;
            }
            .info-panel p {
                font-size: 0.95rem;
            }
            .form-container {
                padding: 2.5rem;
            }
            .form-container h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1.5rem;
            }
            .login-wrapper {
                flex-direction: column;
                max-width: 450px; /* Single column width */
            }
            .info-panel {
                display: none; /* Hide info panel on smaller screens to prioritize form */
            }
            .form-container {
                padding: 2.5rem 2rem;
                flex-basis: 100%; /* Take full width */
            }
            header {
                padding: 1rem;
            }
            header h1 {
                font-size: 2rem;
            }
            nav {
                flex-wrap: wrap;
                margin-top: 0.5rem;
            }
            nav a {
                margin: 5px 10px;
                padding: 6px 10px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 1rem;
            }
            .login-wrapper {
                border-radius: 15px;
            }
            .form-container {
                padding: 2rem 1.5rem;
            }
            .form-container h2 {
                font-size: 1.8rem;
                margin-bottom: 1.5rem;
            }
            form input {
                padding: 12px 12px 12px 45px;
                font-size: 0.95rem;
            }
            form input + i {
                left: 15px;
            }
            form button {
                padding: 14px;
                font-size: 1rem;
            }
            .signup-link {
                font-size: 0.9rem;
                margin-top: 1.5rem;
            }
            header h1 {
                font-size: 1.6rem;
            }
            nav a {
                font-size: 0.8rem;
            }
            .message {
                padding: 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>EBookHub</h1>
    <nav>
        <a href="home.php">Home</a>
        <a href="login.php" class="active">Login</a>
        <a href="signup.php">Sign Up</a>
    </nav>
</header>

<div class="main-container">
    <div class="login-wrapper">
        <div class="info-panel">
            <h2>Welcome Back!</h2>
            <p>Log in to access your account, manage your listings, and continue your reading journey with EBookHub.</p>
        </div>

        <div class="form-container">
            <h2>Member Login</h2>

            <?php if (!empty($message)): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email Address" required autocomplete="username">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit">Login</button>
                <p class="signup-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
            </form>
        </div>
    </div>
</div>

</body>
</html>