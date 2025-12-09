{{--
 | 2025 DeveMain
 |
 | All rights reserved. For internal use only.
 | Unauthorized copying, modification, or distribution is prohibited.
 |
 | @author    DeveMain <devemain@gmail.com>
 | @copyright 2025 DeveMain
 | @license   PROPRIETARY
 | @link      https://github.com/DeveMain
 --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeveMain - Demo Project</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(150deg, #66bcea 0%, #4b57a2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            text-align: center;
            color: white;
            padding: 2rem;
        }

        .logo {
            font-size: 4rem;
            animation: hoverFloat 2s infinite ease-in-out;
            display: inline-block;
        }

        @keyframes hoverFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 300;
        }

        .subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .features {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 3rem 0;
            flex-wrap: wrap;
        }

        .feature {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            min-width: 200px;
        }

        .tech-stack {
            margin: 2rem 0;
        }

        .tech-item {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .github-link {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            margin-top: 2rem;
            transition: transform 0.3s ease;
        }

        .github-link:hover {
            transform: translateY(-2px);
        }

        .blink {
            animation-name: blink;
            animation-duration: 1s;
            animation-iteration-count: infinite;
        }

        @keyframes blink {
            0%   {opacity: 0}
            49%  {opacity: 0}
            50%  {opacity: 1}
            100% {opacity: 1}
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">🚀</div>
    <h1>DeveMain</h1>
    <div class="subtitle">Demo Project</div>
    <div style="font-family: monospace; opacity: 0.5;">
        in development<span class="blink">_</span>
    </div>

    <div class="features">
        <div class="feature">
            <h3>⚡ Fast</h3>
            <p>Optimized Laravel application</p>
        </div>
        <div class="feature">
            <h3>🔧 Modern</h3>
            <p>Latest PHP & Laravel features</p>
        </div>
        <div class="feature">
            <h3>🐳 Dockerized</h3>
            <p>Containerized with Docker</p>
        </div>
        <div class="feature">
            <h3>☁️ Deployed</h3>
            <p>Live on Render.com</p>
        </div>
    </div>

    <div class="tech-stack">
        <span class="tech-item">PHP 8.4+</span>
        <span class="tech-item">Laravel 12.x</span>
        <span class="tech-item">Docker</span>
        <span class="tech-item">GitHub Actions</span>
    </div>

    <a href="https://github.com/devemain/demo" class="github-link">
        📁 View on GitHub
    </a>
</div>
</body>
</html>
