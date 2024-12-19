document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
    loader.addScript("plugins/UnicornAlert");
    loader.addScript("classes/Sidenavigation");
    loader.addScript("dialogs/IdentCreateDialog");
    loader.addScript("classes/ImageEditLoader");
    
    loader.loadScripts()
        .then((message) => {
            postLoaded();
        })
        .catch((error) => console.error(error));
});

let image_edit_loader = null;

// Method is called after all scripts are loaded
function postLoaded()
{
    new Sidenavigation("Bildarchiv");
    image_edit_loader =  new ImageEditLoader();
}


function saveEdits()
{
    image_edit_loader.saveEdits();
}

function deleteImage()
{
    image_edit_loader.deleteImage();
}