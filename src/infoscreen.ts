import { Departure } from "./types/departure";
import { Config } from "./types/config";

window.onload = async () => {
    const config: Config = await loadConfig();
    departures(config);
    news(config);
    setTimeout(() => {
        location.reload();
    }, getConfigValue(config, "reloadInterval") * 1000);
};

async function loadConfig(): Promise<Config> {
    try {
        const response = await fetch("config.json?t=" + new Date().getTime());
        if (!response.ok) throw new Error(`HTTP status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error("Error loading configuration file: ", error);
        return {
            reloadInterval: 60,
            newsInterval: 20,
            departureInterval: 20,
            news: [],
            departureUrl: "",
        };
    }
}

function news(config: Config): void {
    const newsDisplay = document.querySelector<HTMLImageElement>("#news");
    if (!newsDisplay?.parentElement) return;
    const newsBackground = newsDisplay.parentElement;
    const newsArr = config.news;
    if (!newsArr || newsArr.length === 0) return;

    const setNews = (news: string): void => {
        newsDisplay.src = "news/" + news;
        newsBackground.style.setProperty("--url", `url('news/${news}')`);
    };

    let currentNews = 0;
    setNews(newsArr[currentNews]);

    if (newsArr.length === 1) return;
    setInterval(() => {
        currentNews = (currentNews + 1) % newsArr.length;
        setNews(newsArr[currentNews]);
    }, getConfigValue(config, "newsInterval") * 1000);
}

function departures(config: Config): void {
    const departureTable =
        document.querySelector<HTMLTableElement>(".departure table");
    if (!departureTable) return;
    updateDepartures(config.departureUrl, departureTable);
    setInterval(() => {
        updateDepartures(config.departureUrl, departureTable);
    }, getConfigValue(config, "departureInterval") * 1000);
}

async function updateDepartures(
    url: string,
    departureTable: HTMLTableElement
): Promise<void> {
    fetch(url + "&t=" + new Date().getTime())
        .then((response) => {
            if (!response.ok)
                throw new Error(`HTTP status: ${response.status}`);
            return response.json();
        })
        .then((departures: Departure[]) => {
            departureTable
                .querySelectorAll("tr:not(.th)")
                .forEach((row) => row.remove());
            departures.forEach((departure) => {
                departureTable.appendChild(createRow(departure));
            });
        })
        .catch((error) => {
            console.error("Error updating departure data: ", error);
        });
}

function createRow(departure: Departure): HTMLTableRowElement {
    const timeReal = departure.realtimeDepartureTime;
    const timeIn = departure.realtimeDepartureTime - new Date().getTime();
    const timeDelay =
        departure.realtimeDepartureTime - departure.plannedDepartureTime;

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

function textOutput(...options: (string | undefined)[]): string {
    for (const element of options) {
        if (element != null) return element;
    }
    return "";
}

function timeClockOutput(time: number): string {
    return new Date(time).toLocaleTimeString("de-DE", {
        hour: "numeric",
        minute: "numeric",
    });
}

function timeOffsetOutput(time: number): string {
    const output = Math.round(time / 1000 / 60);
    if (output <= 0) return "Jetzt";
    return output + " min";
}

function timeDelayOutput(time: number): string {
    const output = Math.round(time / 1000 / 60);
    if (output > 0) return "+ " + output + " min";
    if (output < 0) return "- " + Math.abs(output) + " min";
    return "pünktlich";
}

function delayOutput(timeDelay: number): string {
    const time = timeDelayOutput(timeDelay);
    const color = time.startsWith("+") ? "delayed" : "intime";
    return `
        <td class="time delay ${color}">
            ${time}
        </td>
    `;
}

function getConfigValue(config: Config, key: keyof Config): number {
    return (config[key] ?? 60) as number;
}
