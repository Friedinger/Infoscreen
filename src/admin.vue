<template>
    <h1>Infoscreen Admin</h1>
    <p><a href="../">Zurück zum Infoscreen</a></p>
    <h3>News hinzufügen</h3>
    <p>
        Es können nur Bilder hochgeladen werden mit einer Größe von maximal
        10MB.
    </p>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add" />
        <input type="file" name="file" />
        <input type="submit" name="submit" value="Hochladen" />
    </form>
    <br />
    <h3>News löschen</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="remove" />
        <select name="news">
            <option value="">-- Bitte wählen --</option>
        </select>
        <input type="submit" name="submit" value="Löschen" />
    </form>
    <br />
    <h3>Einstellungen</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="settings" />
        <label for="departureUrl">Abfahrten API URL </label>
        <input type="url" name="departureUrl" value="departureUrl" /><br />
        <label for="newsInterval">News Intervall (in Sekunden)</label>
        <input type="number" name="newsInterval" value="newsInterval" /><br />
        <label for="departureInterval">Abfahrten Intervall (in Sekunden)</label>
        <input
            type="number"
            name="departureInterval"
            value="departureInterval" /><br />
        <label for="reloadInterval">Neulade Intervall (in Sekunden)</label>
        <input
            type="number"
            name="reloadInterval"
            value="reloadInterval" /><br />
        <input type="submit" name="submit" value="Ändern" />
    </form>
    <br />
    <h3>Credits</h3>
    <p>
        Infoscreen &#169; Azubiwerk München<br />
        Version: {$ COMMIT_HASH $}<br />
        Entwickelt von
        <a href="https://friedinger.org/" target="_blank">Manuel Weis</a><br />
        Code:
        <a href="https://github.com/Friedinger/Infoscreen" target="_blank"
            >https://github.com/Friedinger/Infoscreen</a
        >
    </p>
</template>

<script setup lang="ts"></script>
