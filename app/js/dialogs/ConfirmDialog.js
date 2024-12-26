class ConfirmDialog
{
    constructor(onSave = (result) => {})
    {
        this.dialog = null;
        this.onSave = onSave;
        this.renderDialog((dialog) => {
            dialog.querySelector(".cancel").addEventListener("click", () => this.onCancel());
            dialog.querySelector(".submit").addEventListener("click", () => {
                this.hideDialog();
                this.onSave(true);
            });
            this.initInputListeners();
        });
    }   

    // Renders the dialog
    renderDialog(callback = (dialog) => {})
    {
        fetch("./app/dialogs/confirm_dialog.html")
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

    // Event when button "Abbrechen" is clicked
    onCancel()
    {
        this.hideDialog();
        this.onSave(false);
    }

}