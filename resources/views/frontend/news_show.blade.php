<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>News - Uploader</title>
    <link rel="shortcut icon" href="{{ asset('img/icons/icon-48x48.png') }}" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
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

        .section-title {
            background: linear-gradient(90deg, blue, violet);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bold;
        }

        /* Footer */
        footer {
            background: #222;
            color: #ddd;
            padding: 60px 0;
            margin-top: 60px;
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
            background: linear-gradient(270deg, yellow, green, cyan, blue, violet);
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
                    <li class="nav-item"><a class="nav-link active" href="{{ route('news') }}">News</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li> -->
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="section-title mb-3">{{ $news->title ?? 'News Detail' }}</h2>
        <span class="news-date">{{ \Carbon\Carbon::parse($news->created_at)->format('d M, Y') }}</span>
        <p class="news-desc mt-3">{{ $news->description }}</p>
        <a href="{{ route('news') }}" class="btn btn-secondary mt-3">Back to News</a>
    </div>
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

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</body>

</html>