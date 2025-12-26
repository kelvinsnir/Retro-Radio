@extends('layouts.app')

@section('title', 'RetroWave - Music Streaming')

@section('content')
<header>
    <h1>RETRO<span class="title-accent">WAVE</span></h1>
    <p class="subtitle">Your Daily Dose of Nostalgia</p>
</header>

<!-- Search Bar -->
<div class="search-bar">
    <input type="text" class="search-input" id="searchInput" placeholder="Search for tracks, artists...">
</div>

<!-- Genre Filters -->
<div class="genre-filters">
    <button class="genre-btn active" data-genre="all">All</button>
    <button class="genre-btn" data-genre="electronic">Electronic</button>
    <button class="genre-btn" data-genre="pop">Pop</button>
    <button class="genre-btn" data-genre="rock">Rock</button>
    <button class="genre-btn" data-genre="jazz">Jazz</button>
    <button class="genre-btn" data-genre="classical">Classical</button>
    <button class="genre-btn" data-genre="hiphop">Hip Hop</button>
</div>

<!-- Player Card -->
<div class="player-card">
    <div class="album-art">
        <div class="album-content">
            <div class="vinyl-container">
                <div class="vinyl" id="vinyl"></div>
            </div>
            <h2 class="track-title" id="currentTitle">Select a track</h2>
            <p class="track-artist" id="currentArtist">No artist</p>
            <span class="genre-tag" id="currentGenre">Genre</span>
        </div>
    </div>

    <div class="controls-section">
        <div class="progress-container">
            <div class="progress-bar" id="progressBar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="time-labels">
                <span id="currentTime">0:00</span>
                <span id="totalTime">0:00</span>
            </div>
        </div>

        <div class="control-buttons">
            <button class="btn btn-small" id="prevBtn">
                <svg class="icon" viewBox="0 0 24 24">
                    <polygon points="19 20 9 12 19 4 19 20"></polygon>
                    <line x1="5" y1="19" x2="5" y2="5"></line>
                </svg>
            </button>

            <button class="btn btn-large" id="playBtn">
                <svg class="icon icon-lg" id="playIcon" viewBox="0 0 24 24">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
                <svg class="icon icon-lg" id="pauseIcon" viewBox="0 0 24 24" style="display: none;">
                    <rect x="6" y="4" width="4" height="16"></rect>
                    <rect x="14" y="4" width="4" height="16"></rect>
                </svg>
            </button>

            <button class="btn btn-small" id="nextBtn">
                <svg class="icon" viewBox="0 0 24 24">
                    <polygon points="5 4 15 12 5 20 5 4"></polygon>
                    <line x1="19" y1="5" x2="19" y2="19"></line>
                </svg>
            </button>
        </div>

        <div class="volume-control">
            <svg class="icon volume-icon" viewBox="0 0 24 24">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
            </svg>
            <input type="range" id="volumeSlider" min="0" max="100" value="70">
        </div>
    </div>
</div>

<!-- Playlist -->
<div class="playlist-card">
    <h3 class="playlist-title">Up Next</h3>
    <div id="playlist">
        <div class="loading">Loading tracks...</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // CSRF Token Setup
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Global State
    var tracks = @json($tracks);
    var currentTrackIndex = 0;
    var isPlaying = false;
    var audioPlayer = document.getElementById('audioPlayer');

    // DOM Elements
    var vinyl = document.getElementById('vinyl');
    var playBtn = document.getElementById('playBtn');
    var playIcon = document.getElementById('playIcon');
    var pauseIcon = document.getElementById('pauseIcon');
    var prevBtn = document.getElementById('prevBtn');
    var nextBtn = document.getElementById('nextBtn');
    var progressBar = document.getElementById('progressBar');
    var progressFill = document.getElementById('progressFill');
    var currentTimeEl = document.getElementById('currentTime');
    var totalTimeEl = document.getElementById('totalTime');
    var volumeSlider = document.getElementById('volumeSlider');
    var searchInput = document.getElementById('searchInput');
    var playlistEl = document.getElementById('playlist');

    // Initialize
    function init() {
        renderPlaylist();
        setupEventListeners();
        if (tracks.length > 0) {
            loadTrack(0);
        }
    }

    // Render Playlist
    function renderPlaylist() {
        if (tracks.length === 0) {
            playlistEl.innerHTML = '<div class="loading">No tracks available</div>';
            return;
        }

        var html = '';
        for (var i = 0; i < tracks.length; i++) {
            var track = tracks[i];
            var activeClass = i === currentTrackIndex ? 'active' : '';
            html += '<div class="track-item ' + activeClass + '" onclick="selectTrack(' + i + ')">';
            html += '<div class="track-info">';
            html += '<span class="track-number">' + (i + 1) + '</span>';
            html += '<div>';
            html += '<div class="track-name">' + track.title + '</div>';
            html += '<div class="track-artist-small">' + track.artist + '</div>';
            html += '</div></div>';
            html += '<div class="track-meta">';
            html += '<div class="track-duration">' + track.duration + '</div>';
            html += '<div class="track-genre">' + track.genre + '</div>';
            html += '</div></div>';
        }
        playlistEl.innerHTML = html;
    }

    // Load Track
    function loadTrack(index) {
        if (index < 0 || index >= tracks.length) return;

        currentTrackIndex = index;
        var track = tracks[index];

        // Update UI
        document.getElementById('currentTitle').textContent = track.title;
        document.getElementById('currentArtist').textContent = track.artist;
        document.getElementById('currentGenre').textContent = track.genre;
        document.getElementById('totalTime').textContent = track.duration;

        // Load audio
        audioPlayer.src = track.audio_url;
        audioPlayer.load();

        renderPlaylist();

        // Auto play if was playing
        if (isPlaying) {
            audioPlayer.play();
        }
    }

    // Select Track
    function selectTrack(index) {
        loadTrack(index);
        play();
    }

    // Play
    function play() {
        audioPlayer.play();
        isPlaying = true;
        vinyl.classList.add('spinning');
        playIcon.style.display = 'none';
        pauseIcon.style.display = 'block';
    }

    // Pause
    function pause() {
        audioPlayer.pause();
        isPlaying = false;
        vinyl.classList.remove('spinning');
        playIcon.style.display = 'block';
        pauseIcon.style.display = 'none';
    }

    // Toggle Play/Pause
    function togglePlay() {
        if (isPlaying) {
            pause();
        } else {
            play();
        }
    }

    // Next Track
    function nextTrack() {
        var nextIndex = (currentTrackIndex + 1) % tracks.length;
        loadTrack(nextIndex);
        if (isPlaying) play();
    }

    // Previous Track
    function prevTrack() {
        var prevIndex = (currentTrackIndex - 1 + tracks.length) % tracks.length;
        loadTrack(prevIndex);
        if (isPlaying) play();
    }

    // Update Progress
    function updateProgress() {
        if (audioPlayer.duration) {
            var percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
            progressFill.style.width = percent + '%';

            var mins = Math.floor(audioPlayer.currentTime / 60);
            var secs = Math.floor(audioPlayer.currentTime % 60);
            currentTimeEl.textContent = mins + ':' + (secs < 10 ? '0' : '') + secs;
        }
    }

    // Seek
    function seek(e) {
        var rect = progressBar.getBoundingClientRect();
        var percent = (e.clientX - rect.left) / rect.width;
        audioPlayer.currentTime = percent * audioPlayer.duration;
    }

    // Update Volume
    function updateVolume(value) {
        audioPlayer.volume = value / 100;
        volumeSlider.style.background = 'linear-gradient(to right, #a855f7 0%, #ec4899 ' + value + '%, #e5e7eb ' + value + '%, #e5e7eb 100%)';
    }

    // Search Tracks
    var searchTimeout;
    function searchTracks() {
        clearTimeout(searchTimeout);
        var query = searchInput.value.trim();

        if (query.length < 2) return;

        searchTimeout = setTimeout(function() {
            playlistEl.innerHTML = '<div class="loading">Searching...</div>';

            fetch('/search?q=' + encodeURIComponent(query))
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    tracks = data;
                    currentTrackIndex = 0;
                    renderPlaylist();
                    if (tracks.length > 0) {
                        loadTrack(0);
                    }
                })
                .catch(function(error) {
                    console.error('Search error:', error);
                    playlistEl.innerHTML = '<div class="loading">Error loading tracks</div>';
                });
        }, 500);
    }

    // Load Genre
    function loadGenre(genre) {
        playlistEl.innerHTML = '<div class="loading">Loading ' + genre + ' tracks...</div>';

        var url = genre === 'all' ? '/' : '/genre/' + genre;

        fetch(url)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                tracks = data;
                currentTrackIndex = 0;
                renderPlaylist();
                if (tracks.length > 0) {
                    loadTrack(0);
                }
            })
            .catch(function(error) {
                console.error('Genre load error:', error);
                playlistEl.innerHTML = '<div class="loading">Error loading tracks</div>';
            });
    }

    // Event Listeners
    function setupEventListeners() {
        // Player controls
        playBtn.addEventListener('click', togglePlay);
        nextBtn.addEventListener('click', nextTrack);
        prevBtn.addEventListener('click', prevTrack);
        progressBar.addEventListener('click', seek);

        // Audio events
        audioPlayer.addEventListener('timeupdate', updateProgress);
        audioPlayer.addEventListener('ended', nextTrack);
        audioPlayer.addEventListener('loadedmetadata', function() {
            var mins = Math.floor(audioPlayer.duration / 60);
            var secs = Math.floor(audioPlayer.duration % 60);
            totalTimeEl.textContent = mins + ':' + (secs < 10 ? '0' : '') + secs;
        });

        // Volume
        volumeSlider.addEventListener('input', function(e) {
            updateVolume(e.target.value);
        });

        // Search
        searchInput.addEventListener('input', searchTracks);

        // Genre buttons
        var genreBtns = document.querySelectorAll('.genre-btn');
        for (var i = 0; i < genreBtns.length; i++) {
            genreBtns[i].addEventListener('click', function() {
                // Remove active class from all
                var allBtns = document.querySelectorAll('.genre-btn');
                for (var j = 0; j < allBtns.length; j++) {
                    allBtns[j].classList.remove('active');
                }

                // Add active class to clicked
                this.classList.add('active');

                // Load genre
                var genre = this.getAttribute('data-genre');
                if (genre !== 'all') {
                    loadGenre(genre);
                }
            });
        }
    }

    // Initialize volume
    updateVolume(70);

    // Start the app
    init();
</script>
@endpush
