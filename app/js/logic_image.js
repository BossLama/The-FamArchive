document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
    loader.addScript("plugins/UnicornAlert");
    loader.addScript("classes/Sidenavigation");
    loader.addScript("classes/ImageLoader");
    
    loader.loadScripts()
        .then((message) => {
            postLoaded();
        })
        .catch((error) => console.error(error));
});

let image_loader = null;

// Method is called after all scripts are loaded
function postLoaded()
{
    new Sidenavigation("Bildarchiv");
    image_loader =  new ImageLoader();

    document.getElementById("button_edit").addEventListener("click", () => {
        image_loader.editImage();
    });
}