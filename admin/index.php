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
	if ($file["size"] > 50 * 1024 * 1024) return "Datei ist zu groß";

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

?>

<!DOCTYPE html>
<html lang="de">

<head>
	<title>Infoscreen Admin</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="icon" type="image/png" href="https://cdn.prod.website-files.com/64d11728a06601a00aeea217/64d117eaec6fe190c947cfbc_favicon.png" />
	<style>
		body {
			font-family: sans-serif;
		}
	</style>
</head>

<body>
	<main>
		<h1>Infoscreen Admin</h1>
		<p><a href="../">Zurück zum Infoscreen</a></p>
		<h3>News hinzufügen</h3>
		<p>Es können nur Bilder hochgeladen werden mit einer Größe von maximal 50MB.</p>
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="add">
			<input type="file" name="file">
			<input type="submit" name="submit" value="Hochladen">
			<label><?php echo add(); ?></label>
		</form>
		<br>
		<h3>News löschen</h3>
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="remove">
			<select name="news">
				<option value="">-- Bitte wählen --</option>
				<?php newsSelect(); ?>
			</select>
			<input type="submit" name="submit" value="Löschen">
			<label><?php echo remove(); ?></label>
		</form>
		<br>
		<h3>Einstellungen</h3>
		<?php $settings = settings(); ?>
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="settings">
			<label for="departureUrl">Abfahrten API URL </label> <input type="url" name="departureUrl" value="<?php echo $GLOBALS["config"]["departureUrl"]; ?>"><br>
			<label for="newsInterval">News Intervall (in Sekunden)</label> <input type="number" name="newsInterval" value="<?php echo $GLOBALS["config"]["newsInterval"]; ?>"><br>
			<label for="departureInterval">Abfahrten Intervall (in Sekunden)</label> <input type="number" name="departureInterval" value="<?php echo $GLOBALS["config"]["departureInterval"]; ?>"><br>
			<label for="reloadInterval">Neulade Intervall (in Sekunden)</label> <input type="number" name="reloadInterval" value="<?php echo $GLOBALS["config"]["reloadInterval"]; ?>"><br>
			<input type="submit" name="submit" value="Ändern">
			<label><?php echo $settings; ?></label>
		</form>
		<br>
		<h3>Credits</h3>
		<p>
			Infoscreen &#169; <?= date("Y") ?> Azubiwerk München<br>
			Version 1.0<br>
			Entwickelt von <a href="https://friedinger.org/">Manuel Weis</a>
		</p>
	</main>
</body>

</html>