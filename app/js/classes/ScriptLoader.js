class ScriptLoader {
    constructor() {
        this.scripts = [];
    }

    // Add a script to the scripts array
    addScript(path) {
        if (typeof path === "string" && path.trim() !== "") {
            this.scripts.push("./app/js/" + path + ".js");
        } else {
            console.error("Ungültiger Pfad:", path);
        }
    }

    // Load all scripts in the scripts array
    loadScripts() {
        return new Promise((resolve, reject) => {
            const loadScript = (index) => {
                if (index >= this.scripts.length) {
                    resolve("Alle Skripte wurden erfolgreich geladen!");
                    return;
                }

                const script = document.createElement("script");
                script.src = this.scripts[index];
                script.async = false;
                script.onload = () => {
                    console.log(`Skript geladen: ${this.scripts[index]}`);
                    loadScript(index + 1);
                };
                script.onerror = () => {
                    reject(`Fehler beim Laden des Skripts: ${this.scripts[index]}`);
                };

                document.head.appendChild(script);
            };

            loadScript(0);
        });
    }
}