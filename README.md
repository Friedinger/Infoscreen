# Infoscreen

Infoscreen for [Azubiwerk München](https://azubiwerk-muenchen.de/) that displays departures of public transport and changing, custom news.

Departure times are fetched from API of local transport operator [MVG](https://www.mvg.de/) for specific station and displayed on webpage.

Custom news are displayed alternating, switching is automatic with custom interval.

Settings can be changed in admin portal:
- Uploading of new news
- Deleting news
- Changing departure api url
- Setting intervals for switching news, updating departures and reloading page

Interval for reloading page is required to get new settings if infoscreen is displayed on kiosk screen like [FullPageOS](https://github.com/guysoft/FullPageOS).

## Installation

- Download files from repository and upload to php webserver
- `.gitignore`, `LICENSE` and `README.md` can be removed
- Rename `config_sample.json` to `config.json`
- Edit `AuthUserFile`-path in `admin/.htaccess` to point to `.htpasswd`-file or secure `admin`-folder differently (not recommended)
- Access admin-page in browser and change settings depending on specific requirements
- Access index-file in browser to view infoscreen
