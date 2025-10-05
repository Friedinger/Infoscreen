<template>
    <div>
        <img
            src="https://cdn.prod.website-files.com/64d11728a06601a00aeea217/64d1204dc148da73086e0bdd_Haus%20lila.svg"
            alt="Logo" />
        <h1>
            Azubiwerk München<br />
            Infoscreen
        </h1>
        <p>Version: {$COMMIT_HASH$}</p>
        <p>github.com/<wbr />Friedinger/<wbr />Infoscreen</p>
    </div>
</template>

<style scoped>
div {
    display: flex;
    position: relative;
    flex: 1;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem;
    overflow: hidden;
}
img {
    width: 100%;
    margin: 0;
    margin-bottom: auto;
    filter: invert(56%) sepia(95%) saturate(1814%) hue-rotate(2deg)
        brightness(92%) contrast(105%);
}
h1 {
    display: inline-block;
    position: absolute;
    top: 50%;
    transform: translateY(-50%) rotate(-90deg);
    transform-origin: center;
    font-size: 4rem;
    white-space: nowrap;
}
p {
    margin: 0;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    text-align: center;
}
</style>
