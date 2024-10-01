<?php
// Get the channel_id from the query string
$channel_id = isset($_GET['id']) ? $_GET['id'] : null;

// If no channel_id is provided, return an error
if ($channel_id === null) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "No channel_id provided"]);
    exit();
}

// User agent check
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$allowed_user_agents = [
    'Denver1769', 
    'TiviMate/5.0.4', 
    'Kodi', 
    'MXPlayer', 
    'Lavf/58.76.100', 
    'Player', 
    'VLC', 
    'ExoPlayer', 
    'OTT Navigator', 
    'Dalvik/2.1.0'
]; // Add any other allowed user agents here

$is_allowed = false;
foreach ($allowed_user_agents as $allowed_user_agent) {
    if (strpos($user_agent, $allowed_user_agent) !== false) {
        $is_allowed = true;
        break;
    }
}

// If user agent is not allowed, echo "Hello World" and exit
if (!$is_allowed) {
    echo "Hello World";
    exit();
}

// JSON URL to fetch the data from
$json_url = "https://play.denver1769.in/jc/JC.json";

// Initialize a cURL session to fetch the JSON data
$ch = curl_init();

// Set the options for the cURL session
curl_setopt($ch, CURLOPT_URL, $json_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification if needed

// Execute the cURL request and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
    curl_close($ch);
    exit();
}

// Close the cURL session
curl_close($ch);

// Decode the JSON response
$data = json_decode($response, true);

// Check if JSON decoding was successful
if ($data === null) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Failed to decode JSON"]);
    exit();
}

// Initialize a variable to store the link
$link = null;

// Iterate through the channels and search for the matching channel_id
foreach ($data as $entry) {
    foreach ($entry['channels'] as $channel) {
        if ($channel['channel_id'] == $channel_id) {
            // If a match is found, get the link
            $link = $channel['link'];
            break 2; // Exit both loops when a match is found
        }
    }
}

// Check if a link was found for the given channel_id
if ($link !== null) {
    // Fetch the content from the fetched link using headers
    $ch_link = curl_init();

    // Add headers to the cURL request
    $headers = [
        "Host: jcevents.jiocinema.com",
        "Connection: keep-alive",
        'sec-ch-ua: "Chromium";v="94", "Google Chrome";v="94", ";Not A Brand";v="99"',
        "sec-ch-ua-mobile: ?1",
        'sec-ch-ua-platform: "Android"',
        "Upgrade-Insecure-Requests: 1",
        "User-Agent: Mozilla/5.0 (Linux; Android 11; RMX3171) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.85 Mobile Safari/537.36",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
        "Sec-Fetch-Site: none",
        "Sec-Fetch-Mode: navigate",
        "Sec-Fetch-User: ?1",
        "Sec-Fetch-Dest: document",
        "Accept-Encoding: gzip, deflate, br",
        "Accept-Language: en-US,en;q=0.9,hi;q=0.8,fr;q=0.7,pt;q=0.6"
    ];

    curl_setopt($ch_link, CURLOPT_URL, $link);
    curl_setopt($ch_link, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_link, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verification if needed
    curl_setopt($ch_link, CURLOPT_HTTPHEADER, $headers);  // Add the custom headers
    curl_setopt($ch_link, CURLOPT_ENCODING, ''); // Allow cURL to handle any encoding

    // Execute the cURL request and get the content from the link
    $link_content = curl_exec($ch_link);

    // Check for cURL errors
    if (curl_errno($ch_link)) {
        echo 'cURL Error: ' . curl_error($ch_link);
        curl_close($ch_link);
        exit();
    }
