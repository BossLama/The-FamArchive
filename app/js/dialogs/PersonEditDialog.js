class PersonEditDialog
{

    loaded_user = null;

    constructor(user_id, onSave = (result) => {})
    {
        if(user_id == null)
        {
            console.error("No user id provided");
            return;
        }

        this.loaded_user = user_id;
        this.dialog = null;
        this.onSave = onSave;
        this.renderDialog((dialog) => {
            dialog.querySelector(".cancel").addEventListener("click", () => this.onCancel());
            dialog.querySelector(".submit").addEventListener("click", () => {
                this.onPersonUpdate();
            });
            this.initInputListeners();
            this.initValues(user_id);
        });
    }   

    // Renders the dialog
    renderDialog(callback = (dialog) => {})
    {
        fetch("./app/dialogs/person_edit_dialog.html")
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

    initValues(person_id)
    {
        this.fetchPersonDataById(person_id, (data) => {
            document.getElementById("input_person_edit_firstname").value = data.first_name;
            document.getElementById("input_person_edit_lastname").value = data.last_name;
            document.getElementById("input_person_edit_nutname").value = data.nut_name;
            document.getElementById("input_person_edit_birthday").value = data.birth_day;
            document.getElementById("input_person_edit_birthmonth").value = data.birth_month;
            document.getElementById("input_person_edit_birthyear").value = data.birth_year;
            document.getElementById("input_person_edit_deathday").value = data.death_day;
            document.getElementById("input_person_edit_deathmonth").value = data.death_month;
            document.getElementById("input_person_edit_deathyear").value = data.death_year;
            document.getElementById("input_person_edit_mother").value = `${data.mother.first_name} ${data.mother.last_name}`;
            document.getElementById("input_person_edit_mother_id").value = data.mother.id;
            document.getElementById("input_person_edit_father").value = `${data.father.first_name} ${data.father.last_name}`;
            document.getElementById("input_person_edit_father_id").value = data.father.id;
            
        });
    }

    // Initializes input listeners
    initInputListeners()
    {
        let input_mother_name     = document.getElementById("input_person_edit_mother");
        let input_mother_id       = document.getElementById("input_person_edit_mother_id");
        let list_mothers          = document.getElementById("input_person_edit_mother_list");

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

        let input_father_name     = document.getElementById("input_person_edit_father");
        let input_father_id       = document.getElementById("input_person_edit_father_id");
        let list_fathers          = document.getElementById("input_person_edit_father_list");

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

    // Fetch person data based on an id
    fetchPersonDataById(id = 0, callback = (data) => {})
    {
        fetch("./processing/actions/get_person.php?id=" + id)
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
    onPersonUpdate()
    {

        let data = {
            "firstname" : document.getElementById("input_person_edit_firstname").value,
            "lastname" : document.getElementById("input_person_edit_lastname").value,
            "nutname" : document.getElementById("input_person_edit_nutname").value,
            "birthday": document.getElementById("input_person_edit_birthday").value,
            "birthmonth": document.getElementById("input_person_edit_birthmonth").value,
            "birthyear": document.getElementById("input_person_edit_birthyear").value,
            "deathday": document.getElementById("input_person_edit_deathday").value,
            "deathmonth": document.getElementById("input_person_edit_deathmonth").value,
            "deathyear": document.getElementById("input_person_edit_deathyear").value,
            "mother_id": document.getElementById("input_person_edit_mother_id").value,
            "father_id": document.getElementById("input_person_edit_father_id").value
        };


        fetch("./processing/actions/update_person.php", {
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