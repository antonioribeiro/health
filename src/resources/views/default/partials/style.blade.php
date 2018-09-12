<style>
    body {
        padding-top: {{ 30 * config('health.style.multiplier', 1) }}px;
        padding-bottom: {{ 20 * config('health.style.multiplier', 1) }}px;
        font-family: 'Ubuntu', sans-serif;
    }

    h1, h3 {
        font-size: {{ 3.5 * config('health.style.multiplier', 1) }}em;
    }

    div.btn.single h3 {
        margin: 0;
    }

    h2 {
        font-size: {{ 2.5 * config('health.style.multiplier', 1) }}em;
    }

    .btn {
        margin-top: {{ 25 * config('health.style.multiplier', 1) }}px;
        min-height: {{ 5 * config('health.style.multiplier', 1) }}px;
    }

    .btn.single {
        text-align: left;
    }
</style>
