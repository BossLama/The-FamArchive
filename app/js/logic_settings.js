document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
    loader.addScript("classes/Sidenavigation");
    loader.addScript("plugins/UnicornAlert");
    
    loader.loadScripts()
        .then((message) => {
            postLoaded();
        })
        .catch((error) => console.error(error));
});

// Method is called after all scripts are loaded
function postLoaded()
{
    new Sidenavigation("Einstellungen");

    // Add event listener to the import file input

}

// Method is called when the user wants to export the archive
function createExport()
{
    const unicornManager =  new UnicornAlertHandler();
    unicornManager.createAlert(UnicornAlertTypes.INFO, 'Export des Archivs wird erstellt...', 3000);
    fetch("./processing/actions/create_export_v2.php")
    .then(response => response.json())
    .then(data => {
        if(data.status == "success")
        {
            unicornManager.createAlert(UnicornAlertTypes.SUCCESS, 'Export wurde erstellt', 3000);
            let file_path = "./processing/exports/" + data.file;
            let a = document.createElement("a");
            a.href = file_path;
            a.download = data.file;
            a.click();
        }
        else
        {
            unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Fehler beim Erstellen des Exports', 3000);
        }
    });
}


// Method is called when the user wants to import a file
function createImport()
{
    document.getElementById("input_import_file").click();
}

function handleImport()
{
    if(document.getElementById("input_import_file").files.length == 0)
    {
        return;
    }
    let file = document.getElementById("input_import_file").files[0];
    let form_data = new FormData();
    form_data.append("import_zip", file);

    const unicornManager =  new UnicornAlertHandler();
    unicornManager.createAlert(UnicornAlertTypes.INFO, 'Import des Archivs wird durchgeführt...', 3000);
    fetch("./processing/actions/create_import_v2.php", {
        method: "POST",
        body: form_data
    })
    .then(response => response.json())
    .then(data => {
        if(data.status == "success")
        {
            unicornManager.createAlert(UnicornAlertTypes.SUCCESS, 'Import wurde durchgeführt', 3000);
        }
        else
        {
            unicornManager.createAlert(UnicornAlertTypes.ERROR, data.message, 3000);
        }
    });
}