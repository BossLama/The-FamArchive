document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
    loader.addScript("dialogs/PersonCreateDialog");
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
}


// Open the dialog to create a new person
function createNewPerson()
{
    new PersonCreateDialog();
}