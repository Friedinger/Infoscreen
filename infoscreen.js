window.onload = async () => {
	const defaultConfig = {
		reloadInterval: 60,
		newsInterval: 20,
		departureInterval: 20,
	};

	const config = await loadConfig();
	departures();
	news();
	setTimeout(() => {
		location.reload(true);
	}, getConfigValue("reloadInterval") * 1000);

	async function loadConfig() {
		try {
			const response = await fetch(
				"config.json?t=" + new Date().getTime()
			);
			if (!response.ok)
				throw new Error("Fehler beim Laden der Konfigurationsdatei");
			return await response.json();
		} catch (error) {
			console.error("Fehler beim Laden der Konfigurationsdatei: ", error);
		}
	}

	function news() {
		const newsDisplay = document.querySelector("#news");
		const newsBackground = newsDisplay.parentElement;
		const news = config.news;
		if (config.news.length == 0) return;

		function setNews(news) {
			newsDisplay.src = "news/" + news;
			newsBackground.style.setProperty("--url", `url('news/${news}')`);
		}

		let currentNews = 0;
		setNews(news[currentNews]);

		if (config.news.length == 1) return;
		setInterval(() => {
			currentNews = (currentNews + 1) % news.length;
			setNews(news[currentNews]);
		}, getConfigValue("newsInterval") * 1000);
	}

	function departures(config) {
		const departureTable = document.querySelector(".departure table");
		updateDepartures(config.departureUrl);
		setInterval(() => {
			updateDepartures(config.departureUrl);
		}, getConfigValue("departureInterval") * 1000);

		async function updateDepartures(url) {
			try {
				const response = await fetch(
					url + "&t=" + new Date().getTime()
				);
				if (!response.ok)
					throw new Error(
						`Fehler beim Laden der Abfahrtsdaten (HTTP-Status: ${response.status})`
					);
				const departures = await response.json();
				departureTable
					.querySelectorAll("tr:not(.th)")
					.forEach((row) => row.remove());
				departures.forEach((departure) => {
					departureTable.appendChild(createRow(departure));
				});
			} catch (error) {
				console.error(
					"Fehler beim Aktualisieren der Abfahrtsdaten: ",
					error
				);
			}
		}

		function createRow(departure) {
			const timeReal = departure.realtimeDepartureTime;
			const timeIn =
				departure.realtimeDepartureTime - new Date().getTime();
			const timeDelay =
				departure.realtimeDepartureTime -
				departure.plannedDepartureTime;

			const row = document.createElement("tr");
			row.innerHTML = `
				<td class="line">
					<span class="type ${departure.transportType}"></span>
					<span class="label">${textOutput(departure.label)}</span>
				</td>
				<td class="destination">
					${textOutput(departure.destination)}
				</td>
				<td class="platform">
					${textOutput(departure.platform, departure.stopPositionNumber)}
				</td>
				<td class="time real">
					${timeClockOutput(timeReal)}
				</td>
				<td class="time in">
					${timeOffsetOutput(timeIn)}
				</td>
				${delayOutput(timeDelay)}
			`;
			return row;
		}

		function textOutput(...options) {
			for (const element of options) {
				if (element != null) return element;
			}
			return "";
		}

		function timeClockOutput(time) {
			return new Date(time).toLocaleTimeString("de-DE", {
				hour: "numeric",
				minute: "numeric",
			});
		}

		function timeOffsetOutput(time) {
			const output = Math.round(new Date(time).getTime() / 1000 / 60);
			if (output <= 0) return "Jetzt";
			return output + " min";
		}

		function timeDelayOutput(time) {
			const output = Math.round(new Date(time).getTime() / 1000 / 60);
			if (output > 0) return "+ " + output + " min";
			if (output < 0) return "- " + Math.abs(output) + " min";
			return "pünktlich";
		}

		function delayOutput(timeDelay) {
			const time = timeDelayOutput(timeDelay);
			const color = time.startsWith("+") ? "delayed" : "intime";
			return `
				<td class="time delay ${color}">
					${time}
				</td>
			`;
		}
	}

	function getConfigValue(key) {
		return config[key] ?? defaultConfig[key];
	}
};
