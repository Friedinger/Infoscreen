<?php

if (!function_exists("auth")) {
	require __DIR__ . "/../auth.php";
}

$config = json_decode(file_get_contents(__DIR__ . "/../config.json", true));

header("Content-Type: application/json; charset=utf-8");

if (!function_exists('curl_init')) {
	die('CURL is not installed!');
}
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $config->departureUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($curl);
curl_close($curl);

echo $output;
