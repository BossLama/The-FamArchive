class PersonCreateDialog
{

    constructor(onSave = (result) => {})
    {
        this.dialog = null;
        this.onSave = onSave;
        this.renderDialog((dialog) => {
            dialog.querySelector(".cancel").addEventListener("click", () => this.onCancel());
            dialog.querySelector(".submit").addEventListener("click", () => {
                this.onPersonCreate();
            });
            this.initInputListeners();
        });
    }   

    // Renders the dialog
    renderDialog(callback = (dialog) => {})
    {
        fetch("./app/dialogs/person_create_dialog.html")
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
        let input_mother_name     = document.getElementById("input_person_create_mother");
        let input_mother_id       = document.getElementById("input_person_create_mother_id");
        let list_mothers          = document.getElementById("input_person_create_mother_list");

        input_mother_name.addEventListener("input", () => {
            if(input_mother_name.value.length < 3){ list_mothers.innerHTML = ""; return;}
            this.fetchPersonData(input_mother_name.value, (data) => {
                list_mothers.innerHTML = "";
                data.forEach(person => {
                    list_mothers.appendChild(this.getPersonListItem(person, (person) => {
                        input_mother_name.value = `${person.first_name} ${person.last_name}`;
                        input_mother_id.value = person.id;
                        list_mothers.innerHTML = "";
                    }));
                });
            });
        });

        let input_father_name     = document.getElementById("input_person_create_father");
        let input_father_id       = document.getElementById("input_person_create_father_id");
        let list_fathers          = document.getElementById("input_person_create_father_list");

        input_father_name.addEventListener("input", () => {
            if(input_father_name.value.length < 3){ list_fathers.innerHTML = ""; return;}
            this.fetchPersonData(input_father_name.value, (data) => {
                list_fathers.innerHTML = "";
                data.forEach(person => {
                    list_fathers.appendChild(this.getPersonListItem(person, (person) => {
                        input_father_name.value = `${person.first_name} ${person.last_name}`;
                        input_father_id.value = person.id;
                        list_fathers.innerHTML = "";
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
            onClick(person);
        });
        return element;
    }

    // Event when button "Person erstellen" is clicked
    onPersonCreate()
    {

        let data = {
            "firstname" : document.getElementById("input_person_create_firstname").value,
            "lastname" : document.getElementById("input_person_create_lastname").value,
            "nutname" : document.getElementById("input_person_create_nutname").value,
            "birthday": document.getElementById("input_person_create_birthday").value,
            "birthmonth": document.getElementById("input_person_create_birthmonth").value,
            "birthyear": document.getElementById("input_person_create_birthyear").value,
            "deathday": document.getElementById("input_person_create_deathday").value,
            "deathmonth": document.getElementById("input_person_create_deathmonth").value,
            "deathyear": document.getElementById("input_person_create_deathyear").value,
            "mother_id": document.getElementById("input_person_create_mother_id").value,
            "father_id": document.getElementById("input_person_create_father_id").value
        };


        fetch("./processing/actions/create_person.php", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if(data.status == "success")
            {
                if(data.status == "success")
                {
                    this.hideDialog();
                    this.onSave(data.data);
                }
                else
                {
                    const unicornManager =  new UnicornAlertHandler();
                    unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Person konnte nicht erstellt werden', 3000);
                }
            }
            else
            {
                const unicornManager =  new UnicornAlertHandler();
                unicornManager.createAlert(UnicornAlertTypes.ERROR, 'Unbekannter Fehler beim Erstellen einer Person', 3000);
            }
        });
        return;
    }

    // Event when button "Abbrechen" is clicked
    onCancel()
    {
        this.hideDialog();
    }

}