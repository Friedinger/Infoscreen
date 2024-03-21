<?php

if (!function_exists("auth")) {
	require __DIR__ . "/auth.php";
}

$config = json_decode(file_get_contents(__DIR__ . "/config.json"), true);

$installPath = $config["installPath"];
$installPath = rtrim($installPath, "/") . "/";
$installPath = "/" . ltrim($installPath, "/");
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri = explode($installPath, $uri, 2)[1] ?? $uri;
$uri = "/" . ltrim($uri, "/");

switch ($uri) {
	case "/":
		require __DIR__ . "/infoscreen.php";
		break;
	case "/config.json":
		header("Content-Type: application/json");
		require __DIR__ . "/config.json";
		break;
	case "/infoscreen.css":
		header("Content-Type: text/css");
		require __DIR__ . "/infoscreen.css";
		break;
	case "/infoscreen.js":
		header("Content-Type: text/javascript");
		require __DIR__ . "/infoscreen.js";
		break;
	case "/api/":
		require __DIR__ . "/api/index.php";
		break;
	default:
		error();
}
