/*
 * UI code for status bar
 */

$(document).ready(function() {
   var percent = $('#statuspercent').html() * 1;
   //alert(percent);
    $('.statusbar').progressbar({
        value: percent
    });
});

