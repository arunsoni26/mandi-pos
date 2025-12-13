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

        /* News Section */
        .news-section {
            padding: 30px 20px;
            /* top/bottom 60px, left/right 20px */
        }

        /* News Card Hover Effect */
        .news-card {
            position: relative;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            background: #f3f3f3;
            border-radius: 16px;
            padding: 20px;
            text-align: left;
            min-height: 220px;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            cursor: pointer;
            border: 1px solid #ccc;
        }

        .news-card:hover {
            transform: scale(1.05);
            /* zoom effect */
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
            z-index: 10;
            /* ensures hovered card stays above */
        }

        @keyframes borderMove {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 300% 50%;
            }
        }


        .news-date {
            display: block;
            font-size: 14px;
            font-weight: 600;
            /* color: #0a0a0a; */
            margin-bottom: 8px;
        }

        .news-desc {
            font-size: 16px;
            /* color: #0a0a0a; */
            font-weight: 500;
            line-height: 1.5;
        }

        .newsSwiper .swiper-wrapper {
            display: flex;
            align-items: stretch;
        }

        .newsSwiper .swiper-slide {
            display: flex;
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

        .news-ticker-box {
            max-width: 100%;
            height: 150px;
            /* small box */
            overflow: hidden;
            position: relative;
        }

        .news-ticker-list {
            list-style: none;
            padding: 0;
            margin: 0;
            animation: tickerScroll 20s linear infinite;
        }

        .news-ticker-list li {
            padding: 6px 0;
            font-size: 16px;
            font-weight: 500;
            display: flex;
            gap: 10px;
            align-items: center;
            color: #222;
        }

        .news-date {
            font-size: 14px;
            font-weight: 600;
            /* color: #ff3366; */
        }

        @keyframes tickerScroll {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-100%);
            }
        }

        .running-border {
            position: relative;
            border: 3px solid transparent;
            border-radius: 8px;
            padding: 10px;
            background: linear-gradient(white, white) padding-box,
                linear-gradient(90deg, red, orange, yellow, green, blue, purple, red) border-box;
            background-size: 300% 300%;
            animation: borderMove 5s linear infinite;
        }

        @keyframes borderMove {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 300% 50%;
            }
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

    <!-- News Section -->
    <section class="news-section">
        <div class="container-fluid px-0">
            <!-- News Ticker Box -->
            <div class="news-ticker-box bg-white shadow-sm rounded-3 p-3 mx-auto running-border">
                <ul class="news-ticker-list mb-0">
                    @foreach($news as $item)
                    <li style="padding-left:5px; color: {{ $item->color }} !important;">
                        <span class="news-date">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M, Y') }}
                        </span>
                        <span class="news-desc">
                            {{ \Illuminate\Support\Str::words($item->description, 20, '...') }}
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <hr>
        </div>

        <div class="container news-section">
            <h2 class="text-center mb-5 section-title">Latest News</h2>

            <div class="row g-4" id="news-container">
                @foreach($allnews->take(6) as $item)
                <div class="col-md-6 col-lg-4 news-item">
                    <div class="news-card">
                        <span class="news-date" style="color: #ff3366;">{{ \Carbon\Carbon::parse($item->created_at)->format('d M, Y') }}</span>
                        <p class="news-desc">
                            {{ \Illuminate\Support\Str::words($item->description, 20, '...') }}
                            <a href="{{ route('news.show', $item->id) }}" target="_blank" class="">Read More</a>
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            @if($allnews->count() > 6)
            <div class="text-center mt-4">
                <button id="load-more-news" class="btn btn-gradientshow">Load More</button>
            </div>
            @endif
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

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        var swiper = new Swiper(".newsSwiper", {
            slidesPerView: 3,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
            },
            speed: 4000,
            allowTouchMove: true,
            grabCursor: true,
            breakpoints: {
                0: {
                    slidesPerView: 1,
                    spaceBetween: 10
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 15
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 20
                }
            }
        });
    </script>

    <script>
        let offset = 6;

        document.getElementById('load-more-news').addEventListener('click', function() {
            fetch(`{{ route('news.loadMore') }}?offset=${offset}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        this.style.display = 'none';
                        return;
                    }

                    const container = document.getElementById('news-container');
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.classList.add('col-md-6', 'col-lg-4', 'news-item');
                        div.innerHTML = `
                    <div class="news-card">
                        <span class="news-date" style="color: #ff3366;">${new Date(item.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric'})}</span>
                        <p class="news-desc">${item.description.split(' ').slice(0,20).join(' ')}...
                        <a href="/news/${item.id}" target="_blank" class="">Read More</a>
                        </p>
                    </div>
                `;
                        container.appendChild(div);
                    });

                    offset += 6;
                });
        });
    </script>

</body>

</html>