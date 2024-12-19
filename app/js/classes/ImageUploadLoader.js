class ImageUploadLoader
{
    image_loaded        = false;
    current_image       = null;
    created_idents      = [];

    originalImageHeight = 0;
    originalImageWidth  = 0;

    constructor()
    {
        document.getElementById("canavas_image_view").addEventListener("click", (e) => {
            if(!this.image_loaded)
            {
                document.getElementById("input_image_image").click();
            }
        });

        document.getElementById("input_image_image").addEventListener("change", (e) => {
            let file = e.target.files[0];
            if(file == null) return;
            
            let reader = new FileReader();
            reader.onload = (e) => {
                this.image_loaded = true;
                this.currentImage = e.target.result;
                this.image_url = file.name;
                this.renderUploadedImage(e.target.result, (canvas, image) => {
                    this.enableDrawing();
                });
            }
            reader.readAsDataURL(file);
        });
    }

    renderUploadedImage(image, callback = (canvas, image) => {}) {
        let canvas = document.getElementById("canavas_image_view");
        let ctx = canvas.getContext("2d");
    
        let img = new Image();
        const devicePixelRatio = window.devicePixelRatio || 1;
        img.onload = () => { // Arrow function preserves 'this' context
            let width = canvas.clientWidth || 400;
            let aspect = img.width / img.height;
            let height = width / aspect;
    
            this.originalImageHeight = img.height;
            this.originalImageWidth = img.width;
    
            canvas.width = width * devicePixelRatio;
            canvas.height = height * devicePixelRatio;
    
            ctx.scale(devicePixelRatio, devicePixelRatio);
    
            canvas.style.width = width + "px";
            canvas.style.height = height + "px";
    
            ctx.drawImage(img, 0, 0, width, height);
            callback(canvas, img);
        };
        img.src = image;
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
            if(String(ident.id).includes("created"))
            {   
                this.created_idents.delete(ident.id);
            }
            else this.removed_idents.push(ident.id);
            line.remove();
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
        this.renderUploadedImage(this.currentImage, (canvas, image) => {
            callback();
        });
    }

    createIdent(x, y, width, height) {
        x = Math.round(x);
        y = Math.round(y);
        width = Math.round(width);
        height = Math.round(height);

        console.log(x, y, width, height);

        new IdentCreateDialog((data) => {
            if(data == null) return;
            let ident_array = {
                person_id: data.id,
                x: x,
                y: y,
                width: width,
                height: height
            }
            this.created_idents.push(ident_array);

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


    saveEdits()
    {
        let date = document.getElementById("input_image_date").value.split(".");    
        if(date.length != 3)
        {
            const unicornManager =  new UnicornAlertHandler();
            unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Ungültiges Datumformat', 3000);
            return
        }

        var formData = new FormData();
        formData.append("image", document.getElementById("input_image_image").files[0]);
        formData.append("day", date[0] == "xx" ? null : date[0]);
        formData.append("month", date[1] == "xx" ? null : date[1]);
        formData.append("year", date[2] == "xxxx" ? null : date[2]);
        formData.append("location", document.getElementById("input_image_location").value);
        formData.append("description", document.getElementById("input_image_description").value);
        formData.append("latitude", document.getElementById("input_image_latitude").value);
        formData.append("longitude", document.getElementById("input_image_longitude").value);
        formData.append("idenified_persons", JSON.stringify(this.created_idents));

        fetch("./processing/actions/create_image.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status == "success")
            {
                window.location.href = `./image.html?id=${data.data.id}`;
            }
            else
            {
                const unicornManager =  new UnicornAlertHandler();
                unicornManager.createAlert(UnicornAlertTypes.ERROR, data.message, 3000);
            }
        })
        
    }
}