document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
    loader.addScript("plugins/UnicornAlert");
    loader.addScript("classes/Sidenavigation");
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
}


function saveEdits()
{
    image_upload_loader.saveEdits();
}