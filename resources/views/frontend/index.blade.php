<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Uploader</title>
    <link rel="shortcut icon" href="{{ asset('img/icons/icon-48x48.png') }}" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --gradient: linear-gradient(45deg, #ff3366, #ff6b6b, #4834d4, #686de0);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --border: 1px solid rgba(255, 255, 255, 0.1);
            --shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
        }

        .nav-links a::before,
        .nav-links a::after {
            content: '';
        }

        .logo {
            color: #ff3366;
            animation: colorShift 6s linear infinite;
            text-decoration: none;
            /* remove underline */
            font-size: 1.8rem;
            font-weight: 800;
        }

        /* Color cycle animation */
        @keyframes colorShift {
            0% {
                color: #ff3366;
            }

            /* pink */
            25% {
                color: #ffcc00;
            }

            /* yellow */
            50% {
                color: #00c3ff;
            }

            /* blue */
            75% {
                color: #4834d4;
            }

            /* purple */
            100% {
                color: #ff3366;
            }

            /* back to pink */
        }


        /* Hero */
        .hero-section {
            position: relative;
            height: 80vh;
            background: url("https://wallpapercave.com/wp/wp3396918.jpg") center/cover no-repeat;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            animation: fadeInUp 1s ease-in-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Section titles */
        .section-title {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bold;
        }

        /* Footer */
        footer {
            background: #222;
            color: #ddd;
            padding: 60px 0;
        }

        footer a {
            color: #ddd;
            text-decoration: none;
        }

        footer a:hover {
            color: #0d6efd;
        }

        .btn-gradientshow {
            color: white;
            border: none;
            border-top-left-radius: 20px;
            border-bottom-right-radius: 20px;
            background: linear-gradient(270deg, blue);
            background-size: 600% 600%;
            transition: all 0.4s ease-in-out;
        }

        .btn-gradientshow:hover {
            background-position: right center;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
            color: #fff;
        }
        
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="logo" href=""><img src="{{ asset('img/icons/icon-48x48.png') }}"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto fw-medium">
                    <li class="nav-item"><a class="nav-link" href="{{ route('homepage') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('news') }}">News</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li> -->
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <a href="#about" class="btn btn-gradientshow">Learn More</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-md-6">
                    <img src="https://tse1.mm.bing.net/th/id/OIP.kNdOInTPYQqLrJi0kRYvcQHaDl?pid=Api&P=0&h=180"
                        class="img-fluid rounded shadow-lg" alt="About Us">
                </div>
                <div class="col-md-6">
                    <h2 class="section-title mb-3">About Us</h2>
                    <p class="text-muted">
                        Uploader is your trusted platform for sharing and managing content. We believe in empowering users with a
                        seamless, modern experience that makes information accessible, engaging, and impactful.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-md-6 order-md-2">
                    <img src="https://tse1.mm.bing.net/th/id/OIP.y6uMnlXbgT-o7PENrUh-pQHaDl?pid=Api&P=0&h=180"
                        class="img-fluid rounded shadow-lg" alt="Services">
                </div>
                <div class="col-md-6">
                    <h2 class="section-title mb-3">Our Services</h2>
                    <p class="text-muted">
                        From latest news delivery to powerful uploading tools, Uploader provides fast, reliable, and secure
                        solutions. Built with modern technologies to keep you ahead.
                    </p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Instant Content Uploading</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i> News & Updates</li>
                        <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Secure File Management</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-4">
                    <h5 class="fw-bold">Uploader</h5>
                    <p>Uploader is your trusted platform for sharing and managing content. Building seamless experiences since
                        2024.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('homepage') }}">Home</a></li>
                        <li><a href="{{ route('news') }}">News</a></li>
                        <!-- <li><a href="#about">About</a></li>
                        <li><a href="#services">Services</a></li> -->
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold">Contact</h5>
                    <p><i class="bi bi-geo-alt-fill me-2"></i>Jaipur, India</p>
                    <p><i class="bi bi-telephone-fill me-2"></i>+91 9876543210</p>
                    <p><i class="bi bi-envelope-fill me-2"></i>support@uploader.com</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center small">&copy; 2025 Uploader. All Rights Reserved.</div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>