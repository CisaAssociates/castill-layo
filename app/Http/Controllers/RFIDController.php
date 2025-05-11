<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RfidLog;
use GuzzleHttp\Client;

class RFIDController extends Controller
{
    public function index()
    {
        $latestScan = RfidLog::latest()->first();
        $photoPath = $latestScan ? $latestScan->photo_path : null;

        return view('welcome', [
            'rfid' => $latestScan ? $latestScan->rfid : null,
            'photo' => $photoPath ? 'storage/' . $photoPath : null,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'rfid' => 'required|string'
        ]);

        $rfid = $request->input('rfid');

        // Capture photo from DroidCam using cURL
        $cameraUrl = 'http://192.168.20.12:8080/video/jpeg'; // DroidCam "snapshot" URL
        $ch = curl_init($cameraUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false); // Exclude headers in the output
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout to prevent hanging forever
        $photoData = curl_exec($ch);

        // Debugging: check the cURL response
        $curlError = curl_error($ch);
        curl_close($ch);

        if (!$photoData) {
            // Log error info for debugging
            Log::error('cURL Error: ' . $curlError);
            return response()->json(['message' => 'Failed to capture photo'], 500);
        }

        // Extract a single JPEG image frame from the MJPEG stream
        // MJPEG stream is a continuous sequence of JPEG frames, so we need to extract the first one.
        $imageStart = strpos($photoData, "\xff\xd8"); // JPEG start marker
        $imageEnd = strpos($photoData, "\xff\xd9", $imageStart); // JPEG end marker

        if ($imageStart === false || $imageEnd === false) {
            return response()->json(['message' => 'No valid JPEG frame found'], 500);
        }

        // Extract the JPEG image data
        $photo = substr($photoData, $imageStart, $imageEnd - $imageStart + 2); // Include the JPEG end marker

        // Generate a unique filename
        $filename = 'rfid_photos/' . $rfid . '_' . time() . '.jpg';

        // Store the photo on disk (public storage)
        Storage::disk('public')->put($filename, $photo);

        // Save to DB
        DB::table('rfid_logs')->insert([
            'rfid' => $rfid,
            'photo_path' => $filename,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Saved successfully'], 200);
    }

    public function latest()
    {
        $latestScan = RfidLog::latest()->first();

        return response()->json([
            'rfid' => $latestScan->rfid ?? '',
            'photo' => $latestScan->photo_path ?? '',
        ]);
    }
}
