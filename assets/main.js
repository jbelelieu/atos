
/**
 * 
 */
function bindUnload() {
    window.onbeforeunload = function () {
        return 'You have unsaved changes.'
    }

    var forms = document.getElementsByTagName('form');
    for (var i = 0; i < forms.length; i++) {
        forms.item(i).addEventListener('submit', removeUnloadForForm)
    }
}

/**
 * 
 */
function redirectBasedOnFormValue(formValue) {
    window.location = formValue.value;
}

/**
 * 
 */
function removeUnloadForForm() {
    window.onbeforeunload = null;
}

/**
 * Warns the user if they try to leave a page that has unsaved form changes.
 */
function preventUnloadBasedOnFormChanges(formId) {
    document.getElementById(formId).addEventListener("input", function () {
        bindUnload();
    });
}
