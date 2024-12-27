document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
    loader.addScript("plugins/UnicornAlert");
    loader.addScript("classes/Sidenavigation");
    loader.addScript("classes/FamilyTreeRenderer");
    
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

    // Get id as parameter
    let url = new URL(window.location.href);
    let user_id = url.searchParams.get("id");

    if(user_id == null)
    {
        const unicornManager =  new UnicornAlertHandler();
        unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Keine Person-ID übergeben', 3000);
        setInterval(() => {
            window.location.href = "./people.html";
        }, 3000);
        return;
    }

    // Create Graph Handler
    fetch("./processing/actions/get_full_persontree.php?id=" + user_id)
    .then(response => response.json())
    .then(data => {
        if(data.status != "success")
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Person konnte nicht geladen werden', 3000);
            return;
        }
        new FamilyTreeRenderer(data.data, document.querySelector("main"), (id) => {
            window.location.href = "./person.html?id=" + id;
        });
    });

}