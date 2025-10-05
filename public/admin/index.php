<?php

// Get config from json file
$GLOBALS["config"] = json_decode(file_get_contents(__DIR__ . "/../config.json"), true);

function add()
{
	// Skip if not submitted or action is not add
	if (!isset($_POST["submit"]) || $_POST["action"] != "add") return "&nbsp;";

	// Get uploaded file and check for file errors
	$file = $_FILES["file"];
	if ($file["error"] != 0) return "Fehler beim Hochladen der Datei";
	if (file_exists(__DIR__ . "/../news/" . $file["name"])) return "Datei existiert bereits";
	if (!getimagesize($file["tmp_name"])) return "Datei ist kein Bild";
	if ($file["size"] > 10_000_000) return "Datei ist zu groß";

	// Store file
	$move = move_uploaded_file($file["tmp_name"], __DIR__ . "/../news/" . $file["name"]);
	if (!$move) return "Fehler beim Speichern der Datei";

	// Add new news to config
	array_push($GLOBALS["config"]["news"], $file["name"]);
	$configUpdated = file_put_contents(__DIR__ . "/../config.json", json_encode($GLOBALS["config"]));
	if (!$configUpdated) {
		unlink(__DIR__ . "/../news/" . $file["name"]);
		return "Fehler beim Speichern der Konfiguration";
	}

	return "Datei erfolgreich hochgeladen";
}

function newsSelect()
{
	// Output select option for each news
	foreach ($GLOBALS["config"]["news"] as $news) {
		echo "<option value='$news'>$news</option>";
	}
}

function remove()
{
	// Skip if not submitted or action is not remove
	if (!isset($_POST["submit"]) || $_POST["action"] != "remove") return "&nbsp;";

	// Get news from POST
	$news = $_POST["news"];
	if (!$news) return "Bitte eine News auswählen";

	// Check if news exists
	$index = array_search($news, $GLOBALS["config"]["news"]);
	if ($index === false) return "Datei nicht gefunden";

	// Delete news file
	$delete = unlink(__DIR__ . "/../news/" . $news);
	if (!$delete) return "Fehler beim Löschen der Datei";

	// Remove news from config
	array_splice($GLOBALS["config"]["news"], $index, 1);
	$configUpdated = file_put_contents(__DIR__ . "/../config.json", json_encode($GLOBALS["config"]));
	if (!$configUpdated) return "Fehler beim Speichern der Konfiguration";

	return "Datei erfolgreich gelöscht";
}

function settings()
{
	// Skip if not submitted or action is not settings
	if (!isset($_POST["submit"]) || $_POST["action"] != "settings") return "&nbsp;";

	// Get settings from POST and validate them
	$departureUrl = $_POST["departureUrl"];
	$newsInterval = $_POST["newsInterval"];
	$departureInterval = $_POST["departureInterval"];
	$reloadInterval = $_POST["reloadInterval"];
	if (!$departureUrl) return "Bitte eine URL eingeben";
	if (!$newsInterval || !$departureInterval || !$reloadInterval) return "Bitte eine Zahl eingeben";

	// Update config with new settings
	$GLOBALS["config"]["departureUrl"] = $departureUrl;
	$GLOBALS["config"]["newsInterval"] = $newsInterval;
	$GLOBALS["config"]["departureInterval"] = $departureInterval;
	$GLOBALS["config"]["reloadInterval"] = $reloadInterval;
	$configUpdated = file_put_contents(__DIR__ . "/../config.json", json_encode($GLOBALS["config"]));
	if (!$configUpdated) return "Fehler beim Speichern der Konfiguration";

	return "Einstellungen erfolgreich geändert";
}
