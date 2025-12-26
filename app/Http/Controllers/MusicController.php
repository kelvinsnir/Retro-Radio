<?php

namespace App\Http\Controllers;

use App\Services\JamendoService;
use App\Models\Track;
use App\Models\Playlist;
use Illuminate\Http\Request;

class MusicController extends Controller
{
    protected $jamendo;

    public function __construct(JamendoService $jamendo)
    {
        $this->jamendo = $jamendo;
    }

    public function index()
    {
        // Get popular tracks from Jamendo
        $jamendoTracks = $this->jamendo->getPopularTracks(10);

        // Transform to our format
        $tracks = collect($jamendoTracks)->map(function ($track) {
            return [
                'id' => $track['id'],
                'title' => $track['name'],
                'artist' => $track['artist_name'],
                'duration' => $this->formatDuration($track['duration']),
                'audio_url' => $track['audio'],
                'image_url' => $track['image'] ?? null,
                'genre' => $track['musicinfo']['tags']['genres'][0] ?? 'Unknown'
            ];
        });

        return view('music.index', compact('tracks'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $jamendoTracks = $this->jamendo->searchTracks($query);

        $tracks = collect($jamendoTracks)->map(function ($track) {
            return [
                'id' => $track['id'],
                'title' => $track['name'],
                'artist' => $track['artist_name'],
                'duration' => $this->formatDuration($track['duration']),
                'audio_url' => $track['audio'],
                'image_url' => $track['image'] ?? null,
                'genre' => $track['musicinfo']['tags']['genres'][0] ?? 'Unknown'
            ];
        });

        return response()->json($tracks);
    }

    public function genre($genre)
    {
        $jamendoTracks = $this->jamendo->getTracksByGenre($genre, 20);

        $tracks = collect($jamendoTracks)->map(function ($track) {
            return [
                'id' => $track['id'],
                'title' => $track['name'],
                'artist' => $track['artist_name'],
                'duration' => $this->formatDuration($track['duration']),
                'audio_url' => $track['audio'],
                'image_url' => $track['image'] ?? null,
                'genre' => $track['musicinfo']['tags']['genres'][0] ?? 'Unknown'
            ];
        });

        return response()->json($tracks);
    }

    public function saveTrack(Request $request)
    {
        $request->validate([
            'jamendo_id' => 'required',
            'title' => 'required',
            'artist' => 'required',
            'duration' => 'required|integer',
            'audio_url' => 'required|url',
        ]);

        $track = Track::updateOrCreate(
            ['jamendo_id' => $request->jamendo_id],
            $request->all()
        );

        return response()->json(['success' => true, 'track' => $track]);
    }

    public function play($id)
    {
        $track = Track::where('jamendo_id', $id)->first();

        if ($track) {
            $track->incrementPlayCount();
        }

        return response()->json(['success' => true]);
    }

    private function formatDuration($seconds)
    {
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        return sprintf('%d:%02d', $minutes, $secs);
    }
}
