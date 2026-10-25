<?php
declare(strict_types=1);

require_once __DIR__ . '/security.php';

// อนุญาตเฉพาะผู้ที่ Login แล้ว
require_login();

// ตำแหน่งไฟล์รูปและวิดีโอ
$leftBanner  = 'assets/images/banner-left.png';
$rightBanner = 'assets/images/banner-right.png';
$videoPath   = 'assets/video.mp4';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Main Page | Secure Portal</title>

    <style>
        /* =========================
           GLOBAL
        ========================= */
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            background: #101010;
            color: #e8c66b;
            font-family: Arial, sans-serif;
        }

        /* =========================
           SIDE BANNERS
           รูปต้นฉบับ 600 x 1200 px
        ========================= */
        .banner {
            position: fixed;
            top: 0;
            bottom: 0;
            width: 200px;
            overflow: hidden;
            background: #000;
            z-index: 10;
        }

        .banner-left {
            left: 0;
            border-right: 1px solid #b89646;
        }

        .banner-right {
            right: 0;
            border-left: 1px solid #b89646;
        }

        .banner img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }

        /* =========================
           MAIN CONTENT
        ========================= */
        .main-content {
            min-height: 100vh;
            margin-left: 200px;
            margin-right: 200px;
            padding: 24px;

            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }

        /* =========================
           HEADER
        ========================= */
        .topbar {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;

            padding-bottom: 16px;
            border-bottom: 1px solid #8f7131;
        }

        .topbar h1 {
            margin: 0;
            font-size: clamp(22px, 3vw, 34px);
            color: #e8c66b;
        }

        .logout-button {
            display: inline-block;
            padding: 10px 18px;

            background: #b18a32;
            color: #111;
            text-decoration: none;
            font-weight: bold;

            border-radius: 6px;
            transition: background 0.2s ease;
        }

        .logout-button:hover {
            background: #e8c66b;
        }

        .logout-button:focus-visible {
            outline: 2px solid #fff;
            outline-offset: 3px;
        }

        /* =========================
           VIDEO
        ========================= */
        .video-container {
            width: 100%;
            max-width: 1100px;

            background: #000;
            border: 1px solid #8f7131;
            border-radius: 10px;
            overflow: hidden;
        }

        .video-container video {
            display: block;
            width: 100%;
            max-height: 75vh;

            object-fit: contain;
            background: #000;
        }

        /* =========================
           FOOTER
        ========================= */
        .footer-text {
            margin-top: auto;
            padding-top: 12px;

            color: #c6b47f;
            text-align: center;
            font-size: 13px;
        }

        /* =========================
           TABLET
        ========================= */
        @media (max-width: 900px) {
            .banner {
                width: 100px;
            }

            .main-content {
                margin-left: 100px;
                margin-right: 100px;
                padding: 16px;
            }
        }

        /* =========================
           MOBILE
        ========================= */
        @media (max-width: 600px) {
            .banner {
                width: 55px;
            }

            .main-content {
                margin-left: 55px;
                margin-right: 55px;
                padding: 10px;
                gap: 16px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .topbar h1 {
                font-size: 22px;
            }

            .logout-button {
                padding: 8px 12px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <!-- =========================
         LEFT BANNER
    ========================= -->
    <aside class="banner banner-left">
        <img
            src="<?= htmlspecialchars($leftBanner, ENT_QUOTES, 'UTF-8') ?>"
            alt="Left Banner"
        >
    </aside>

    <!-- =========================
         RIGHT BANNER
    ========================= -->
    <aside class="banner banner-right">
        <img
            src="<?= htmlspecialchars($rightBanner, ENT_QUOTES, 'UTF-8') ?>"
            alt="Right Banner"
        >
    </aside>

    <!-- =========================
         MAIN PAGE CONTENT
    ========================= -->
    <main class="main-content">

        <header class="topbar">
            <h1>Main Page</h1>

            <a class="logout-button" href="logout.php">
                Logout
            </a>
        </header>

        <!-- VIDEO PLAYER -->
        <section class="video-container">
            <video
                autoplay
                muted
                loop
                playsinline
                controls
            >
                <source
                    src="<?= htmlspecialchars($videoPath, ENT_QUOTES, 'UTF-8') ?>"
                    type="video/mp4"
                >

                เบราว์เซอร์ของคุณไม่รองรับวิดีโอ HTML5
            </video>
        </section>

        <footer class="footer-text">
            Secure Portal
        </footer>

    </main>

</body>
</html>
