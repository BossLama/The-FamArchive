class ImageEditLoader
{
    image_id            = null;
    image_url           = null;
    removed_idents      = [];
    created_idents      = new Map();

    constructor()
    {
        let urlParams = new URLSearchParams(window.location.search);
        this.image_id = urlParams.get("id") ? urlParams.get("id") : null;

        if(this.image_id == null)
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Keine Bild-Id angegeben', 3000);
            setTimeout(() => {
                window.location.href = "./index.html";
            }, 3000);
            return;
        }

        this.fetchImage();

    }


    fetchImage()
    {
        fetch("./processing/actions/get_image.php?id=" + this.image_id)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if(data.status != "success") return;
            this.image_url = data.data.url;
            this.renderImage(data.data.url);
            this.renderInformation(data.data);
            this.enableDrawing();

            document.getElementById("box_persons").innerHTML = "";
            data.data.idents.forEach(element => {
                this.renderPerson(element, data.data.url);
            });
        })
    }


    renderInformation(data)
    {
        let day = data.day ? data.day : "xx";
        if(day < 10) day = "0" + day;
        let month = data.month ? data.month : "xx";
        if(month < 10) month = "0" + month;
        let year = data.year ? data.year : "xxxx";
        let date = day + "." + month + "." + year;

        let location = data.location ? data.location : null;
        let description = data.description ? data.description : null;

        let latitude = data.latitude ? data.latitude : null;
        let longitude = data.longitude ? data.longitude : null;

        document.getElementById("input_image_date").value = date;
        document.getElementById("input_image_location").value = location;
        document.getElementById("input_image_description").value = description;
        document.getElementById("input_image_latitude").value = latitude;
        document.getElementById("input_image_longitude").value = longitude;
    }

    renderImage(url, callback = (canvas, image) =>{})
    {
        let canvas = document.getElementById("canavas_image_view");
        let ctx = canvas.getContext("2d");
        let img = new Image();
        const devicePixelRatio = window.devicePixelRatio || 1;
        img.onload = function() {
            let width = canvas.clientWidth || 400;
            let aspect = img.width / img.height;
            let height = width / aspect;
        
            canvas.width = width * devicePixelRatio;
            canvas.height = height * devicePixelRatio;

            ctx.scale(devicePixelRatio, devicePixelRatio);
        
            canvas.style.width = width + "px";
            canvas.style.height = height + "px";
        
            ctx.drawImage(img, 0, 0, width, height);
            callback(canvas, img);
        }
        img.src = "./processing/storage/" + url;
    }

    renderPerson(ident, url)
    {
        let firstname = ident.person.first_name ? ident.person.first_name : "--";
        let lastname = ident.person.last_name ? ident.person.last_name : "--";
        let birthyear = ident.person.birth_year ? ident.person.birth_year : "--";

        let line = document.createElement("div");
        line.classList.add("line");
        line.classList.add("align");
        line.innerHTML =`<span class="bold">${firstname} ${lastname} *${birthyear}</span>`;

        let span = document.createElement("span");
        span.classList.add("buttons");
        let button_delete = document.createElement("button");
        button_delete.classList.add("delete");
        button_delete.innerHTML = "Löschen";
        button_delete.addEventListener("click", () => {
            new ConfirmDialog("Soll die Identifikation wirklich gelöscht werden?", (result) => {
                if(result)
                {
                    if(String(ident.id).includes("created"))
                    {   
                        this.created_idents.delete(ident.id);
                    }
                    else this.removed_idents.push(ident.id);
                    line.remove();
                }
            });
        });
        
        let button_show = document.createElement("button");
        button_show.innerHTML = "Zeigen";
        button_show.addEventListener("click", () => {
            this.renderIdent(ident.x, ident.y, ident.width, ident.height, url);
        });

        span.appendChild(button_delete);
        span.appendChild(button_show);
        line.appendChild(span);

        document.getElementById("box_persons").appendChild(line);
    }

    renderIdent(x, y, width, height, url)
    {
        this.renderImage(url, (canvas, image) => {
            let ctx = canvas.getContext("2d");
            let aspect = image.width / image.height;
            let canvas_width = canvas.clientWidth;
            let canvas_height = canvas_width / aspect;
            let scale = canvas_width / image.width;

            ctx.strokeStyle = "#FF0000";
            ctx.lineWidth = 2;
            ctx.strokeRect(x * scale, y * scale, width * scale, height * scale);
        });
    }

    enableDrawing() {
        const canvas = document.getElementById("canavas_image_view");
        const ctx = canvas.getContext("2d");

        let isDrawing = false;
        let startX = 0, startY = 0; // Startkoordinaten
        let currentX = 0, currentY = 0; // Aktuelle Mausposition

        // Event-Listener für Mausaktionen
        canvas.addEventListener("mousedown", (e) => {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            startX = e.clientX - rect.left;
            startY = e.clientY - rect.top;
        });

        canvas.addEventListener("mousemove", (e) => {
            if (!isDrawing) return;

            const rect = canvas.getBoundingClientRect();
            currentX = e.clientX - rect.left;
            currentY = e.clientY - rect.top;

            // Canvas neu zeichnen
            this.redrawImage(() => {
                ctx.strokeStyle = "rgba(255, 0, 0, 0.8)";
                ctx.lineWidth = 2;

                // Rechteck zeichnen
                ctx.strokeRect(startX, startY, currentX - startX, currentY - startY);
            });
        });

        canvas.addEventListener("mouseup", () => {
            if (!isDrawing) return;
            isDrawing = false;

            const rectWidth = currentX - startX;
            const rectHeight = currentY - startY;

            if (rectWidth > 0 && rectHeight > 0) {
                const { x, y, width, height } = this.convertToImageCoords(startX, startY, rectWidth, rectHeight);
                this.createIdent(x, y, width, height);
            }
        });
    }

    convertToImageCoords(x, y, width, height) {
        const canvas = document.getElementById("canavas_image_view");
        const scaleX = this.originalImageWidth / canvas.width;
        const scaleY = this.originalImageHeight / canvas.height;

        return {
            x: x * scaleX,
            y: y * scaleY,
            width: width * scaleX,
            height: height * scaleY
        };
    }

    redrawImage(callback) {
        const canvas = document.getElementById("canavas_image_view");
        const ctx = canvas.getContext("2d");
        const img = this.currentImage;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        if (callback) callback();
    }

    renderImage(url, callback = (canvas, image) => {}) {
        let canvas = document.getElementById("canavas_image_view");
        let ctx = canvas.getContext("2d");
        let img = new Image();
        this.currentImage = img; // Speichert das aktuelle Bild
        const devicePixelRatio = window.devicePixelRatio || 1;

        img.onload = () => {
            this.originalImageWidth = img.width;
            this.originalImageHeight = img.height;

            let width = canvas.clientWidth || 400;
            let aspect = img.width / img.height;
            let height = width / aspect;

            canvas.width = width * devicePixelRatio;
            canvas.height = height * devicePixelRatio;

            ctx.scale(devicePixelRatio, devicePixelRatio);
            canvas.style.width = width + "px";
            canvas.style.height = height + "px";

            ctx.drawImage(img, 0, 0, width, height);
            callback(canvas, img);
        };
        img.src = "./processing/storage/" + url;
    }

    createIdent(x, y, width, height) {
        x = Math.round(x);
        y = Math.round(y);
        width = Math.round(width);
        height = Math.round(height);

        new IdentCreateDialog((data) => {
            if(data == null) return;
            let ident_array = {
                person_id: data.id,
                x: x,
                y: y,
                width: width,
                height: height
            }
            this.created_idents.set("created_" + this.created_idents.length, ident_array);

            let ident_object = {
                id: "created_" + this.created_idents.length,
                person: {
                    first_name: data.first_name,
                    last_name: data.last_name,
                    birth_year: data.birth_year
                },
                x: x,
                y: y,
                width: width,
                height: height
            }
            this.renderPerson(ident_object, this.image_url);
        });
    }


    deleteImage()
    {
        fetch("./processing/actions/delete_image.php?id=" + this.image_id, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if(data.status != "success")
            {
                const unicornManager =  new UnicornAlertHandler();
                unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Fehler beim Löschen', 3000);
                return;
            }
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.SUCCESS, 'Bild gelöscht', 3000);

            setInterval(() => {
                window.location.href = "./index.html";
            }, 2000);
        });
    }


    saveEdits()
    {
        let date = document.getElementById("input_image_date").value.split(".");    
        if(date.length != 3)
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Ungültiges Datumformat', 3000);
            return
        }

        fetch("./processing/actions/update_image.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                image_id: this.image_id,
                day: date[0] == "xx" ? null : date[0],
                month: date[1] == "xx" ? null : date[1],
                year: date[2] == "xxxx" ? null : date[2],
                location: document.getElementById("input_image_location").value,
                description: document.getElementById("input_image_description").value,
                latitude: document.getElementById("input_image_latitude").value,
                longitude: document.getElementById("input_image_longitude").value,
                removed_idents: this.removed_idents,
                created_idents: Array.from(this.created_idents.values())
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if(data.status != "success")
            {
                const unicornManager =  new UnicornAlertHandler();
                unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Fehler beim Speichern', 3000);
                return;
            }
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.SUCCESS, 'Änderungen gespeichert', 3000);

            setInterval(() => {
                window.location.href = "./image.html?id=" + this.image_id;
            }, 2000);
        })
        
    }
}