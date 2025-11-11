<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class VideoStreamController extends Controller
{
    public function stream(Request $request, $filename)
    {
        // Vérifie si le fichier existe sur le disque 'public'
        if (!Storage::disk('public')->exists('lessons_video/' . $filename)) {
            abort(404);
        }

        $path = Storage::disk('public')->path('lessons_video/' . $filename);
        $size = Storage::disk('public')->size('lessons_video/' . $filename);
        $file = fopen($path, 'rb');

        $start = 0;
        $length = $size;
        $status = 200;
        $headers = [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes',
        ];

        if ($request->headers->has('Range')) {
            preg_match('/bytes=(\d*)-(\d*)/', $request->header('Range'), $matches);

            $start = intval($matches[1]);
            $end = $matches[2] !== '' ? intval($matches[2]) : $size - 1;
            $length = $end - $start + 1;

            fseek($file, $start);
            $status = 206;
            $headers['Content-Range'] = "bytes $start-$end/$size";
            $headers['Content-Length'] = $length;
        } else {
            $headers['Content-Length'] = $size;
        }

        return response()->stream(function () use ($file, $length) {
            $chunkSize = 1024 * 1024; // 1MB
            $bytesSent = 0;

            while (!feof($file) && $bytesSent < $length) {
                $buffer = fread($file, $chunkSize);
                echo $buffer;
                flush();
                $bytesSent += strlen($buffer);
            }

            fclose($file);
        }, $status, $headers);
    }
}
