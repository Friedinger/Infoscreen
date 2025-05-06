<!DOCTYPE html>
<html lang="de">

<head>
	<title>Infoscreen</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="icon" type="image/png" href="https://cdn.prod.website-files.com/64d11728a06601a00aeea217/64d117eaec6fe190c947cfbc_favicon.png" />
	<link rel="stylesheet" type="text/css" href="infoscreen.css?t=<?= time() ?>" />
	<script type="text/javascript" src="infoscreen.js?t=<?= time() ?>"></script>
</head>

<body>
	<main>
		<section class="departure">
			<table>
				<tr class="th">
					<th>Linie</th>
					<th>Ziel</th>
					<th>Halt</th>
					<th>Real</th>
					<th>In</th>
					<th>Verspätung</th>
				</tr>
			</table>
		</section>
		<section class="text">
			<img src="https://cdn.prod.website-files.com/64d11728a06601a00aeea217/64d1204dc148da73086e0bdd_Haus%20lila.svg" alt="Logo" />
			<h1>
				Azubiwerk München<br>
				Infoscreen
			</h1>
			<p>Version: {{COMMIT_HASH}}</p>
			<p>github.com/Friedinger/Infoscreen</p>
		</section>
		<section class="news">
			<img id="news" src="" alt="News" />
		</section>
	</main>
</body>

</html>