document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
    loader.addScript("classes/Sidenavigation");
    
    loader.loadScripts()
        .then((message) => {
            postLoaded();
        })
        .catch((error) => console.error(error));
});

// Method is called after all scripts are loaded
function postLoaded()
{
    new Sidenavigation("Karte");

    // Load the map
    getUserLocation((position) => {
        loadMap(position);
    });
}

// Loads the map
function loadMap(position)
{
    var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    // Add a red marker to the map
    var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
    marker.bindPopup("Du bist hier");

    // Fetch image locations
    fetchImageLocations(map);
}

let loaded_locations = [];

// Fetch image locations
function fetchImageLocations(map)
{
    fetch("./processing/actions/get_image_locations.php")
    .then(response => response.json())
    .then(data => {
        if(data.status == "success")
        {
            let locations = data.data;
            locations.forEach(location => {
                var hash = location.latitude + "x" + location.longitude;
                if(loaded_locations.includes(hash)) return;
                loaded_locations.push(hash);
                renderCircle(location.latitude, location.longitude, map);
            });
        }
        else
        {
            console.error("Failed to fetch image locations");
        }
    })
    .catch(error => {
        console.error(error);
    });
}


// Request permission to use the user's location
function requestLocationPermission()
{
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
        });
    } else {
        console.error("Geolocation is not supported by this browser.");
    }
}

// Returns laitude, longitude and accuracy of the user's location
function getUserLocation(callback = (position) => {})
{
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            callback(position);
        });
    } else {
        console.error("Geolocation is not supported by this browser.");
    }
}

function renderCircle(lat, lon, map)
{
    console.log(lat, lon);
    L.circle([lat, lon], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.2,
        radius: 50
    }).addTo(map);
}