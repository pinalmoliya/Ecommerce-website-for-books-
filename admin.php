<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "ebookhub");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === "admin" && $password === "12345") {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}

// If not logged in, show login form
if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login | EBookHub</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary-color: #3a7bd5;
                --secondary-color: #00d2ff;
                --background-start: #f0f4f8;
                --background-end: #e2e8f0;
                --surface-color: rgba(255, 255, 255, 0.95);
                --shadow-light: rgba(0, 0, 0, 0.08);
            }
            body {
                font-family: 'Poppins', sans-serif;
                background: linear-gradient(135deg, var(--background-start), var(--background-end));
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .login-box {
                background: var(--surface-color);
                padding: 2rem;
                border-radius: 15px;
                width: 350px;
                box-shadow: 0 10px 30px var(--shadow-light);
                text-align: center;
            }
            h2 {
                font-size: 2rem;
                font-weight: 700;
                background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin-bottom: 1rem;
            }
            input {
                width: 100%;
                padding: 10px;
                margin: 8px 0;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-size: 1rem;
            }
            button, .home-btn {
                display: inline-block;
                background: var(--primary-color);
                color: white;
                border: none;
                padding: 10px;
                width: 100%;
                border-radius: 8px;
                font-size: 1rem;
                cursor: pointer;
                transition: 0.3s;
                text-decoration: none;
                margin-top: 8px;
            }
            button:hover, .home-btn:hover {
                background: var(--secondary-color);
            }
            .error {
                color: red;
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>Admin Login</h2>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <a href="home.php" class="home-btn">Home</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Fetch dashboard stats
$user_count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$book_count = $conn->query("SELECT COUNT(*) as total FROM books_for_sale")->fetch_assoc()['total'];
$order_count = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | EBookHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3a7bd5;
            --secondary-color: #00d2ff;
            --background-start: #f0f4f8;
            --background-end: #e2e8f0;
            --surface-color: rgba(255, 255, 255, 0.95);
            --shadow-light: rgba(0, 0, 0, 0.08);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-start), var(--background-end));
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px var(--shadow-light);
        }
        .header h1 { margin: 0; font-size: 1.5rem; }
        .logout {
            background: red;
            padding: 8px 15px;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
        }
        .logout:hover { background: #ff4d4d; }
        .dashboard {
            max-width: 1100px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            padding: 0 20px;
        }
        .card {
            background: var(--surface-color);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px var(--shadow-light);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .card:hover { transform: translateY(-5px); }
        .card h2 {
            font-size: 48px;
            margin: 0;
            color: var(--primary-color);
        }
        .card p {
            margin-top: 10px;
            font-size: 16px;
            font-weight: 500;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>EBookHub Admin Panel</h1>
        <a href="admin.php?logout=true" class="logout">Logout</a>
    </div>
    <section class="dashboard">
        <div class="card">
            <h2><?php echo $user_count; ?></h2>
            <p>Total Users</p>
        </div>
        <div class="card">
            <h2><?php echo $book_count; ?></h2>
            <p>Books Listed</p>
        </div>
        <div class="card">
            <h2><?php echo $order_count; ?></h2>
            <p>Total Orders</p>
        </div>
    </section>
</body>
</html>
