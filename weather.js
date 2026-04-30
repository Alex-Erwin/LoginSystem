// Weather widget for Barre, VT using Open-Meteo
// Barre, VT coordinates: lat 44.2, lon -72.5

async function loadWeather() {
    const widget = document.getElementById('weather-widget');
    if (!widget) return;

    try {
        const url = 'https://api.open-meteo.com/v1/forecast?latitude=44.2&longitude=-72.5&current_weather=true&temperature_unit=fahrenheit';
        const res = await fetch(url);
        const data = await res.json();
        const w = data.current_weather;

        const descriptions = {
            0: 'Clear sky', 1: 'Mainly clear', 2: 'Partly cloudy', 3: 'Overcast',
            45: 'Foggy', 48: 'Foggy', 51: 'Light drizzle', 53: 'Drizzle',
            61: 'Light rain', 63: 'Rain', 71: 'Light snow', 73: 'Snow',
            80: 'Showers', 95: 'Thunderstorm'
        };
        const desc = descriptions[w.weathercode] || 'See forecast';

        widget.innerHTML = `
            <strong>Current Weather in Barre, VT:</strong>
            &nbsp; ${w.temperature}&deg;F &nbsp;|&nbsp; ${desc}
            &nbsp;|&nbsp; Wind: ${w.windspeed} mph
        `;
    } catch (e) {
        widget.innerHTML = '<strong>Weather:</strong> Unable to load at this time.';
    }
}

loadWeather();
