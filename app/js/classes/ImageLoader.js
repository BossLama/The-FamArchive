class ImageLoader
{
    image_id = null;

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
            this.renderImage(data.data.url);
            this.renderInformation(data.data);
            this.renderMapView(data.data.latitude, data.data.longitude);

            document.getElementById("box_persons").innerHTML = "";
            data.data.idents.forEach(element => {
                this.renderPerson(element, data.data.url);
            });

            document.getElementById("canavas_image_view").addEventListener("click", () => {
                // Open image in new tab
                window.open("./processing/storage/" + data.data.url);
                
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
        if(date.startsWith("xx.xx.xxxx")) date = "Unbekannt";
        if(date.startsWith("xx.xx.")) date = year;
        if(date.startsWith("xx.")) date = month + "/" + year;

        let location = data.location ? data.location : "Unbekannt";
        let description = data.description ? data.description : "Keine Beschreibung vorhanden";


        document.getElementById("box_metadata").innerHTML = 
        `
                <div class="line"><span class="bold">Aufnahmedatum</span><span>${date}</span></div>
                <div class="line"><span class="bold">Aufnahmeort</span><span>${location}</span></div>
                <div class="line"><span class="bold">Beschreibung</span><span>${description}</span></div>
        `;
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
        let button_open = document.createElement("button");
        button_open.innerHTML = "Details";
        
        let button_show = document.createElement("button");
        button_show.innerHTML = "Zeigen";
        button_show.addEventListener("click", () => {
            this.renderIdent(ident.x, ident.y, ident.width, ident.height, url);
        });

        span.appendChild(button_open);
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

    renderMapView(latitude, longitude)
    {
        if(latitude == null || longitude == null)
        {
            document.querySelector(".map-view").style.display = "none";
            return;
        }
        document.querySelector(".map-view").style.display = "block";
        let map = L.map('map').setView([latitude, longitude], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);
        L.marker([latitude, longitude]).addTo(map);
    }

    editImage()
    {
        window.location.href = `./image_edit.html?id=${this.image_id}`;
    }
}