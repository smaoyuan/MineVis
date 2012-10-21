/*
 * UI code for Visual Analytics page
 */

var project_id = -1;
// Unused
var documents = [];

/**
 * Just Initialize stuff on page loads
 */
$(document).ready(function() {
    //get project id
    project_id = $('aside:first').attr('id');

    // Add click events handling
    bindOptions();

    // Make sure we don't see the document list
    $('#documents').hide();

    // Set documetns as dialogs with manual opening
    $('.document').dialog({
        autoOpen: false
    });
});

/**
 * Binds the options to a click action that will trigger loading the documents
 */
function bindOptions() {
    $('option').click(function() {
        openDocument(this.value);
        return false;
    })
}

/**
 * This will open a document in a jquery UI dialog box.
 * If the document isn't available try to load it first.
 */
function openDocument(id) {
    //check if loaded
    if ($('#doc'+id).length <= 0) {
        getDocument(id);
    } else {
        $('#doc'+id).dialog("open");
    }
}

/**
 * Gets a document
 * This will load a document via ajax and add it to the list.
 */
function getDocument(id) {
    //alert("getting document " + id);
    var request = {
        'doc_id': id
    };
    $.getJSON('document.json', request, function(data) {
        $("#documents").append(
            '<div id="doc'+id+'" class="document" title="'+data['name']+'">' +
            data['text']+'</div>'
            );
        $('#doc'+id).dialog({ minHeight: 80 });
    });

}