<?php
// User-Agent check karte hain
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Sirf Tivimate aur OTT Navigator ko allow karein
if (strpos($userAgent, 'TiviMate') !== false || strpos($userAgent, 'OTT Navigator') !== false) {
    // M3U Header
    header("Content-Type: application/x-mpegurl");
    readfile("playlist.m3u");
} else {
    // Unauthorized access ke liye block message
    http_response_code(403);
    echo "Access Denied! This playlist is only for Tivimate or OTT Navigator.";
}
?>
