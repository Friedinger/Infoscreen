<!DOCTYPE html>
<html lang="de">

<head>
	<title>Infoscreen</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
	<script>
		const css = document.createElement("link");
		css.rel = "stylesheet";
		css.type = "text/css";
		css.href = "infoscreen.css" + window.location.search;
		document.head.appendChild(css);
		const js = document.createElement("script");
		js.type = "text/javascript";
		js.src = "infoscreen.js" + window.location.search;
		document.head.appendChild(js);
	</script>
	<main>
		<section class="departure">
			<table>
				<tr class="th">
					<th>Linie</th>
					<th>Ziel</th>
					<th>Plattform</th>
					<th>Real</th>
					<th>In</th>
					<th>Verspätung</th>
				</tr>
			</table>
		</section>
		<section class="news">
			<img id="news" src="" alt="News" />
		</section>
	</main>
</body>

</html>