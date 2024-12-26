document.addEventListener("DOMContentLoaded", () => {
    const loader = new ScriptLoader();
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
    new PersonCreateDialog();
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
    if(birthday.length < 2) birthday = "0" + birthday;
    let birthmonth = person.birth_month ? person.birth_month : "xx";
    if(birthmonth.length < 2) birthmonth = "0" + birthmonth;
    let birthyear = person.birth_year ? person.birth_year : "xxxx";
    if(birthyear.length < 4) birthyear = "xxxx";

    let birthdate = `${birthday}.${birthmonth}.${birthyear}`;
    if(birthdate === "xx.xx.xxxx") birthdate = "Unbekannt";

    let deathday = person.death_day ? person.death_day : "xx";
    if(deathday.length < 2) deathday = "0" + deathday;
    let deathmonth = person.death_month ? person.death_month : "xx";
    if(deathmonth.length < 2) deathmonth = "0" + deathmonth;
    let deathyear = person.death_year ? person.death_year : "xxxx";
    if(deathyear.length < 4) deathyear = "xxxx";

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
            <button class="edit" onclick="editPerson()">Bearbeiten</button>
            <button class="show" onclick="showPerson()">Anzeigen</button>
            <button class="delete" onclick="deletePerson(${person.id})">Löschen</button>
        </td>`;

    return item;
}

function deletePerson(id)
{
    new ConfirmDialog((result) => {
        if(result)
        {
           alert("Person wurde gelöscht");
        }
    });
}