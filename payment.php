<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ebookhub";

// Connect to DB
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$message = "";

// Fetch book details using buy_id
$book = null;
if (isset($_GET['buy_id'])) {
    $id = intval($_GET['buy_id']);
    $result = $conn->query("SELECT * FROM books_for_sale WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $book = $result->fetch_assoc();
    }
}

// Handle payment form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_payment'])) {
    $buyer_name = $conn->real_escape_string($_POST['buyer_name']);
    $buyer_email = $conn->real_escape_string($_POST['buyer_email']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $book_id = intval($_POST['book_id']);

    $sql = "INSERT INTO payments (book_id, buyer_name, buyer_email, payment_method, status, created_at)
            VALUES ('$book_id', '$buyer_name', '$buyer_email', '$payment_method', 'Pending', NOW())";
    if ($conn->query($sql) === TRUE) {
        $message = "✅ Payment successful! Your order is placed.";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment - EBookHub</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        min-height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .payment-box {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .payment-box h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #3a7bd5;
    }
    .book-details {
        background: #f7f9fc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .book-details img {
        max-width: 100px;
        border-radius: 8px;
        margin-right: 15px;
    }
    .book-info {
        display: flex;
        align-items: center;
    }
    .book-info div {
        flex: 1;
    }
    input, select, button {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }
    button {
        background: linear-gradient(45deg, #28a745, #218838);
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    button:hover {
        transform: translateY(-2px);
        background: linear-gradient(45deg, #218838, #28a745);
    }
    .message {
        text-align: center;
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 8px;
    }
    .success { background: #e6ffe6; color: #28a745; }
    .error { background: #ffe6e6; color: #dc3545; }

    /* Home button styling */
    .home-button {
        display: block;
        text-align: center;
        text-decoration: none;
        margin-bottom: 15px;
        padding: 12px;
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        border-radius: 8px;
        font-weight: bold;
        transition: 0.3s;
    }
    .home-button:hover {
        transform: translateY(-2px);
        background: linear-gradient(45deg, #0056b3, #007bff);
    }
</style>
</head>
<body>
<div class="payment-box">
    <h2>Complete Your Payment</h2>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if ($book): ?>
        <div class="book-details">
            <div class="book-info">
                <img src="uploads/<?= htmlspecialchars($book['image']) ?>" alt="Book">
                <div>
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p>By <?= htmlspecialchars($book['author']) ?></p>
                    <p><strong>Price: ₹<?= number_format($book['price'], 2) ?></strong></p>
                </div>
            </div>
        </div>

        <!-- Home Button -->
        <a href="home.php" class="home-button">Home</a>

        <form method="POST">
            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
            <input type="text" name="buyer_name" placeholder="Your Name" required>
            <input type="email" name="buyer_email" placeholder="Your Email" required>
            <select name="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="UPI">UPI</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Debit Card">Debit Card</option>
                <option value="Net Banking">Net Banking</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
            </select>
            <button type="submit" name="confirm_payment">Pay Now</button>
        </form>
    <?php else: ?>
        <p style="text-align:center; color:red;">❌ Invalid Book ID.</p>
        <a href="home.php" class="home-button">Home</a>
    <?php endif; ?>
</div>
</body>
</html>
