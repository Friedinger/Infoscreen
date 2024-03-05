window.onload = () => {
	fetch("config.json")
		.then((response) => response.json())
		.then((config) => {
			console.log(config);
			departures(config);
			news(config);
			setTimeout(() => {
				location.reload(true);
			}, (config.reloadInterval ?? 60) * 1000);
		});
};

function news(config) {
	const newsDisplay = document.querySelector("#news");
	const news = config.news;
	if (config.news.length == 0) return;
	let currentNews = 0;
	newsDisplay.setAttribute("src", "news/" + news[currentNews]);
	if (config.news.length == 1) return;
	setInterval(() => {
		currentNews++;
		if (currentNews >= news.length) currentNews = 0;
		newsDisplay.setAttribute("src", "news/" + news[currentNews]);
	}, (config.newsInterval ?? 20) * 1000);
}

function departures(config) {
	loadDepartures(config);
	setInterval(() => {
		loadDepartures(config);
	}, (config.departureInterval ?? 20) * 1000);
}

function loadDepartures(config) {
	fetch(config.departureUrl)
		.then((response) => response.json())
		.then((data) => {
			let departure = document.querySelector(".departure table");
			departure.querySelectorAll(".tr").forEach((element) => {
				element.remove();
			});
			data.forEach((element) => {
				let timeReal = element.realtimeDepartureTime;
				let timePlan = element.plannedDepartureTime;
				let timeIn =
					element.realtimeDepartureTime - new Date().getTime();
				let timeDelay =
					element.realtimeDepartureTime -
					element.plannedDepartureTime;

				let row = document.createElement("tr");
				row.classList.add("tr");
				row.innerHTML = `
					<td><span class="${element.transportType}"></span>${textOutput(
					element.label
				)}</td>
					<td>${textOutput(element.destination)}</td>
					<td>${textOutput(element.platform, element.stopPositionNumber)}</td>
					<td>${timeOutput(timeReal)}</td><td>${timeOutput(timePlan)}</td>
					<td>${timeOutput(timeIn, "offset")}</td><td>${timeOutput(
					timeDelay,
					"delay"
				)}</td>
				`;
				departure.appendChild(row);
			});
		});
}

function textOutput() {
	for (const element of arguments) {
		if (element != null) return element;
	}
	return "";
}

function timeOutput(time, format = "clock") {
	let output;
	switch (format) {
		case "clock":
			return new Date(time).toLocaleTimeString("de-DE", {
				hour: "numeric",
				minute: "numeric",
			});
		case "offset":
			output = Math.round(new Date(time).getTime() / 1000 / 60);
			if (output <= 0) return "Jetzt";
			return output + " min";
		case "delay":
			output = Math.round(new Date(time).getTime() / 1000 / 60);
			if (output > 0) return "+ " + output + " min";
			if (output < 0) return "- " + Math.abs(output) + " min";
			if (output == 0) return "pünktlich";
	}
}
