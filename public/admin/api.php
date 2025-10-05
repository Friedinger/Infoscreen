<?php
$configPath = __DIR__ . "/../config.json";
$newsDir = __DIR__ . "/../news/";
$method = $_SERVER["REQUEST_METHOD"];

// --- MAIN ROUTER ---

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

switch ($method) {
	case "POST":
		handlePost($configPath, $newsDir);
		break;
	case "DELETE":
		handleDelete($configPath, $newsDir);
		break;
	case "PATCH":
		handlePatch($configPath);
		break;
	default:
		header("Allow: POST, DELETE, PATCH");
		sendResponse(405, ["error" => "Method Not Allowed"]);
		break;
}

exit();

// --- ENDPOINT HANDLERS ---

/**
 * Handles POST requests to add a new news item.
 */
function handlePost($configPath, $newsDir)
{
	if (empty($_FILES["file"])) {
		sendResponse(400, ["error" => "No file uploaded."]);
	}

	$file = $_FILES["file"];

	// File validation
	if ($file["error"] !== UPLOAD_ERR_OK) {
		sendResponse(400, ["error" => "File upload error: " . $file["error"]]);
	}

	$maxSize = 10 * 1024 * 1024; // 10MB
	$allowedMimeTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
	$fileMimeType = mime_content_type($file["tmp_name"]);

	if ($file["size"] > $maxSize) {
		sendResponse(400, ["error" => "File exceeds maximum size of 10MB."]);
	}
	if (!in_array($fileMimeType, $allowedMimeTypes)) {
		sendResponse(400, ["error" => "Invalid file type."]);
	}

	// Sanitize filename and move file
	$fileName = preg_replace("/[^a-zA-Z0-9-_\\.]/", "", basename($file["name"]));
	$targetPath = $newsDir . $fileName;

	if (file_exists($targetPath)) {
		sendResponse(409, ["error" => "File already exists."]);
	}

	if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
		sendResponse(500, ["error" => "Could not move uploaded file."]);
	}

	// Update config
	$config = getConfig($configPath);
	if (!in_array($fileName, $config["news"])) {
		$config["news"][] = $fileName;
		saveConfig($configPath, $config);
	}

	sendResponse(201, ["success" => "File uploaded and config updated.", "fileName" => $fileName]);
}

/**
 * Handles DELETE requests to remove a news item.
 */
function handleDelete($configPath, $newsDir)
{
	$json = file_get_contents("php://input");
	$data = json_decode($json, true);

	if (empty($data["news"])) {
		sendResponse(400, ["error" => "JSON body must contain 'news' key with the filename."]);
	}

	$fileNameToRemove = basename($data["news"]);
	$filePathToRemove = $newsDir . $fileNameToRemove;

	$config = getConfig($configPath);

	if (!in_array($fileNameToRemove, $config["news"])) {
		sendResponse(404, ["error" => "File not found in configuration."]);
	}

	// Delete file
	if (file_exists($filePathToRemove)) {
		if (strpos(realpath($filePathToRemove), realpath($newsDir)) === 0) {
			unlink($filePathToRemove);
		} else {
			sendResponse(403, ["error" => "Forbidden: File path is outside the allowed directory."]);
		}
	}

	// Remove from config
	$config["news"] = array_filter($config["news"], function ($item) use ($fileNameToRemove) {
		return $item !== $fileNameToRemove;
	});

	saveConfig($configPath, $config);
	sendResponse(200, ["success" => "News item deleted.", "fileName" => $fileNameToRemove]);
}

/**
 * Handles PATCH requests to update settings.
 */
function handlePatch($configPath)
{
	$json = file_get_contents("php://input");
	$updates = json_decode($json, true);

	if (json_last_error() !== JSON_ERROR_NONE) {
		sendResponse(400, ["error" => "Invalid JSON provided."]);
	}

	$config = getConfig($configPath);
	$updatedKeys = [];

	foreach ($updates as $key => $value) {
		if (array_key_exists($key, $config)) {
			$config[$key] = $value;
			$updatedKeys[] = $key;
		}
	}

	if (empty($updatedKeys)) {
		sendResponse(400, ["error" => "No valid settings to update. Sent keys must already exist in config."]);
	}

	saveConfig($configPath, $config);
	sendResponse(200, ["success" => "Settings updated.", "updatedKeys" => $updatedKeys]);
}

// --- UTILITY FUNCTIONS ---

/**
 * Sends a JSON response.
 * @param int $statusCode HTTP status code.
 * @param array $data The data to encode as JSON.
 */
function sendResponse($statusCode, $data)
{
	http_response_code($statusCode);
	echo json_encode($data);
	exit();
}

/**
 * Reads and decodes the configuration file.
 * @param string $path Path to the config file.
 * @return array The configuration data.
 */
function getConfig($path)
{
	if (!file_exists($path)) {
		sendResponse(500, ["error" => "Configuration file not found."]);
	}
	$json = file_get_contents($path);
	$config = json_decode($json, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		sendResponse(500, ["error" => "Error decoding configuration JSON."]);
	}
	return $config;
}

/**
 * Saves data to the configuration file.
 * @param string $path Path to the config file.
 * @param array $data The data to save.
 */
function saveConfig($path, $data)
{
	if (isset($data["news"])) $data["news"] = array_values($data["news"]);
	$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	if (file_put_contents($path, $json) === false) {
		sendResponse(500, ["error" => "Failed to write to config file."]);
	}
}
