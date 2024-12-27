document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
    loader.addScript("plugins/UnicornAlert");
    loader.addScript("dialogs/PersonEditDialog");
    loader.addScript("dialogs/PersonCreateDialog");
    loader.addScript("dialogs/ConfirmDialog");
    loader.addScript("classes/Sidenavigation");
    
    loader.loadScripts()
        .then((message) => {
            postLoaded();
        })
        .catch((error) => console.error(error));
});

// Method is called after all scripts are loaded
function postLoaded()
{
    new Sidenavigation("Stammbuch");
    loadPersons();
}


// Open the dialog to create a new person
function createNewPerson()
{
    new PersonCreateDialog(() => {
        loadPersons();
    });
}

function loadPersons()
{
    fetch("./processing/actions/get_persons.php")
    .then(response => response.json())
    .then(data => {
        console.log(data);
        let persons = data.data;
        let list = document.getElementById("table_persons");
        list.innerHTML = "";
        persons.forEach(person => {
            let item = getPersonItem(person);
            list.appendChild(item);
        });
    })
}


function getPersonItem(person)
{
    let firstname = person.first_name ? person.first_name : "Unbekannt";
    let lastname = person.last_name ? person.last_name : "Unbekannt";
    let nutname = person.nut_name ? person.nut_name : "Unbekannt";

    let birthday = person.birth_day ? person.birth_day : "xx";
    if(birthday < 10) birthday = "0" + birthday;
    let birthmonth = person.birth_month ? person.birth_month : "xx";
    if(birthmonth < 10) birthmonth = "0" + birthmonth;
    let birthyear = person.birth_year ? person.birth_year : "xxxx";

    let birthdate = `${birthday}.${birthmonth}.${birthyear}`;
    if(birthdate === "xx.xx.xxxx") birthdate = "Unbekannt";

    let deathday = person.death_day ? person.death_day : "xx";
    if(deathday < 10) deathday = "0" + deathday;
    let deathmonth = person.death_month ? person.death_month : "xx";
    if(deathmonth < 10) deathmonth = "0" + deathmonth;
    let deathyear = person.death_year ? person.death_year : "xxxx";

    let deathdate = `${deathday}.${deathmonth}.${deathyear}`;
    if(deathdate === "xx.xx.xxxx") deathdate = "Unbekannt";


    let item = document.createElement("tr");
    item.innerHTML = `
        <td>${firstname}</td>
        <td>${lastname}</td>
        <td>${nutname}</td>
        <td>${birthdate}</td>
        <td>${deathdate}</td>
        <td>
            <button class="edit" onclick="editPerson(${person.id})">Bearbeiten</button>
            <button class="show" onclick="showPerson(${person.id})">Baum anzeigen</button>
            <button class="delete">Löschen</button>
        </td>`;

    let delete_buton = item.querySelector(".delete");
    delete_buton.addEventListener("click", () => {
        deletePerson(person);
    }); 

    return item;
}

function deletePerson(person)
{
    var id = person.id;
    var message = "Soll " + person.first_name + " " + person.last_name + " wirklich gelöscht werden?";

    new ConfirmDialog(message, (result) => {
        if(result)
        {
            fetch("./processing/actions/delete_person.php?id=" + id, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === "success")
                {
                    const unicornManager =  new UnicornAlertHandler();
                    unicornManager.createAlert(UnicornAlertTypes.WARNING, 'Person wurde gelöscht', 3000);
                    setTimeout(() => {
                        loadPersons();
                    }, 3000);
                }
                else
                {
                    const unicornManager =  new UnicornAlertHandler();
                    unicornManager.createAlert(UnicornAlertTypes.ERROR, data.message, 3000);
                }
            });
        }
    });
}

function editPerson(id)
{
    new PersonEditDialog(id, () => {
        loadPersons();
    });
}

function showPerson(id)
{
    window.location.href = "./person.html?id=" + id;
}