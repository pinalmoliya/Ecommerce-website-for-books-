<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EBookHub | Your Modern Digital Book Marketplace</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- INTERNAL CSS START (same as your original, kept intact) --- */
        :root {
            --primary-color: #3a7bd5;
            --secondary-color: #00d2ff;
            --background-start-color: #f0f4f8;
            --background-end-color: #e2e8f0;
            --surface-color: rgba(255, 255, 255, 0.9);
            --text-color: #2c3e50;
            --heading-color: #1a202c;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --border-light: rgba(255, 255, 255, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

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
        }

        header {
            background: var(--surface-color);
            padding: 1rem 2rem;
            text-align: center;
            box-shadow: 0 4px 12px var(--shadow-light);
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
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
            flex-wrap: wrap;
        }

        nav a {
            margin: 5px 15px;
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

        nav a:hover::before { left: 0; }

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

        main {
            padding: 2.5rem 2rem;
            max-width: 1200px;
            margin: auto;
            flex-grow: 1;
        }

        section {
            text-align: center;
            margin: 6rem 0;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards;
            padding: 2rem;
            border-radius: 15px;
            background: var(--surface-color);
            box-shadow: 0 10px 30px var(--shadow-light);
            backdrop-filter: blur(5px);
            border: 1px solid var(--border-light);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        section:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px var(--shadow-medium);
        }

        section h2 {
            font-size: 2.8rem;
            color: var(--heading-color);
            margin-bottom: 1.8rem;
            font-weight: 700;
            position: relative;
            display: inline-block;
        }

        section h2::after {
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

        section p {
            max-width: 850px;
            margin: 0 auto 2.5rem auto;
            font-size: 1.15rem;
            color: #4a5568;
        }

        .main-img {
            width: 100%;
            max-width: 700px;
            display: block;
            margin: 2.5rem auto;
            border-radius: 15px;
            box-shadow: 0 15px 40px var(--shadow-medium);
            animation: zoomIn 1.2s ease;
            border: 1px solid var(--border-light);
        }

        .features, .categories {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2.5rem;
            margin-top: 3rem;
        }

        .card {
            background: var(--surface-color);
            padding: 2.2rem;
            border-radius: 15px;
            width: 320px;
            box-shadow: 0 10px 30px var(--shadow-light);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 1s ease forwards;
            opacity: 0;
            border: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
        }

        .card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: block;
            opacity: 0.8;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .card:hover i {
            transform: scale(1.1);
            opacity: 1;
        }

        .features .card:nth-child(1), .categories .card:nth-child(1) { animation-delay: 0.2s; }
        .features .card:nth-child(2), .categories .card:nth-child(2) { animation-delay: 0.4s; }
        .features .card:nth-child(3), .categories .card:nth-child(3) { animation-delay: 0.6s; }

        .card:hover {
            transform: translateY(-12px);
            box-shadow: 0 18px 45px var(--shadow-medium);
        }

        .card h3 {
            font-size: 1.6rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
            font-weight: 700;
        }

        .card p {
            font-size: 1rem;
            margin-bottom: 0;
            color: #5a677a;
        }

        .cta-button {
            background-image: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 16px 40px;
            font-size: 1.2rem;
            font-weight: 700;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: transform 0.5s ease, box-shadow 0.5s ease, background-position 0.5s ease;
            margin-top: 2rem;
            box-shadow: 0 8px 20px rgba(58, 123, 213, 0.4);
            background-size: 200% auto;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        .cta-button::before {
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

        .cta-button:hover::before { left: 100%; }

        .cta-button:hover {
            transform: scale(1.07);
            box-shadow: 0 15px 35px rgba(0, 210, 255, 0.6);
            background-position: right center;
        }

        footer {
            background: var(--heading-color);
            color: rgba(255, 255, 255, 0.9);
            text-align: center;
            padding: 2rem;
            font-size: 0.95rem;
            margin-top: auto;
            box-shadow: 0 -4px 12px var(--shadow-light);
        }

        @keyframes gradientBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
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
            to { width: 80px; }
        }
    </style>
</head>
<body>

    <header>
        <h1>EBookHub</h1>
        <nav>
            <a href="home.php" class="active">Home</a>
            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
            <a href="buy_sell.php">Buy/Sell</a>
            <a href="search.php">Search</a>
            <a href="admin.php">Admin Panel</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>Welcome to EBookHub</h2>
            <p>Your one-stop platform to buy, sell, or search for digital and physical books in a seamless, modern marketplace.</p>
            <img src="https://images.unsplash.com/photo-1532012197267-da84d127e765?q=80&w=1887&auto=format&fit=crop" alt="Books collection" class="main-img">
        </section>

        <section>
            <h2>Why Choose Us?</h2>
            <div class="features">
                <div class="card">
                    <i class="fas fa-book-open"></i><h3>Wide Selection</h3>
                    <p>Access thousands of books across various genres and categories, from academic texts to best-selling fiction.</p>
                </div>
                <div class="card">
                    <i class="fas fa-handshake"></i><h3>Buy & Sell Easily</h3>
                    <p>Our intuitive platform makes it simple to list your used books for sale or find affordable options from others.</p>
                </div>
                <div class="card">
                    <i class="fas fa-shield-alt"></i><h3>Verified & Trusted</h3>
                    <p>We verify our users to ensure a safe, secure, and trustworthy environment for every transaction.</p>
                </div>
            </div>
        </section>

        <section>
            <h2>Top Categories</h2>
            <div class="categories">
                <div class="card">
                    <i class="fas fa-palette"></i><h3>Fiction</h3>
                    <p>Discover captivating novels, thrillers, and dramas from world-renowned authors.</p>
                </div>
                <div class="card">
                    <i class="fas fa-flask"></i><h3>Academic</h3>
                    <p>Find essential engineering, medical, and science textbooks for your studies.</p>
                </div>
                <div class="card">
                    <i class="fas fa-medal"></i><h3>Competitive Exams</h3>
                    <p>Get the edge with preparatory materials for UPSC, NEET, JEE, SSC, and more.</p>
                </div>
            </div>
        </section>

        <section>
            <h2>Admin Panel Access</h2>
            <p>Admin users can manage listings, monitor transactions, and oversee user activities in a secure dashboard.</p>
            <a href="admin.php"><button class="cta-button">Go to Admin Panel</button></a>
        </section>

        <section>
            <h2>Get Started Today!</h2>
            <p>Create your free account in seconds and join a vibrant community of book lovers.</p>
            <a href="signup.php"><button class="cta-button">Join Now for Free</button></a>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 EBookHub. All rights reserved.</p>
    </footer>

</body>
</html>
