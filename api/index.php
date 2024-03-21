<?php

if (!class_exists("Auth")) {
	require __DIR__ . "/../auth.php";
}

$config = json_decode(file_get_contents(__DIR__ . "/../config.json", true));

header("Content-Type: application/json; charset=utf-8");
echo file_get_contents($config->departureUrl);
