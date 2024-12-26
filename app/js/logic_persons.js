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
    new Sidenavigation("Stammbuch");
    new GalleryLoader();
}