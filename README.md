# Infoscreen

Infoscreen for [Azubiwerk München](https://azubiwerk-muenchen.de/) that displays departures of public transport and changing, custom news.

## Features

-   Departure times are fetched from API of local transport operator [MVG](https://www.mvg.de/) for specific station and displayed on a webpage.

-   Custom news are displayed alternating, switching is automatic with custom interval.

Settings can be changed in admin portal:

-   Uploading of new news
-   Deleting news
-   Changing departure api url
-   Setting intervals for switching news, updating departures and reloading page (Interval for reloading page is required to get new settings if Infoscreen is displayed on kiosk screen like [FullPageOS](https://github.com/guysoft/FullPageOS))

## Setup webpage

1.  Download [files](https://github.com/Friedinger/Infoscreen/archive/refs/heads/main.zip) from the repository and upload them to your PHP webserver
2.  Remove `.gitignore`, `LICENSE` and `README.md`
3.  Rename `config_sample.json` to `config.json`
4.  Protect at least the `admin` directory, better the whole Infoscreen, with a login (e.g. `.htaccess` and `.htpasswd`)
    -   Example for `.htaccess`:
        ```apache
        AuthType Basic
        AuthName "Restricted Area"
        AuthUserFile /path/to/.htpasswd
        Require valid-user
        ```
    -   Example for `.htpasswd`:
        ```apache
        username:hashed_password
        ```
5.  Access the admin-page in your browser and change settings depending on specific requirements
6.  Access the index-file to view Infoscreen

## Install FullPageOS on Raspberry Pi

1. Write [FullPageOS](https://github.com/guysoft/FullPageOS) image to SD card using [Raspberry Pi Imager](https://www.raspberrypi.com/software/)

    - The image is located in `Other specific-purpose OS` -> `FullPageOS`
    - Nightly build is recommended (tested with version 0.14.0)
    - Set ssh username, ssh password, timezone and keyboard layout in imager settings (`Strg+Shift+X`). Setting Wi-Fi credentials and hostname here is not tested.

2. Edit configs on boot partition of SD card

    - Set URL to webserver in `fullpageos.txt`
    - Set Wi-Fi credentials in `wifi.nmconnection` (if needed)
    - Change `splash.png` for a custom boot logo (optional)

3. Insert SD card into Raspberry Pi and boot

4. Connect to Raspberry Pi via SSH

5. Change VNC password (recommended)

    ```bash
    sudo ~/scripts/setX11vncPass yourpassword
    ```

    Replace `yourpassword` with your desired password,
    reboot to apply changes.

6. Optionally change the background image in `/opt/custompios/background.png`. It can be uploaded via `scp`.

7. Done. The Infoscreen now should be displayed on the screen connected to the Raspberry Pi.

### Displaying Infoscreen if protected with login

If the Infoscreen is protected with a login, you need to extend some of the scripts located in `~/scripts/`. The following changes work if the login can be automatically filled by the Chromium password manager and just need to be sent with the enter key, for example if the login is protected with http basic auth using `.htaccess` and `.htpasswd`.

1. Connect to Raspberry Pi via VNC or physical keyboard and mouse, login to your Infoscreen and store the credentials in Chromium password manager.

2. Edit `~/scripts/start_chromium_browser`:

    Original file: https://github.com/guysoft/FullPageOS/blob/devel/src/modules/fullpageos/filesystem/opt/custompios/scripts/start_chromium_browser

    - Add the following line before the standard behavior `chromium-browser` command (before line 17 in original file):

        ```bash
        /opt/custompios/scripts/login &
        ```

    - Optionally add the following to the beginning of the file after the shebang line `#!/bin/bash` (after line 1 in original file):

        ```bash
        export DISPLAY=:0
        WID=$(xdotool search --onlyvisible --class chromium|head -1)
        xdotool windowactivate ${WID}
        xdotool mousemove 50 50
        ```

        This moves the mouse to the top left corner of the screen to indicate that chromium is now starting.

3. Add the script `~/scripts/login` with the following content:

    ```bash
    #!/bin/bash
    export DISPLAY=:0
    WID=$(xdotool search --onlyvisible --class chromium|head -1)
    xdotool windowactivate ${WID}
    sleep 10
    xdotool key Return
    xdotool mousemove 10000 10000
    ```

4. Make the script executable:

    ```bash
    chmod +x ~/scripts/login
    ```

5. Reboot the Raspberry Pi, the Infoscreen should be displayed on the screen connected to the Raspberry Pi and the login should be automatically filled and submitted.

6. If the login is not automatically filled and submitted, you can try to increase the sleep time in the script `~/scripts/login` to give Chromium more time to load the page.
