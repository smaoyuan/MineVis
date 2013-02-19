/**
 * Notes
 *
 * The folowing variables should be decaled within the page to make this work:
 * @param vis_id int id of the current vis on this page
 *
 * This avoids having to query the vis and then get the other ids. Trying to optimize...
 */

var insert_location_x = 100;
var insert_location_y = 100;
var insert_offset_x = 40;
var insert_offset_y = 40;
var floating_preview_top = 860; /* 260px (menus/spacing) + 600px (graph) */
var autosave_interval;
var autosave_interval_time = 30000;
var autoload_timeout;
var lhrd_mode = false;

/** 
 * Ready up the page
 * list things here
 */
$(document).ready(function() {
    /*
     * Initialize UI
     */
    init_Tabs();
    init_EntitySearch();
    init_EntityAutoComplete();
    init_Preview();
    init_Save();
    init_Resize();
    init_LHRD();

    /*
     * Initialize Graph
     */
    minegraph.init("graph", 1200, 600);
    init_Graph_Events();

    /*
     * Load data after page loaded (say .2sec)
     */
    autoload_timeout = setTimeout('load_minegraph();',100);

/*
     * Start Auto save every 30 sec or so as defined
     */
//autosave_interval = setInterval('autosave();', autosave_interval_time);

}); //End Ready

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 *
 *
 * INITIALIZATION FUNCTIONS
 *
 *
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/

/**
 * Initialize large High Res Display Mode Button
 */
function init_LHRD() {
    $("#lhrd").click(function() {
        lhrd_mode_toggle();
        return false;
    });
}

/**
 * Initialize Search Tabs
 */
function init_Tabs() {
    /**
     * Set up Tabs
     */
    $('#browser').tabs({
        select: function(event, ui) {
            //console.log("tab selected: " + ui.tab.hash);
            var request = {};
            request['vis_id'] = vis_id;
            switch(ui.tab.hash)
            {
                case "#frequency":
                    //Only load it the first time.
                    if ($('#f_ents').hasClass('loading-results') == true) {
                        //We want to request: Biclusters Listing
                        request['type'] = "entity_frequencies";
                        //Ajax it up
                        $.post("request.json",
                            request,
                            function(response) {
                                //display resutls
                                $("#f_ents").html(response).removeClass("loading-results");
                                improve_item_lists("#f_ents");
                            },
                            'html'
                            ).error(function() {
                            //display error
                            $("#f_ents").text('=[ oops error here').removeClass("loading-results");
                        });
                    }
                    break;
                case "#browsebic":
                    //Only load it the first time.
                    if ($('#b_bics').hasClass('loading-results') == true) {
                        //We want to request: Biclusters Listing
                        request['type'] = "browse_bic";
                        //Ajax it up
                        $.post("request.json",
                            request,
                            function(response) {
                                //display resutls
                                $("#b_bics").html(response).removeClass("loading-results");
                                improve_item_lists("#b_bics");
                            },
                            'html'
                            ).error(function() {
                            //display error
                            $("#b_bics").text('=[ oops error here').removeClass("loading-results");
                        });
                    }
                    break;
                case "#browsedoc":
                    //Only load it the first time.
                    if ($('#b_docs').hasClass('loading-results') == true) {
                        //We want to request: Documents Listing
                        request['type'] = "browse_doc";
                        //Ajax it up
                        $.post("request.json",
                            request,
                            function(response) {
                                //display resutls
                                $("#b_docs").html(response).removeClass("loading-results");
                                improve_item_lists("#b_docs");
                            },
                            'html'
                            ).error(function() {
                            //display error
                            $("#b_docs").text('=[ oops error here').removeClass("loading-results");
                        });
                    }
                    break;
                case "#browsechain":
                    //Only load it the first time.
                    if ($('#b_chains').hasClass('loading-results') == true) {
                        //We want to request: Chain Links Listing
                        request['type'] = "browse_chain_link";
                        //Ajax it up
                        $.post("request.json",
                            request,
                            function(response) {
                                //display resutls
                                $("#b_chains").html(response).removeClass("loading-results");
                                improve_item_lists("#b_chains");
                            },
                            'html'
                            ).error(function() {
                            //display error
                            $("#b_chains").text('=[ oops error here').removeClass("loading-results");
                        });
                    }
                    break;

                // case "#browseentity":

                //     //Only load it the first time.
                //     if ($('#b_entities').hasClass('loading-results') == true) {
                //         //We want to request: Biclusters Listing
                //         request['type'] = "entity_frequencies";
                //         //Ajax it up
                //         $.post("request.json",
                //             request,
                //             function(response) {
                //                 //display resutls
                //                 $("#b_entities").html(response).removeClass("loading-results");
                //                 improve_item_lists("#f_ents");
                //             },
                //             'html'
                //             ).error(function() {
                //             //display error
                //             $("#b_entities").text('=[ oops error here').removeClass("loading-results");
                //         });
                //     }
                //     break;

                default:
            //nada aqui
            }
        }
    });
}

/**
* Initialize Entity Search
*/
function init_EntitySearch() {
    $('#ent_search').submit(function() {
        //make sure we don't do an empty search
        if ($('#ent_query').val() == "") {
            return false;
        }
        //set up request data
        var request = {};
        request['type'] = "ent-search";
        request['vis_id'] = vis_id;
        request['term'] = $('#ent_query').val();
        request['ent_name'] = $('#ent_name').val();
        request['ent_id'] = $('#ent_id').val();
        request['ent_type'] = $('#ent_type').val();

        // show we're loading stuff...
        $("#ent_results").text(". . . Loading").addClass("loading-results");

        /*
     * Ajax request results
     */
        $.post("request.json",
            request,
            function(response) {
                $("#ent_results").html(response).removeClass("loading-results");
                improve_item_lists("#ent_results");
            },
            'html'
            ).error(function() {
            $("#ent_results").text('=[ oops error here').removeClass("loading-results");
        });

        //cancel normal form handling
        return false;
    });
}

/**
* Initialize Entity Autocomplete
*/
function init_EntityAutoComplete() {
    $("#ent_query").catcomplete({
        /* Ajax Request Auto Complete Data*/
        source: function(request, response) {
            request['type'] = "ent-search-autocomplete";
            request['vis_id'] = vis_id;
            $.getJSON( "request.json", request, function( data, status, xhr ) {
                response( data );
                return;
            });
            return;
        },
        /* Only auto complete with at least 3 letters */
        minLength: 3,
        /* Update Hidden values to hold extra selection data */
        select: function(event, ui) {
            $('#ent_name').val(ui.item.label);
            $('#ent_id').val(ui.item.id);
            $('#ent_type').val(ui.item.category);
        }
    });
}

/**
* Initialize Preview
*/
function init_Preview() {
    //Handle button click
    $("#add_to_workspace").click(function() {
        if ($("#preview .content").children().length > 0) {
            //console.log(preview_data);
            if (preview_data.type == "document") {
                console.log("adding document to graph...");
                graph_add_document(preview_data);
            } else if (preview_data.type == "bicluster") {
                console.log("adding BiCluster to graph");
                graph_add_bicluster(preview_data);
            } else if (preview_data.type == "link") {
                console.log("adding Link to graph...");
                graph_add_link(preview_data);
            } else {
                console.log("unknown preview type error");
            }
        }
        return false;
    });

    /**
 * Make the preview menu follow the view pane if we scroll below the
 * graph.
 */
    $(document).scroll(function() {
        if (!lhrd_mode) {
            var offset = $(document).scrollTop();

            if (offset > floating_preview_top) {
                $('#preview').animate({
                    top:offset
                },{
                    duration:500,
                    queue:false
                });
            } else {
                $('#preview').animate({
                    top:floating_preview_top
                },{
                    duration:500,
                    queue:false
                });
            }
        }
    });
}

/**
* Resize event for workspace graph
*/
function init_Resize() {
    $('#resize a').click(function() {
        var size = $(this).text().split(' x ', 2);
        //alert('width' + size[0]+'height'+ size[1]);
        update_floating_menu_top(minegraph.height, size[1]);
        minegraph.resize(size[0],size[1]);
        // cancel page scroll
        return false;
    });
}

/**
* Debug functions like manual save/load
*/
function init_Save() {
    //save
    $('#save').click(function() {
        save_minegraph();
        return false;
    });
}

/**
* Init graph events
* Added callbacks to minevis for context menu actions like show documents
* of a bicluster
*/
function init_Graph_Events() {
    minegraph.event('bic_show_docs', graph_show_bicluster_documents);
    minegraph.event('bic_show_bics', graph_show_bicluster_links);
    minegraph.event('doc_show_bics', graph_show_document_biclusters);

    minegraph.event('bic_show_thin_bics', graph_show_thin_biclusters);
    minegraph.event('thinBic_show_metaBic', graph_thinBic_show_metaBic);    
}


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
*
*
* USEAGE FUNCTIONS
*
*
* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/

/**
 * Toggle LHRD Mode
 */
function lhrd_mode_toggle() {
    if (lhrd_mode) {
            console.log("Disabling LHRD Mode");
            lhrd_mode = false;
            // Dynamically remove extra css.
            var stylesheets = $("head link[rel=stylesheet]");
            for (var i=0; i<stylesheets.length; i++) {
                //console.log($(stylesheets[i]).attr('href'));
                if ($(stylesheets[i]).attr('href').indexOf('lhrd.css') > 0) {
                    $(stylesheets[i]).remove();
                }
            }
            $('#preview').css('top', floating_preview_top+'px');
        } else {
            console.log("Enabling LHRD Mode");
            lhrd_mode = true;
            // Dynamically load extra css.
            var path = $("head link[rel=stylesheet]:first").attr('href');
            path = path.split("css/",2)[0] + "css/"; //small path hack to avoid having to manually figure out the css folder.
            //console.log(path);
            $('#preview').css('top', '190px');
            $("head").append($("<link rel='stylesheet' type='text/css' media='screen' href='" + path + "lhrd.css' type='text/css' media='screen' />"));
            update_floating_menu_top(minegraph.height,3100);
            minegraph.resize(7600, 3100);
        }
        //console.log(lhrd_mode);
}

/**
* auto save every 10 or so seconds
*/
function autosave() {
    console.log('auto save starting...');
    save_minegraph();
}

/**
* Gets the graph data, ajax it and save it to the vis
*/
function save_minegraph() {
    console.log("Saving Data");
    //get save
    var minegraphSave = minegraph.save();
    minegraphSave.LHRD = lhrd_mode;
    var JSONSave = JSON.stringify(minegraphSave);

    console.log(minegraphSave);
    var data = {
        vis_id: vis_id,
        save: JSONSave
    };
    $.post("save",
        data,
        function(response) {
            console.log("succesfully saved minegraph");
        //console.log(response);
        },
        'html'
        ).error(function() {
        console.log("save ajax request failed");
    });

    //Update last save time
    $('#file span').text(new Date(minegraph.last_save).toString());
}

/**
* Load it from ajax datas
*/
function load_minegraph() {
    console.log(vis_data);
    /* Check if save contains anything */
    if (vis_data) {
        console.log('Loading Data');

        /* load size */
        update_floating_menu_top(minegraph.height, vis_data.height);
        minegraph.resize(vis_data.width, vis_data.height);

        /*  LHRD Mode */
        if (vis_data.LHRD) {
            lhrd_mode_toggle();
        }

        /* load documents */
        console.log("loading documents...");
        var d;
        for(var i=0; i < vis_data.documents.length; i++) {
            d = vis_data.documents[i];
            //console.log(d);
            minegraph.addDocument(d.data, d.x, d.y);
        }

        /* load biclusters */
        console.log("loading biclusters...");
        var b;
        for(i=0; i < vis_data.biclusters.length; i++) {
            b = vis_data.biclusters[i];
            //console.log(b);
            minegraph.addBic(b.data, b.x, b.y);
        }

        /* load links */
        console.log("loading links...");
        var l,from,to, links;
        var links_to_remove; //must be queued
        for(i=0; i < vis_data.links.length; i++) {
            links_to_remove = []; //reset queue each time
            l = vis_data.links[i];
            from = minegraph.findFromTypeId(l.tid1);
            to = minegraph.findFromTypeId(l.tid2);
            //Don't load a deleted link
            if (from != null && to != null) {
                if (!l.cell) {
                    minegraph.link(from, to, l.userlink);
                } else {
                    //Load link for all cells
                    link = minegraph.link(from, to);
                    links = minegraph.findLinks(from, to);
                    // Find any cell links that were not in the save file to remove them.
                    for(var ii=0; ii < links.length; ii++) {
                        if (l.cell.indexOf(links[ii].cell) == -1) {
                            // Can't remove the link while looping the array
                            // so queue it for later.
                            links_to_remove.push(links[ii]);
                        }
                    }
                    // Now that we're outside the loop remove them safely
                    for(ii=0; ii < links_to_remove.length; ii++) {
                        console.log('    removing link not in save: ' + links_to_remove[ii].cell);
                        minegraph.removeLink(links_to_remove[ii]);
                    }
                }
            }
        }

        /* load highlights */
        console.log("loading highlights...");
        var h;
        for(i=0; i < vis_data.highlights.length; i++) {
            h = vis_data.highlights[i];
            //console.log(h);
            // Find Objects
            from = minegraph.findFromTypeId(h.tid1);
            to = minegraph.findFromTypeId(h.tid2);
            // Find Links
            links = minegraph.findLinks(from, to);
            // Simple Link (no cells)
            if (links.length == 1 && !links[0].cell) {
                minegraph.toggle_link_highlight(links[0]);
            } else if (links.length > 0 && links[0].cell ) {
                for(ii=0; ii < links.length;ii++) {
                    if (links[ii].cell == h.cell) {
                        minegraph.toggle_link_highlight(links[ii]);
                    }
                }
            } else {
                console.log("error! cannot find link for highlight: ");
                console.log(h);
            }
        }

        console.log("Loading Complete!");
    } else {
        console.log('No data to load, new graph');
    }
}

/**
 * Update floating preview minimum top
 * @param old_h int old height
 * @param new_h int new height
 */
function update_floating_menu_top(old_h, new_h) {
    floating_preview_top = floating_preview_top - (old_h*1) + (new_h*1);
    if (!lhrd_mode) {
        $('#preview').css('top', floating_preview_top+'px');
    }
}

/**
 * Adds a document to the graph
 * @param documentJSON JSON document data
 */
function graph_add_document(documentJSON) {
    if (minegraph.findDocument(documentJSON.id) == null) {
        minegraph.addDocument(documentJSON, insert_location_x, insert_location_y);
        increment_insert_location();
    } else {
        minegraph.alert('This document is already in the graph.');
    }
}

/**
 * Adds a bicluster to the graph
 * @param biclusterJSON JSON bicluster data
 */
function graph_add_bicluster(biclusterJSON) {
    if (minegraph.findBiCluster(biclusterJSON.id) == null) {
        minegraph.addBic(biclusterJSON, insert_location_x, insert_location_y);
        increment_insert_location();
    } else {
        minegraph.alert('This bicluster is already in the graph');
    }
}

/**
 * Shows the documents a bicluster was generated from next to it.
 * @param bic Object bicluster to show documents for
 */
function graph_show_bicluster_documents(bic) {
    var bb = bic.getBBox();
    console.log('loading documents for bicluster ' + bic.id);

    // Request Parameters
    var request = new Object();
    request['vis_id'] = vis_id;
    request['ent_id'] = bic.id;
    request['type'] = 'show_bic_docs';

    // Ajax me so stuff
    $.get("request.json",
        request,
        function(response) {
            //console.log(response);
            var d,x,y;
            x = bb.x + bb.width + 50;
            y = 10;
            for (var i=0; i < response.length; i++) {
                //Find or Add document
                d = minegraph.findDocument(response[i].id);
                if (d == null) {
                    d = minegraph.addDocument(response[i], x, y);
                    y += d.height + 10;
                } else {
                    console.log('    doc ' + d.id + ' already in the graph');
                }
                // Try to link
                if (minegraph.findLinks(bic, d).length == 0) {
                    minegraph.link(bic, d);
                } else {
                    console.log('    doc ' + d.id + ' already linked to bic');
                }
            }
        },
        'json'
        ).error(function() {
        minegraph.alert("error loading bicluser documents.");
    });
}

/**
 * Shows the bicluster a bicluster has links to next to it.
 * @param bic Object bicluster to show documents for
 */
function graph_show_bicluster_links(bic) {
    var bb = bic.getBBox();
    var x = bb.x + bb.width + 100;
    var y = 50;
    var d;
    console.log('graph_show_bicluster_links');

    // Request Parameters
    var request = new Object();
    request['vis_id'] = vis_id;
    request['ent_id'] = bic.id;
    request['type'] = 'show_bic_links';

    // Ajax me so stuff
    $.get("request.json",
        request,
        function(response) {
            console.log(response);
            for( var i=0; i < response.length; i++) {
                // Find or add BiCluster
                d = minegraph.findBiCluster(response[i].id);
                if (d == null) {
                    d = minegraph.addBic(response[i], x, y);
                    y += d.height + 30;
                } else {
                    console.log("Bicluster " + response[i].id + " is already in the graph");
                }
                // Link them if not already linked
                if (minegraph.findLinks(bic, d).length == 0) {
                    minegraph.link(bic, d);
                } else {
                    console.log("Biclusters " + bic.id + " & " + response[i].id + " are already linked");
                }
            }
        },
        'json'
        ).error(function() {
        minegraph.alert("error loading biclusers.");
    });
}



function graph_show_thin_biclusters(bic, row_name, col_name, row_pos, col_pos, flag) {

    var bb = bic.getBBox();
    var x = bb.x + bb.width + 100;
    var y = 50;
    var d;

    if (flag == 0) {
        console.log('graph_show_thin_biclusters by row name');       
    } else if(flag == 1) {
        console.log('graph_show_thin_biclusters by col name')
    }

    var thinBic = {
        type: 'thinBic',
        id: '',
        grid: [],
        rows: [],
        cols: [],
        row_type: '',
        col_type: '',
        // a flag to show how the thin bicluster is generated, 0 - by row, 1 - by col.
        flag_row_or_col: 0,
    }

    thinBic["id"] = "(" + parseInt(bic.id) + ')(' + col_pos + ", " + row_pos + ", " + flag + ")";    

    var tmp = [];
    var tmpArray = [];
    var index = 0;

    // cancel ajax
    $.ajaxSetup({async:false});

    // adding all cols names 
    if (flag == 0) {             
        for (var i = 0; i < bic.xlabels.length; i++) {
            tmp[index] = $(bic.xlabels[i].node).text();
            index++;
            // console.log(tmp[i]);
        }      
    }

    // adding all row names
    if (flag == 1) {
        for (var i = 0; i < bic.ylabels.length; i++) {
            tmp[index] = $(bic.ylabels[i].node).text();
            index++;
            // console.log(tmp[i]);
        }
    }

    // Request Parameters
    var request = new Object();
    request['vis_id'] = vis_id;
    request['ent_id'] = bic.id;
    request['type'] = 'show_bic_links';  

    // Ajax me so stuff
    $.get("request.json",
        request,
        function(response) {
            // console.log(response);
            for(var i = 0; i < response.length; i++) {

                // match the name by row
                if (flag == 0) {
                    for (var j = 0; j < response[i].rows.length; j++) {
                        if (response[i].rows[j].name == row_name) {
                            for (var k = 0; k < response[i].cols.length; k++) {
                                tmp[index] = response[i].cols[k].name;
                                index++;
                            }
                        }
                    }

                    for (var j = 0; j < response[i].cols.length; j++) {
                        if (response[i].cols[j].name == row_name) {
                            for (var k = 0; k < response[i].rows.length; k++) {
                                tmp[index] = response[i].rows[k].name;
                                index++;
                            }                            
                        }
                    }
                }

                // match the name by col
                if (flag == 1) {
                    for (var j = 0; j < response[i].cols.length; j++) {
                        if (response[i].cols[j].name == col_name) {
                            for (var k = 0; k < response[i].rows.length; k++) {
                                tmp[index] = response[i].rows[k].name;
                                index++;
                            }                            
                        }
                    } 
                    
                    for (var j = 0; j < response[i].rows.length; j++) {
                        if (response[i].rows[j].name == col_name) {
                            for (var k = 0; k < response[i].cols.length; k++) {
                                tmp[index] = response[i].cols[k].name;
                                index++;
                            }
                        }
                    }                                       
                }
            }

            if (flag == 0) {

                // insert col_name into this array
                tmp.splice(0, 0, col_name);

                // remove the repeat elements in this array
                removeRepeat(tmp);

                thinBic["rows"] = new Array(1);
                thinBic["rows"][0] = [];
                thinBic["rows"][0]["name"] = row_name;
                thinBic["rows"][0]["row"] = "";

                thinBic["cols"] = new Array(tmp.length);
                for (var i = 0; i < thinBic["cols"].length; i++) {
                    thinBic["cols"][i] = [];
                    thinBic["cols"][i]["col"] = "";
                    thinBic["cols"][i]["name"] = tmp[i];
                    // console.log("cols name: " + thinBic.cols[i].name);
                }

                thinBic["grid"] = new Array(1);
                thinBic["grid"][0] = [];
                for (var i = 0; i < tmp.length; i++) {
                    thinBic.grid[0][i] = 2;
                    // console.log("thinBic[\"gird\"][0][" + i + "] is: " + thinBic.grid[0][i]);
                }

                thinBic.flag_row_or_col = 0;
            }

            if (flag == 1) {

                // insert col_name into this array
                tmp.splice(0, 0, row_name);

                // remove the repeat elements in this array
                removeRepeat(tmp);

                // thinBic["id"] = parseInt(bic.id) + '\"' + col_name + '\" by col';
                // console.log(thinBic["id"]);

                thinBic["cols"] = new Array(1);
                thinBic["cols"][0] = [];
                thinBic["cols"][0]["name"] = col_name;
                thinBic["cols"][0]["col"] = "";

                thinBic["rows"] = new Array(tmp.length);
                for (var i = 0; i < thinBic["rows"].length; i++) {
                    thinBic["rows"][i] = new Array(1);
                    thinBic["rows"][i]["row"] = "";
                    thinBic["rows"][i]["name"] = tmp[i];
                    // console.log("cols name: " + thinBic.cols[i].name);
                }

                thinBic["grid"] = new Array(tmp.length);
                for (var i = 0; i < tmp.length; i++) {
                    thinBic["grid"][i] = new Array(1);
                    thinBic["grid"][i][0] = 2;
                }

                thinBic.flag_row_or_col = 1;             
            }


            // Find or add BiCluster
            d = minegraph.findBiCluster(thinBic.id);
            if (d == null) {
                d = minegraph.addBic(thinBic, x, y);
                y += d.height + 30;
            } else {
                console.log("Bicluster " + thinBic.id + " is already in the graph");
            }

            // Link them if not already linked
            if (minegraph.findLinks(bic, d).length == 0 && bic.id != d.id) {
                minegraph.link(bic, d);
            } else {
                console.log("Biclusters " + bic.id + " & " + thinBic.id + " are already linked");
            }
    });  
}


function graph_thinBic_show_metaBic(bic, row_name, col_name, row_pos, col_pos) {
    var bb = bic.getBBox();
    var x = bb.x + bb.width + 100;
    var y = 50;
    var d;
    console.log('thinBic_show_metaBic');

    var metaBic = {
        type: 'metaBic',
        id: '',
        grid: [],
        rows: [],
        cols: [],
        row_type: '',
        col_type: '',
    }

    metaBic.id = bic.id + "(" + row_pos + ", " + col_pos + ")";

    metaBic["rows"] = new Array(1);
    metaBic["rows"][0] = [];
    metaBic["rows"][0]["name"] = row_name;
    metaBic["rows"][0]["row"] = "";

    metaBic["cols"] = new Array(1);
    metaBic["cols"][0] = [];
    metaBic["cols"][0]["name"] = col_name;
    metaBic["cols"][0]["col"] = "";

    metaBic["grid"] = new Array(1);
    metaBic.grid[0] = [];
    metaBic.grid[0][0] = 2;

    // Find or add BiCluster
    d = minegraph.findBiCluster(metaBic.id);
    if (d == null) {
        d = minegraph.addBic(metaBic, x, y);
        y += d.height + 30;
    } else {
        console.log("Bicluster " + metaBic.id + " is already in the graph");
    }  

    // Link them if not already linked
    if (minegraph.findLinks(bic, d).length == 0 && bic.id != d.id) {
        minegraph.link(bic, d);
    } else {
        console.log("Biclusters " + bic.id + " & " + metaBic.id + " are already linked");
    }      
}



/*
* remove the repeat elements in an array
* @param an array
*/
function removeRepeat(anArray) {
    for (var i = 0; i < anArray.length; i++) {
        for (var j = i + 1; j < anArray.length; j++) {
            if (anArray[i] == anArray[j]) {
                anArray.splice(j, 1);
                j--;
            }
        }
    }
}


/**
 * Shows the biclusters this document contributed to generating.
 * @param doc Object document to show documents for
 */
function graph_show_document_biclusters(doc) {
    var bb = doc.getBBox();
    var x = bb.x + bb.width + 100;
    var y = 50;
    var b;
    console.log('graph_show_document_biclusters');

    // Request Parameters
    var request = new Object();
    request['vis_id'] = vis_id;
    request['ent_id'] = doc.id;
    request['type'] = 'show_doc_bics';

    // Ajax me so stuff
    $.get("request.json",
        request,
        function(response) {
            //console.log(response);
            for (var i=0; i < response.length; i++) {
                b = minegraph.findBiCluster(response[i].id);
                if (b == null) {
                    b = minegraph.addBic(response[i], x, y);
                    y += b.height + 30;
                } else {
                    console.log("Bicluster " + response[i].id + " is already in the graph");
                }
                // Link them if not already linked
                if (minegraph.findLinks(doc, b).length == 0) {
                    minegraph.link(doc, b);
                } else {
                    console.log("Document " + doc.id + " & Bicluster " + b.id + " are already linked");
                }
            }
        },
        'json'
        ).error(function() {
        minegraph.alert("error loading document biclusers.");
    });
}

/**
 * Adds a link to the graph
 * @param linkJSON JSON link data
 */
function graph_add_link(linkJSON) {
    var existing_bic_notice = false; // has a notice been sent
    var d, x, y, l;
    var insert_count = 0;

    /*
     * Add Target or find it if it exists
     */
    var target = minegraph.findBiCluster(linkJSON.target.id);
    if (target == null) {
        // target isn't in the graph add it
        target = minegraph.addBic(linkJSON.target, insert_location_x, insert_location_y);
        increment_insert_location();
    } else {
        // target is already in the graph, warn user we're using prexisiting nodes
        minegraph.alert("Note: one or more biclusters from this link were already in the graph and won't be reinserted");
        existing_bic_notice = true;
    }

    /*
     * Loop through destination and
     * find/add them and then link them to target (if not linked)
     */
    x = insert_location_x + target.width + insert_offset_x;
    y = insert_location_y;

    for (var i = 0; i < linkJSON.destinations.length; i++) {
        // does it already exsit in the graph?
        d = minegraph.findBiCluster(linkJSON.destinations[i].id);
        if (d == null) {
            // it's not in the graph load it.
            d = minegraph.addBic(linkJSON.destinations[i], x, y);
            insert_count++;
            y = y + d.height + insert_offset_y;
        } else {
            // Notify user if he hasn't been already.
            if (existing_bic_notice == false) {
                minegraph.alert("Note: one or more biclusters from this link were already in the graph and won't be reinserted");
                existing_bic_notice = true;
            }
        }
        // check if it's already linked?
        l = minegraph.findLinks(target, d);
        if (l.length == 0) {
            // it's not linked so link it
            minegraph.link(target, d);
        }
    }

    // update insert location if we added some destinations
    if (insert_count > 0) {
        x = insert_location_x + target.width + insert_offset_x;
        increment_insert_location_x_y(x,y);
    }
}

/**
 * Increment insert location for next insert
 */
function increment_insert_location() {
    insert_location_x += insert_offset_x;
    if (insert_location_x > minegraph.width) {
        insert_location_x = insert_offset_x;
    }
    insert_location_y += insert_offset_y;
    if (insert_location_y > minegraph.height) {
        insert_location_y = insert_offset_y;
    }
}

/**
 * Increment insert location for next insert
 * @param x int offset from x.
 * @param y int offset from y.
 */
function increment_insert_location_x_y(x,y) {
    console.log('insert_location_x' + insert_location_x);
    console.log('insert_location_y' + insert_location_y);
    insert_location_x += x + insert_offset_x;
    if (insert_location_x > minegraph.width) {
        insert_location_x = insert_offset_x;
    }
    insert_location_y += y + insert_offset_y;
    if (insert_location_y > minegraph.height) {
        insert_location_y = insert_offset_y;
    }
}

/**
 * Alternative Item Selection
 */
function add_frequency_search() {
    $('#f_ents a.item').click(function() {
        /*
         * Get the entity's data and parse it into an array
         * 0 - name
         * 1 - type
         * 2 - id
         */
        var data = JSON.parse($(this).children('.item_id').text());

        //Switch tab
        $('#browser').tabs('option', 'selected', 0);

        //Set forms data
        $('#ent_name').val(data[0]);
        $('#ent_query').val(data[0]);
        $('#ent_id').val(data[2]);
        $('#ent_type').val(data[1]);

        //Trigger search
        $('#ent_search').submit();
    });
}

/**
 * Item selection
 */
function add_preview_event(div) {
    $(div + ' a.item').click(function() {
        //console.log(this);
        // Get the id and element type
        type = $(this).children('.item_type').text();
        id = $(this).children('.item_id').text();
        $('#preview .content').html(type + ": " + id + "<br/>");

        // Ajax Request
        var request = new Object();
        request['vis_id'] = vis_id;
        request['ent_type'] = type;
        request['ent_id'] = id;

        //Load it up
        if (type == "bic") {
            $('#preview .content').addClass("loading-results").append("loading bic...");
            // set request type
            request['type'] = 'preview_bic';
            // send request
            $.get("request.json",
                request,
                function(response) {
                    $("#preview .content").html(response).removeClass("loading-results");
                },
                'html'
                ).error(function() {
                $("#preview .content").text('=[ oops error here').removeClass("loading-results");
            });
        } else if (type== "doc") {
            // display loading
            $('#preview .content').addClass("loading-results").append("loading doc...");
            // set request type
            request['type'] = 'preview_doc';
            // send request
            $.get("request.json",
                request,
                function(response) {
                    $("#preview .content").html(response).removeClass("loading-results");
                },
                'html'
                ).error(function() {
                $("#preview .content").text('=[ oops error here').removeClass("loading-results");
            });
        } else if (type == "link") {
            $('#preview .content').addClass("loading-results").append("loading link...");
            // set request type
            request['type'] = 'preview_link';
            // send request
            $.get("request.json",
                request,
                function(response) {
                    $("#preview .content").html(response).removeClass("loading-results");
                },
                'html'
                ).error(function() {
                $("#preview .content").text('=[ oops error here').removeClass("loading-results");
            });
        } else {
            $('#preview .content').append(" This type is not yet supported");
        }
        //disable link event thingy
        return false;
    })
}

/**
 * Set up collapsing ul lists for resutls...
 */
function improve_item_lists(div) {

    // Find list items representing folders and
    // style them accordingly.  Also, turn them
    // into links that can expand/collapse the
    // tree leaf.
    $(div +' li > ul').each(function(i) {
        //console.log(i);
        // Find this list's parent list item.
        var parent_li = $(this).parent('li');

        // Style the list item as folder.
        parent_li.addClass('folder');

        // Temporarily remove the list from the
        // parent list item, wrap the remaining
        // text in an anchor, then reattach it.
        var sub_ul = $(this).remove();
        parent_li.wrapInner('<a/>').find('a').click(function() {
            // Make the anchor toggle the leaf display.
            sub_ul.toggle();
        });
        parent_li.append(sub_ul);
    });

    // Hide all lists except the outermost.
    $(div + ' ul ul').hide();

    //but still show the first one...
    $(div + ' ul ul').filter(':first').show();

    if ($(div).attr('id') == 'f_ents') {
        // add click handling to pull up a seach
        add_frequency_search();
    } else {
        // add click handling to preview the items
        add_preview_event(div);
    }

}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 *
 *
 * JQuery Extensions
 *
 *
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/

/**
 * This adds categories to the auto complete.
 */
$.widget( "custom.catcomplete", $.ui.autocomplete, {
    _renderMenu: function( ul, items ) {
        var self = this,
        currentCategory = "";
        $.each( items, function( index, item ) {
            if ( item.category != currentCategory ) {
                ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                currentCategory = item.category;
            }
            self._renderItem( ul, item );
        });
    }
});