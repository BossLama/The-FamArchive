class GalleryLoader
{

    current         = null;
    last_loaded     = null;
    is_pending      = false;

    constructor()
    {
        this.current = 0;
        this.fetchNextImage();
        this.enableScrollListener();
    }

    renderItem(item)
    {
        let year = item.year ? item.year : "Unbekannt";
        let element = document.createElement("div");
        element.classList.add("item");
        element.innerHTML = `
                    <img src="./processing/storage/${item.url}" alt="Bild">
                    <div class="cover"></div>
                    <p class="year">${year}</p>
                    <p class="hover-info">Klicken für Details</p>
        `;
        element.addEventListener("click", () => {
            window.location.href = `./image.html?id=${item.id}`;
        });
        document.querySelector(".gallery").appendChild(element);
        return element;
    }

    // Fetch the next image
    fetchNextImage()
    {
        if(this.is_pending) return false;
        this.is_pending = true;
        fetch("./processing/actions/get_images.php?offset=" + this.current + "&limit=1")
        .then(response => response.json())
        .then(data => {
            if(data.status != "success") return;
            if(data.data.length == 0) return;
            let element = data.data[0];
            element = this.renderItem(element);
            this.is_pending = false;
            this.last_loaded = element;
            this.current++;

            if(this.isElementVisible(element))
            {
                this.last_loaded = null;
                this.fetchNextImage();
            }
        })
        .catch(error => {
            console.error(error);
            this.is_pending = false;
        });
    }

    // Check if parts of the element are visible
    isElementVisible(element)
    {
        var rect = element.getBoundingClientRect();
        var vWidth = window.innerWidth || document.documentElement.clientWidth;
        var vHeight = window.innerHeight || document.documentElement.clientHeight;

        if (rect.right < 0 || rect.bottom < 0
            || rect.left > vWidth || rect.top > vHeight)
            return false;

        return true;
    }

    // Enable the scroll listener
    enableScrollListener()
    {
        document.querySelector("main").addEventListener("scroll", () => {
            if(this.is_pending) return;
            if(this.last_loaded == null) return;
            if(this.isElementVisible(this.last_loaded))
            {
                this.last_loaded = null;
                this.fetchNextImage();
            }
        });
    }
}