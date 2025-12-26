<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RetroWave - Music Streaming')</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #fbbf24, #ec4899, #a855f7, #60a5fa);
            font-family: 'Arial', sans-serif;
            overflow-x: hidden;
            position: relative;
        }

        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.7;
            animation: pulse 6s ease-in-out infinite;
            pointer-events: none;
        }

        .orb1 {
            top: 80px;
            left: 40px;
            width: 256px;
            height: 256px;
            background: #fef08a;
        }

        .orb2 {
            top: 160px;
            right: 40px;
            width: 288px;
            height: 288px;
            background: #fbcfe8;
            animation-delay: 2s;
        }

        .orb3 {
            bottom: -128px;
            left: 33%;
            width: 384px;
            height: 384px;
            background: #d8b4fe;
            animation-delay: 4s;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 0.5; }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .container {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 16px;
        }

        header {
            text-align: center;
            margin-bottom: 48px;
        }

        h1 {
            font-size: 4.5rem;
            font-weight: bold;
            color: white;
            text-shadow: 4px 4px 0px rgba(0,0,0,0.2), 8px 8px 0px rgba(255,107,107,0.3);
            font-family: Impact, sans-serif;
            letter-spacing: 3px;
            margin-bottom: 16px;
        }

        .title-accent {
            color: #fef08a;
        }

        .subtitle {
            font-size: 1.5rem;
            color: white;
            font-weight: 300;
            letter-spacing: 4px;
        }

        .search-bar {
            max-width: 600px;
            margin: 0 auto 32px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 16px 24px;
            border-radius: 50px;
            border: none;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 10px 30px rgba(168, 85, 247, 0.3);
        }

        .genre-filters {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .genre-btn {
            padding: 10px 24px;
            border-radius: 50px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .genre-btn:hover, .genre-btn.active {
            background: linear-gradient(135deg, #a855f7, #ec4899);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .player-card {
            max-width: 896px;
            margin: 0 auto 32px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .album-art {
            position: relative;
            height: 384px;
            background: linear-gradient(135deg, #a78bfa, #f472b6, #fb923c);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .album-art::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.2);
        }

        .album-content {
            position: relative;
            z-index: 10;
            text-align: center;
        }

        .vinyl-container {
            width: 192px;
            height: 192px;
            margin: 0 auto 24px;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(8px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .vinyl {
            width: 160px;
            height: 160px;
            background: linear-gradient(135deg, #fbbf24, #ec4899);
            border-radius: 50%;
        }

        .vinyl.spinning {
            animation: spin 3s linear infinite;
        }

        .track-title {
            font-size: 2.25rem;
            font-weight: bold;
            color: white;
            margin-bottom: 8px;
        }

        .track-artist {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
        }

        .genre-tag {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 16px;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(4px);
            border-radius: 9999px;
            font-size: 0.875rem;
            color: white;
        }

        .controls-section {
            padding: 32px;
        }

        .progress-container {
            margin-bottom: 24px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 9999px;
            overflow: hidden;
            cursor: pointer;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(to right, #a855f7, #ec4899);
            width: 0%;
            transition: width 0.15s;
        }

        .time-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 8px;
        }

        .control-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            margin-bottom: 24px;
        }

        .btn {
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: scale(1.1);
        }

        .btn-small {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #a78bfa, #f472b6);
        }

        .btn-large {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #a855f7, #ec4899);
        }

        .volume-control {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .volume-icon {
            color: #a855f7;
        }

        input[type="range"] {
            flex: 1;
            height: 8px;
            border-radius: 8px;
            appearance: none;
            cursor: pointer;
            background: linear-gradient(to right, #a855f7 0%, #ec4899 70%, #e5e7eb 70%, #e5e7eb 100%);
        }

        input[type="range"]::-webkit-slider-thumb {
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: white;
            border: 2px solid #a855f7;
            cursor: pointer;
        }

        input[type="range"]::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: white;
            border: 2px solid #a855f7;
            cursor: pointer;
        }

        .playlist-card {
            max-width: 896px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 32px;
        }

        .playlist-title {
            font-size: 1.875rem;
            font-weight: bold;
            background: linear-gradient(to right, #a855f7, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 24px;
        }

        .track-item {
            padding: 16px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .track-item:hover {
            background: #f3f4f6;
        }

        .track-item.active {
            background: linear-gradient(to right, #a78bfa, #f472b6);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .track-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .track-number {
            font-size: 1.5rem;
            font-weight: bold;
            opacity: 0.5;
        }

        .track-name {
            font-weight: 600;
            font-size: 1.125rem;
            margin-bottom: 4px;
        }

        .track-artist-small {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .track-item.active .track-artist-small {
            color: rgba(255, 255, 255, 0.8);
        }

        .track-meta {
            text-align: right;
        }

        .track-duration {
            font-size: 0.875rem;
            opacity: 0.75;
            margin-bottom: 4px;
        }

        .track-genre {
            font-size: 0.75rem;
            opacity: 0.6;
        }

        .track-item.active .track-genre {
            color: rgba(255, 255, 255, 0.6);
        }

        .icon {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }

        .icon-lg {
            width: 32px;
            height: 32px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: white;
            font-size: 1.2rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="bg-orb orb1"></div>
    <div class="bg-orb orb2"></div>
    <div class="bg-orb orb3"></div>

    <div class="container">
        @yield('content')
    </div>

    <audio id="audioPlayer" style="display: none;"></audio>

    @stack('scripts')
</body>
</html>
