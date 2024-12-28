class Sidenavigation
{

    elements = null;

    constructor(active = "Bildarchiv")
    {
        this.elements = new Map();
        let side_navigation_context = this.createSideNavigation();
        this.side_navigation = side_navigation_context[0];
        this.list_items = side_navigation_context[1];

        this.list_items.appendChild(this.createItem("icon_light_gallery", "Bildarchiv", "./index.html"));
        this.list_items.appendChild(this.createItem("icon_light_document", "Dokumentenarchiv", "./documents.html"));
        this.list_items.appendChild(this.createItem("icon_light_book", "Stammbuch", "./people.html"));
        this.list_items.appendChild(this.createItem("icon_light_map", "Karte", "./map.html"));
        this.list_items.appendChild(this.createItem("icon_light_setting", "Einstellungen", "./settings.html"));

        this.elements.get(active).classList.add("active");

        document.body.prepend(this.side_navigation);

        // Add the toggle button for mobile
        this.createToggleButton();
    }

    // Method returns the side navigation and the ul element as array
    createSideNavigation()
    {
        let sideNav     = document.createElement("nav");
        let title       = document.createElement("h1");
        let ul          = document.createElement("ul");
        let version     = document.createElement("p");

        title.innerHTML = "FamArchive";
        version.innerHTML = "v 2.1.0";
        version.classList.add("version");

        sideNav.appendChild(title);
        sideNav.appendChild(ul);
        sideNav.appendChild(version);

        sideNav.id = "side_navigation";
        sideNav.classList.add("side-navigation");

        return [sideNav, ul];
    }

    // Method returns a item for the side navigation
    createItem(icon, text, link, onclick = null)
    {
        if(onclick == null && link == null)
        {
            console.error("Please provide a link or an onclick function");
            return;
        }

        if(onclick == null)
        {
            let item    = document.createElement("a");
            let li      = document.createElement("li");
            let img     = document.createElement("img");
            let span    = document.createElement("span");

            img.src = "./app/resources/icons/" + icon + ".svg";
            span.innerHTML = text;
            item.href = link;

            li.appendChild(img);
            li.appendChild(span);
            item.appendChild(li);

            this.elements.set(text, li);
            return item;
        }
        else
        {
            let item    = document.createElement("li");
            let img     = document.createElement("img");
            let span    = document.createElement("span");

            img.src = "./app/resources/icons/" + icon + ".svg";
            span.innerHTML = text;
            item.onclick = onclick;

            item.appendChild(img);
            item.appendChild(span);

            this.elements.set(text, item);
            return item;
        }
    }

    // Create a toggle button for mobile devices
    createToggleButton()
    {
        let toggleButton = document.createElement("button");
        toggleButton.id = "toggle_navigation";
        toggleButton.innerHTML = "☰"; // Hamburger menu icon
        toggleButton.addEventListener("click", () => {
            this.side_navigation.classList.toggle("visible");
        });

        document.body.prepend(toggleButton);
    }
}
