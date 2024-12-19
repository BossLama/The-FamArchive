class IdentCreateDialog
{

    selectedPerson = null;

    constructor(onSave = (result) => {})
    {
        this.dialog = null;
        this.onSave = onSave;
        this.renderDialog((dialog) => {
            dialog.querySelector(".create-person").addEventListener("click", () => this.onPersonCreate());
            dialog.querySelector(".cancel").addEventListener("click", () => this.onCancel());
            dialog.querySelector(".submit").addEventListener("click", () => {
                this.hideDialog();
                this.onSave(this.selectedPerson);
            });
            this.initInputListeners();
        });
    }   

    // Renders the dialog
    renderDialog(callback = (dialog) => {})
    {
        fetch("./app/dialogs/ident_create_dialog.html")
        .then(response => response.text())
        .then(data => {
            let dialog = document.createElement("div");
            dialog.innerHTML = data;
            document.body.appendChild(dialog);
            this.dialog = dialog;
            callback(dialog);
        });
    }

    // Removes the dialog
    hideDialog()
    {
        this.dialog.remove();
    }

    // Initializes input listeners
    initInputListeners()
    {
        let input_name      = document.getElementById("input_ident_create_name");
        let input_id        = document.getElementById("input_ident_create_id");
        let list_persons    = document.getElementById("input_ident_create_list");

        input_name.addEventListener("input", () => {
            if(input_name.value.length < 3){ list_persons.innerHTML = ""; return;}
            this.fetchPersonData(input_name.value, (data) => {
                list_persons.innerHTML = "";
                data.forEach(person => {
                    list_persons.appendChild(this.getPersonListItem(person, (person) => {
                        input_name.value = `${person.first_name} ${person.last_name}`;
                        input_id.value = person.id;
                        list_persons.innerHTML = "";
                    }));
                });
            });
        });
    }

    // Fetches person data based on a query
    fetchPersonData(query = "", callback = (data) => {})
    {
        fetch("./processing/actions/get_persons.php?query=" + query)
        .then(response => response.json())
        .then(data => {
            if(data.status != "success") callback([]);
            callback(data.data);
        });
    }

    // Renders person list item
    getPersonListItem(person, onClick = (person) => {})
    {
        let firstname = person.first_name ? person.first_name : "";
        let lastname = person.last_name ? person.last_name : "";
        let birthyear = person.birth_year ? person.birth_year : "--";
        let label = `${firstname} ${lastname} *${birthyear}`;

        let element = document.createElement("li");
        element.classList.add("person-list-item");
        element.innerHTML = `
            <p>${label}</p>
        `;

        element.addEventListener("click", () => {
            this.selectedPerson = person;
            onClick(person);
        });
        return element;
    }

    // Event when button "Person erstellen" is clicked
    onPersonCreate()
    {
        return;
    }

    // Event when button "Abbrechen" is clicked
    onCancel()
    {
        this.hideDialog();
    }

}