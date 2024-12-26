document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
    loader.addScript("plugins/UnicornAlert");
    loader.addScript("classes/Sidenavigation");
    loader.addScript("dialogs/PersonCreateDialog");
    loader.addScript("dialogs/IdentCreateDialog");
    loader.addScript("classes/ImageUploadLoader");
    
    loader.loadScripts()
        .then((message) => {
            postLoaded();
        })
        .catch((error) => console.error(error));
});

let image_upload_loader = null;

// Method is called after all scripts are loaded
function postLoaded()
{
    new Sidenavigation("Bildarchiv");
    image_upload_loader =  new ImageUploadLoader();
    preRenderText();
}


function saveEdits()
{
    image_upload_loader.saveEdits();
}

function preRenderText(text = "Klicke um ein Bild hochzuladen") {
    const canvas = document.getElementById('canavas_image_view');
    const context = canvas.getContext('2d');

    // Anpassung an Gerätepixelverhältnis
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;

    context.scale(dpr, dpr);

    // Hintergrund zeichnen
    context.fillStyle = 'gray';
    context.fillRect(0, 0, canvas.width / dpr, canvas.height / dpr);

    // Text zeichnen
    context.fillStyle = '#333';
    context.font = '16px Arial'; // Größere Schriftgröße für Klarheit
    context.textAlign = 'center';
    context.textBaseline = 'middle';

    const x = canvas.width / (2 * dpr);
    const y = canvas.height / (2 * dpr);
    context.fillText(text, x, y);
}
