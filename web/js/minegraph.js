/**
 * MineGraph
 *
 * Small Framework like implementation for MineVis Graph Workspace Environement.
 * Author & copyright: Patrick Fiaux
 * Contact: pfiaux@vt.edu
 *
 * To Do
 * Move some of the helper function to either core or a different sub space to
 * "hide" them. Not important but nice to make them less accesible to avoid bugs.
 *
 * This relies on:
 * JQuery
 * JQuery Context Menu
 * RaphaelJS
 *
 */
var minegraph = {
    VERSION: '0.4.1',
    graph: null,
    width: 1200,
    height: 650,
    container: '',
    last_save: null,
    /* private ish variables */
    core: {
        colors: {},
        documents: [],
        biclusters: [],
        links: [],
        events: {
            bic_show_docs: [],
            bic_show_bics: [],
            doc_show_bics: [],

            bic_show_thin_bics: [],
            thinBic_show_metaBic: []
        },
        link_object1: null
    }
};

/**
 * Define some color constants
 */
minegraph.core.colors.orange = '#FF6F00';
minegraph.core.colors.green = '#5DD838';
minegraph.core.colors.blue_10 = 'rgba(135,135,255,.1)';
minegraph.core.colors.blue = 'rgba(135,135,255,1)';
minegraph.core.colors.white = '#fff';
minegraph.core.colors.orange_80 = 'rgba(255,111,0,.8)';
minegraph.core.colors.orange_2_80 = 'rgba(207,90,0,.8)';
minegraph.core.colors.white_80 = 'rgba(255,255,255,.8)';
minegraph.core.colors.black = '#000';

/**
 * Initialize graph to set element and coordinates
 * @param {String} elementId name of element to graph on
 * @param {int} width graph width
 * @param {int} height graph height
 */
minegraph.init = function(elementId, width, height) {
    minegraph.width = width;
    minegraph.height = height;
    minegraph.container = elementId;
    minegraph.graph = Raphael(elementId, width, height);
    $('body').children(':last').after("<div id='minegraph-dialog'></div>");
    $('#minegraph-dialog').dialog({
        autoOpen: false,
        hide: "fade"
    });
};

/**
 * Resizes the raphael graph
 * @param {int} width new graph width
 * @param {int} height new graph height
 */
minegraph.resize = function(width, height) {
    //resize canvas
    minegraph.width = width;
    minegraph.height = height;
    minegraph.graph.setSize(width, height);

    // resize css container node
    $('#'+minegraph.container).width(width).height(height);

};

/**
 * Saves the current graph and it's data to a JSON object for loading later
 *
 * @return JSON save object
 */
minegraph.save = function() {
    var save = {};
    var i, t;

    /* Save the graph size to resize on load */
    save.width = minegraph.width;
    save.height = minegraph.height;
    /* Save Documents */
    save.documents = [];
    for (i = 0; i < minegraph.core.documents.length; i++) {
        t = minegraph.core.documents[i];
        bb = t.getBBox(); //bounding box as x and y with transformations applied
        save.documents[i] = {
            id: t.id,
            x: bb.x,
            y: bb.y
        }
    }

    /* Save BiClusters */
    save.biclusters = [];
    for (i = 0; i < minegraph.core.biclusters.length; i++) {
        t = minegraph.core.biclusters[i];
        // get the bounding box of the frame to avoid including labels
        bb = t.frame.getBBox(); //bounding box as x and y with transformations applied
        save.biclusters[i] = {
            id: t.id,
            x: bb.x,
            y: bb.y
        }
    }

    /* Save Links */
    save.links = [];
    var l; //save link for cell links
    var link_i = 0; //save index, different since duplicates are merged
    for (i = 0; i < minegraph.core.links.length; i++) {
        t = minegraph.core.links[i];
        if (t.cell) {
            //console.log(t);
            l = null;
            //try to find matching link
            for (var ii = 0; ii < save.links.length; ii++) {
                if (save.links[ii].tid1 == t.typeid1 && save.links[ii].tid2 == t.typeid2) {
                    l = save.links[ii];
                }
            }
            if (l==null) {
                save.links[link_i] = {
                    tid1: t.typeid1,
                    tid2: t.typeid2,
                    cell: []
                };
                l = save.links[link_i];
                link_i++;
            }
            //console.log(l);
            l.cell.push(t.cell); // add entity to the list for that link
        } else {
            save.links[link_i] = {
                tid1: t.typeid1,
                tid2: t.typeid2,
                cell: t.cell,
                userlink: t.userlink
            };
            link_i++;
        }
    }

    /* Save Highlights */
    save.highlights = [];
    var h_index = 0;
    for (i = 0; i < minegraph.core.links.length; i++) {
        t = minegraph.core.links[i];
        if (minegraph.is_link_highlighted(t)) {
            save.highlights[h_index] = {
                tid1: t.typeid1,
                tid2: t.typeid2,
                cell: t.cell
            };
            h_index++;
        }
    }


    /* Update last save tracker */
    minegraph.last_save = new Date().getTime();
    return save;
};


/**
 * Find a document or bicluster from a typeid
 * @param typeId string
 * @return object or null if nothing...
 */
minegraph.findFromTypeId = function(typeId) {
    var id;
    if (typeId.indexOf("bicluster") > -1) {
        id = typeId.split("bicluster")[1];
        //console.log("bic id " + id);
        return this.findBiCluster(id);
    } else if (typeId.indexOf("document") > -1) {
        id = typeId.split("document")[1]*1;
        //console.log("doc id " + id);
        return this.findDocument(id);
    } else {
        //console.log("no typeid match");
        return null;
    }
};

/**
 * Find a Document in the graph
 * @param docId int of document we're looking for
 * @return Object graph document object if found, null otherwise
 */
minegraph.findDocument = function(docId) {
    var d;
    for(var i = 0; i < minegraph.core.documents.length; i++) {
        d = minegraph.core.documents[i];
        if (d.id == docId) {
            return d;
        }
    }
    return null;
};

/**
 * Find a BiCluster already in the graph
 * @param bicId int id of document we're looking for
 * @return Object graph document object if found, null otherwise
 */
minegraph.findBiCluster = function(bicId) {
    var b;
    for(var i = 0; i < minegraph.core.biclusters.length; i++) {
        b = minegraph.core.biclusters[i];
        if (b.id == bicId) {
            return b;
        }
    }
    return null;
};

/**
 * Find a line link between two elements
 * @param obj1 Object either bic or doc
 * @param obj2 Object either bic or doc
 * @return Array array with all the matching links regardless of order
 * (obj1->obj2 or obj2->obj1)
 */
minegraph.findLinks = function(obj1, obj2) {
    var links = [];
    var l;
    for(var i = 0; i < minegraph.core.links.length; i++) {
        l = minegraph.core.links[i];
        if (l.typeid1 == obj1.type+""+obj1.id && l.typeid2 == obj2.type+""+obj2.id) {
            links.push(l);
        } else if (l.typeid2 == obj1.type+""+obj1.id && l.typeid1 == obj2.type+""+obj2.id) {
            links.push(l);
        }
    }
    return links;
}

//todo find chaininglink

/**
 * Creates a visual representation of a document.
 *
 * @param jsonDoc json containing the document data
 * @param x vertical position of document
 * @param y horizontal position of document
 * @return created set
 */
minegraph.addDocument = function(jsonDoc , x, y) {

    // console.log('new Document (' + jsonDoc["id"] + ')');

    var d = minegraph.graph.set();
    //set the properties
    d.type = "document";
    d.id = jsonDoc["id"];
    d.width = 300;
    d.height = 150;
    d.source_data = jsonDoc;
    d.frame = minegraph.graph.rect(x, y, d.width, d.height, 5).attr({
        fill: "rgba(255, 255, 255, .5)",
        cursor: "move"
    });
    d.title_frame = minegraph.graph.rect(x, y, d.width, 20, 5).attr({
        fill: "rgba(0, 0, 0, .2)",
        cursor: "move"
    });
    d.title = minegraph.graph.text(x+10, y+10, jsonDoc['name']).attr({
        'text-anchor': 'start',
        'fill' : 'white',
        'stroke-opacity' : 0 //invisible stroke so nothing highlights when dragging
    });
    d.content = minegraph.graph.text(x+10, y+30, jsonDoc['text']).attr({
        'text-anchor': 'start',
        'fill' : 'white',
        'stroke-opacity' : 0 //invisible stroke so nothing highlights when dragging
    });
    this.linewraptext(d.content, jsonDoc['text'],d.width-40);

    d.frame.attr('height',d.content.getBBox().height+30);
    d.height = d.content.getBBox().height+30;

    //group them into a set
    d.push(
        //Bounding rectlangle
        d.frame,
        //Title bar
        d.title_frame,
        //Title text
        d.title,
        //Document text content
        d.content
        );

    // push document to doc list
    minegraph.core.documents.push(d);

    // make sure document isn't added ouside of graph.
    minegraph.clipSet(d);

    //set events
    this.set_doc_context_menu(d);
    this.setdrag(d);
    return d;
};

/**
 * Creates a visual representation of a document.
 *
 * @param jsonBic json containing the bicluster data
 * @param x vertical position of document
 * @param y horizontal position of document
 * @return bic cluster set
 */
minegraph.addBic = function(jsonBic, x, y) {

    // if (jsonBic.type == "bicluster") {
    //     console.log('new BiCluster (' + jsonBic['id'] + ')');       
    // } else if (jsonBic.type == "thinBic") {
    //     console.log('new thinBic (' + jsonBic['id'] + ')');
    // } else if (jsonBic.type == "metaBic") {
    //     console.log('new metBic (' + jsonBic['id'] + ')');
    // }

    var bic = minegraph.graph.set();
    var margin = 5;
    var gridX = jsonBic.grid[0].length;
    var gridY = jsonBic.grid.length;
    var cellSpacing = 2;
    var cellW = 15;
    var cellH = 15;
    //temps
    var r, c, dx, dy, t, txt;

    //set the main properties
    bic.type = "bicluster";
    // bic.type = jsonBic.type;
    bic.id = jsonBic['id'];

    // used to store the column and row names
    var textArray = new Array();
    textArray[0] = new Array();
    textArray[1] = new Array();

    // store the number of documents in each cell
    var docNumArray = new Array();
    for (var i = 0; i < gridY; i++) {
        docNumArray[i] = new Array();
        for (var j = 0; j < gridX; j++) {
            docNumArray[i][j] = 0;
        }
    }

    // record doc numbers for each cell
    var num = 0;
    // boolean to chech whether the column name is in the document
    var cNameExist;
    // boolean to chech whether the row name is in the document    
    var rNameExist;
    // store the content of the response document    
    var resText;    

    // store color for each cells
    var colorArray = new Array();
    // index in the colorArray
    var colorIndex;
    // id of the 1st cell in each bicluster
    var startID;

    bic.source_data = jsonBic;
    bic.width = gridX * cellW + 2 * margin;
    bic.height = gridY * cellH + 2 * margin;
    bic.frame = minegraph.graph.rect(x, y, bic.width, bic.height, 5).attr({
        fill: "rgba(255, 255, 255, .5)",
        cursor: "move"
    });

    //draw axis y
    bic.ylabels = minegraph.graph.set();
    for( r = 0; r < gridY; r+=1) {
        dx = x - 5;
        dy = y + r * cellH + 12;
        t = 'undefined';
        if (typeof jsonBic.rows[r] != 'undefined' ) {
            t = jsonBic.rows[r].name;
            textArray[1][r] = t;
        }
        txt = minegraph.graph.text(dx,dy,t).attr({
            'fill': minegraph.core.colors.white_80,
            'text-anchor': 'end',
            'stroke-opacity' : 0
        });      
        bic.ylabels.push(txt);
    }

    //draw axis x
    bic.xlabels = minegraph.graph.set();
    for( c = 0; c < gridX; c+=1) {
        dy = y + margin-10;
        dx = x + margin + c * cellW + 7;
        t = 'undefined';
        if (typeof jsonBic.cols[c] != 'undefined' ) {
            t = jsonBic.cols[c].name;
            textArray[0][c] = t;
        }
        txt = minegraph.graph.text(dx,dy,t).attr({
            'fill': minegraph.core.colors.white_80,
            'text-anchor': 'end',
            'stroke-opacity' : 0
        });
        txt.rotate(45,dx,dy);
        bic.xlabels.push(txt);
    }

    // draw an tag for each bicluster
    bic.idTag = minegraph.graph.set();

    dy = y + gridY * cellH + 18;
    
    if (jsonBic.type == "bicluster") {
        t = "Bic " + jsonBic.id; 
        dx = x + (gridX * cellW + 2 * margin) * 3 / 4;               
    }

    if (jsonBic.type == "thinBic") {
        // t = "From " + parseInt(jsonBic.id);

        t = "thinBic";

        // thinBic generated by row name
        if (jsonBic.flag_row_or_col == 0) {
            dx = x + (gridX * cellW + 2 * margin) * 3 / 4;
        }

        if (jsonBic.flag_row_or_col == 1) {
            dx = x + (gridX * cellW + 2 * margin) * 5 / 4;
        }
    }

    if (jsonBic.type == "metaBic") {
        t = "metaBic";
    }

    txt = minegraph.graph.text(dx,dy,t).attr({
        'fill': minegraph.core.colors.white_80,
        'text-anchor': 'end',
        'stroke-opacity' : 0
    });
    bic.idTag.push(txt);   


    // Request Parameters
    var request = new Object();
    request['vis_id'] = vis_id;
    request['ent_id'] = bic.id;
    request['type'] = 'show_bic_docs';

    // store the content of each documents
    var responseArray = new Array();   

    // cancel ajax
    $.ajaxSetup({async:false});

    /*
    * color cells for basic biclusters
    */
    if (jsonBic.type == "bicluster") {

       // request documents from the server
        $.post("request.json",
            request,
            function(response) {          
                // get content for each documents
                for (var i = 0; i < response.length; i++) {
                    responseArray[i] = response[i];
                }
        });

        // generate a table contains doc number for each cell
        for (var i = 0; i < textArray[0].length; i++) {
            for (var j = 0; j < textArray[1].length; j++) {

                docNumArray[j][i] = 0;

                for (var k = 0; k < responseArray.length; k++) {
                    
                    // get file content
                    resText = responseArray[k]['text'];
                    cNameExist = parseInt(resText.indexOf(textArray[0][i]));
                    rNameExist = parseInt(resText.indexOf(textArray[1][j]));
                    
                    // both items are in the file
                    if (cNameExist > 0 && rNameExist > 0)
                        num++;

                    if (cNameExist > 0 && rNameExist < 0) {
                        // find the full name of the state
                        var tmpRow = stateName(textArray[1][j], stateAbb, state);

                        if (tmpRow != 0) {
                            rNameExist = parseInt(resText.indexOf(tmpRow));
                            if (rNameExist > 0)
                                num++;
                        }
                    }

                    if (cNameExist < 0 && rNameExist > 0) {
                        
                        // find the full name of a state
                        var tmpColumn = stateName(textArray[0][i], stateAbb, state);

                        if (tmpColumn != 0) {
                            // check whether the full name is in the document
                            cNameExist = parseInt(resText.indexOf(tmpColumn));
                            if (cNameExist > 0)
                                num++;
                        }
                    } 

                    if (cNameExist < 0 && rNameExist < 0) {
                        var tmpColumn = stateName(textArray[0][i], stateAbb, state);
                        var tmpRow = stateName(textArray[1][j], stateAbb, state);

                        if (tmpColumn != 0 && tmpRow != 0) {
                            cNameExist = parseInt(resText.indexOf(tmpColumn));
                            rNameExist = parseInt(resText.indexOf(tmpRow));

                            if (cNameExist > 0 && rNameExist > 0)
                                num++;                        
                        }  
                    }
                }
                
                docNumArray[j][i] = num;
                num = 0;                 
            }
        }

        // loop rows
        bic.grid = minegraph.graph.set();
        for( r = 0; r < gridY; r+=1) {
            //loop cols
            for( c = 0; c < gridX; c+=1) {
                dx = x + margin + c * cellW + 1;
                dy = y + margin + r * cellH + 1;
                var rectangle = minegraph.graph.rect(dx, dy, cellW-cellSpacing, cellH-cellSpacing, 2);

                if (docNumArray[r][c] >= 6)
                    minegraph.core.colors.orange_2_80 = color_level7;

                if (docNumArray[r][c] >= 6 && docNumArray[r][c] < 6)
                    minegraph.core.colors.orange_2_80 = color_level6;            

                if (docNumArray[r][c] >= 4 && docNumArray[r][c] < 5)
                    minegraph.core.colors.orange_2_80 = color_level5;

                else if (docNumArray[r][c] >= 3 && docNumArray[r][c] < 4)
                    minegraph.core.colors.orange_2_80 = color_level4;            

                else if (docNumArray[r][c] >= 2 && docNumArray[r][c] < 3)
                    minegraph.core.colors.orange_2_80 = color_level3;                          

                else if (docNumArray[r][c] >= 1 && docNumArray[r][c] < 2)
                    minegraph.core.colors.orange_2_80 = color_level2;

                else if (docNumArray[r][c] >= 0 && docNumArray[r][c] < 1)
                    minegraph.core.colors.orange_2_80 = color_level1;

                rectangle.attr({
                    fill: minegraph.core.colors.orange_2_80,
                    'stroke-width' : 2,
                    'stroke-opacity': 0,
                    cursor: "pointer"
                });

                if (c == 0 && r == 0)
                    startID = rectangle.id;

                //add pop up description?
                bic.grid.push(rectangle);
            //bic.push(rectangle);
            }
        }
    }

    /*
    * color grid for thin biclusters
    */
    if (jsonBic.type == "thinBic") {

       // loop rows
        bic.grid = minegraph.graph.set();
        for( r = 0; r < gridY; r+=1) {
            //loop cols
            for( c = 0; c < gridX; c+=1) {
                dx = x + margin + c * cellW;
                dy = y + margin + r * cellH;
                var rectangle = minegraph.graph.rect(dx, dy, cellW-cellSpacing, cellH-cellSpacing, 2);

                // thinBic is generated by row name
                if(jsonBic.flag_row_or_col == 0)
                    minegraph.core.colors.orange_2_80 = color_thinBic_by_row;

                // thinBic is generated by col name
                if(jsonBic.flag_row_or_col == 1)
                    minegraph.core.colors.orange_2_80 = color_thinBic_by_col;                    

                rectangle.attr({
                    fill: minegraph.core.colors.orange_2_80,
                    'stroke-width' : 2,
                    'stroke-opacity': 0,
                    cursor: "pointer"
                });

                if (c == 0 && r == 0)
                    startID = rectangle.id;

                //add pop up description?
                bic.grid.push(rectangle);
            //bic.push(rectangle);
            }
        }
    }

    if (jsonBic.type == "metaBic") {

       // loop rows
        bic.grid = minegraph.graph.set();
        for( r = 0; r < gridY; r+=1) {
            //loop cols
            for( c = 0; c < gridX; c+=1) {
                dx = x + margin + c * cellW;
                dy = y + margin + r * cellH;
                var rectangle = minegraph.graph.rect(dx, dy, cellW-cellSpacing, cellH-cellSpacing, 2);

                minegraph.core.colors.orange_2_80 = color_metaBic;                  

                rectangle.attr({
                    fill: minegraph.core.colors.orange_2_80,
                    'stroke-width' : 2,
                    'stroke-opacity': 0,
                    cursor: "pointer"
                });

                if (c == 0 && r == 0)
                    startID = rectangle.id;

                //add pop up description?
                bic.grid.push(rectangle);
            //bic.push(rectangle);
            }
        }
    }
 

    // group them into a set
    bic.push(
        bic.frame,
        bic.ylabels,
        bic.xlabels,

        bic.idTag,

        bic.grid
    );

   
    // adding hover event for common bicluster
    if (jsonBic.type == "bicluster" || jsonBic.type == "thinBic" ) {
        // mouse move in: changing color
        var over = function() {
            this.color = this.color || this.attr("fill");
            this.stop().animate({fill: "#5555FF"}, 350);              
        }

        // mouse move out: change back to original color
        var out = function() {
            this.stop().animate({fill: this.color}, 350);       
        }

        // adding mouse event
        bic.grid.hover(over, out);        
    }    

    // an array to flag whether the document is highlight
    var selectedDoc = new Array();
    for (var i = 0; i < responseArray.length; i++) {
        selectedDoc[i] = 0;
    }

    /*
    * mouse down event for grid, coloring the grid and related documentss
    */
    // var mousedownflag = false;
    // if (jsonBic.type == "thinBic") {
    //     bic.grid.mousedown(function(){
    //         // index for colors
    //         colorIndex = this.id - startID;

    //         if (this.attr("fill") != color_grid_selected) {
    //             colorArray[colorIndex] = this.attr("fill");       
    //             this.animate({fill: color_grid_selected}, 300); 

    //             // // calculate the index 
    //             // var cNameIndex = (this.id - startID) % gridX;
    //             // var rNameIndex = ((this.id - startID) - cNameIndex) / gridX;

    //             // var preText, curText;

    //             // for (var k = 0; k < responseArray.length; k++) {
                
    //             //     // get file content
    //             //     resText = responseArray[k]['text'];

    //             //     // check wether the colum name and row name are in the documents
    //             //     cNameExist = parseInt(resText.indexOf(textArray[0][cNameIndex]));
    //             //     rNameExist = parseInt(resText.indexOf(textArray[1][rNameIndex]));

    //             //     var tmpColumn = stateName(textArray[0][cNameIndex], stateAbb, state);
    //             //     var tmpRow = stateName(textArray[1][rNameIndex], stateAbb, state);

    //             //     if (tmpColumn != 0)
    //             //         var tmpColumnExist = parseInt(resText.indexOf(tmpColumn));

    //             //     if (tmpRow != 0)
    //             //         var tmpRowExist = parseInt(resText.indexOf(tmpRow));


    //             //     var tmpDocName = "";

    //             //     if (cNameExist > 0 && rNameExist > 0 
    //             //         || cNameExist < 0 && tmpColumnExist > 0 && rNameExist >0
    //             //         || cNameExist > 0 && rNameExist < 0 && tmpRowExist >0
    //             //         || cNameExist < 0 && rNameExist < 0 && tmpColumnExist > 0 && tmpRowExist > 0) {

    //             //         if (minegraph.findDocument(responseArray[k].id) != null) {    

    //             //             for (var i = 0; i < minegraph.core.documents.length; i++) {
    //             //                 if (minegraph.core.documents[i].id == responseArray[k].id) {

    //             //                     var curDoc = minegraph.core.documents[i];

    //             //                     // flag the displayed document has been selected
    //             //                     selectedDoc[k]++;

    //             //                     curDoc.content.attr({
    //             //                         'fill': 'yellow'
    //             //                     });    
    //             //                 }                       
    //             //             }
    //             //         }                     
    //             //         else {
    //             //             // flag the displayed document has been selected
    //             //             selectedDoc[k]++;                        
    //             //             tmpDocName += responseArray[k].id + " ";
    //             //             alert("Document " + tmpDocName + "is not in the workspace.");                        
    //             //         }
    //             //     }             
    //             // }
    //         }
    //         else {
    //             this.animate({fill: colorArray[colorIndex]}, 300);

    //             // // calculate the index 
    //             // var cNameIndex = (this.id - startID) % gridX;
    //             // var rNameIndex = ((this.id - startID) - cNameIndex) / gridX;

    //             // var preText, curText;

    //             // for (var k = 0; k < responseArray.length; k++) {
                
    //             //     // get file content
    //             //     resText = responseArray[k]['text'];
    //             //     cNameExist = parseInt(resText.indexOf(textArray[0][cNameIndex]));
    //             //     rNameExist = parseInt(resText.indexOf(textArray[1][rNameIndex]));


    //             //     var tmpColumn = stateName(textArray[0][cNameIndex], stateAbb, state);
    //             //     var tmpRow = stateName(textArray[1][rNameIndex], stateAbb, state);

    //             //     if (tmpColumn != 0)
    //             //         var tmpColumnExist = parseInt(resText.indexOf(tmpColumn));

    //             //     if (tmpRow != 0)
    //             //         var tmpRowExist = parseInt(resText.indexOf(tmpRow));



    //             //     // both items are in the file
    //             //     if ((cNameExist > 0 && rNameExist > 0
    //             //         || cNameExist < 0 && tmpColumnExist > 0 && rNameExist >0
    //             //         || cNameExist > 0 && rNameExist < 0 && tmpRowExist >0
    //             //         || cNameExist < 0 && rNameExist < 0 && tmpColumnExist > 0 && tmpRowExist > 0) 
    //             //         && minegraph.findDocument(responseArray[k].id) != null) {
                        
    //             //         preText = resText;
                        
    //             //         // replace the find string
    //             //         curText = resText.replace("<h3>" + textArray[0][cNameIndex] + "</h3>", 
    //             //             textArray[0][cNameIndex]);
    //             //         curText = curText.replace("<h3>" + textArray[1][rNameIndex] + "</h3>", 
    //             //             textArray[1][rNameIndex]);

    //             //         // console.log("here");

    //             //         for (var i = 0; i < minegraph.core.documents.length; i++) {
    //             //             if (minegraph.core.documents[i].id == responseArray[k].id) {
    //             //                 if (selectedDoc[k] == 1)
    //             //                     minegraph.core.documents[i].content.attr({'fill': 'white'});
    //             //                 selectedDoc[k]--;
    //             //             }
    //             //         }
    //             //     }  
    //             // }
    //         }
    //     }); 
    // }      

    // push bic to doc list
    minegraph.core.biclusters.push(bic);

    // console.log(minegraph.core.biclusters);

    // make sure bic isn't added ouside of graph.
    minegraph.clipSet(bic);

    //set events
    if (jsonBic.type == "bicluster") {
        minegraph.set_bic_context_menu(bic);  
        minegraph.set_grid_context_menu(bic);              
    }

    if (jsonBic.type == "thinBic") {
        minegraph.set_thinBic_context_menu(bic);
        minegraph.set_thinBic_grid_context_menu(bic);        
    }

    if (jsonBic.type == "metaBic") {
        minegraph.set_thinBic_context_menu(bic);        
    }
    
    minegraph.setdrag(bic);
    return bic;
};

/**
 * Takes a set and clips it to inside the boundaries of the workspace.
 *
 * @param set Object Raphael Set to clip.
 */
minegraph.clipSet = function(set) {
    var bb = set.getBBox();
    var tx = 0;
    var ty = 0;

    /*
     * Vertical Clipping
     */
    if (bb.x+bb.width > minegraph.width) {
        tx = minegraph.width - bb.x - bb.width;
    //console.log('should move left by:' + tx);
    }

    /*
     * Horizontal Clipping
     */
    if (bb.y + bb.height > minegraph.height) {
        ty = minegraph.height - bb.y - bb.height;
    //console.log('should move up by:' + ty);
    }

    /*
     * Only clip if there's actualy something to do
     */
    if (tx != 0 || ty != 0) {
        set.transform("...T"+tx+","+ty);
    }
};


/**
 * Remove Document
 * @param document Object Document to remove
 * @return Object removed document
 */
minegraph.removeDocument = function(document) {

    // logging this interaction
    console.log("======================================================"); 
    var timeStamp = getCurrentTimeStamp();   
    console.log(timeStamp + ', REMOVE_DOC, DOC_' + document.id + '\n');

    var index = minegraph.core.documents.indexOf(document);
    minegraph.core.documents.splice(index,1);
    document.remove();
    var links_to_remove = [];
    for(var i=0; i<minegraph.core.links.length; i++) {
        if (minegraph.core.links[i].typeid1 == 'document'+document.id
            || minegraph.core.links[i].typeid2 == 'document'+document.id) {
            links_to_remove.push(minegraph.core.links[i]);
        }
    }
    for (i=0;i<links_to_remove.length;i++) {
        minegraph.removeLink(links_to_remove[i]);
    }

    // log this interaction
    console.log("   SUCCESSFULLY REMOVED");
    
    return document;
};

/**
 * Remove Bicluster
 * @param bicluster Object Bicluster to remove
 * @return Object removed Bicluster
 */
minegraph.removeBicluster = function(bicluster) {

    // logging this interaction
    console.log("======================================================"); 
    var timeStamp = getCurrentTimeStamp();   
    console.log(timeStamp + ', REMOVE_BIC, BIC_' + bicluster.id + '\n');

    var index = minegraph.core.biclusters.indexOf(bicluster);
    minegraph.core.biclusters.splice(index,1);
    bicluster.remove();
    var links_to_remove = [];
    for(var i=0; i<minegraph.core.links.length; i++) {
        if (minegraph.core.links[i].typeid1 == 'bicluster'+bicluster.id
            || minegraph.core.links[i].typeid2 == 'bicluster'+bicluster.id) {
            links_to_remove.push(minegraph.core.links[i]);
        }
    }
    for (i=0;i<links_to_remove.length;i++) {
        minegraph.removeLink(links_to_remove[i]);
    }

    console.log("   SUCCESSFULLY REMOVED");

    return document;
};


/**
 * Removes a link from the link array and the graph
 * @param link to be removed
 * @return removed link
 */
minegraph.removeLink = function (link) {
    //remove link from links list
    var index = minegraph.core.links.indexOf(link)
    minegraph.core.links.splice(index, 1);
    //remove path from graph
    link.bg.remove();
    return link;
};

/**
 * Trick to line wrap text.
 * http://stackoverflow.com/questions/3142007/how-to-either-determine-svg-text-box-width-or-force-line-breaks-after-x-chara
 *
 * @param element raphael text element to linewrap text for
 * @param text string we want to line wrap
 * @param width max width to line wrap to.
 */
minegraph.linewraptext = function(element, text, width) {
    //split text per word
    var words = text.split(" ");
    //line wrapped text
    var tempText = "";

    for (var i=0; i<words.length; i++) {
        element.attr("text", tempText + " " + words[i]);
        if (element.getBBox().width > width) {
            //alert("Wrapping: " + words[i]);
            //it goes over the width, start new line
            tempText += "\n" + words[i];
            //compensate offset by moving down (since the vertical anchor is center)
            // there is no vertical alignment in version 2.0
            element.translate(0,6);
        }
        else {
            //otherwise add it to current line
            tempText += " " + words[i];
        }
    }
    tempText += "\n";
    return tempText;
};

/**
 * Takes a Set Element and makes it and all it's components draggable.
 * Handy function!
 *
 * @param set set element we want to add draging to.
 */
minegraph.setdrag = function(set) {
    set.drag(
        //Move function
        function (dx, dy, x, y, e) {
            if (e.which === 1) {
                var bb = set.getBBox();
                //Do an ABSOLUTE transform, otherwise it craps out on rotated elements and
                // moves them in random directions...
                //var t = set.frame.transform();

                //set the new transformation
                var tx = set.oBB.x - bb.x + dx;
                var ty = set.oBB.y - bb.y + dy;
                //update by flattening with old tranformation if present
                //                if (t.length > 0) {
                //                    console.log('transformation');
                //                    console.log(t[0][1]);
                //                    console.log(t[0][2]);
                //                    tx += t[0][1];
                //                    ty += t[0][2];
                //                }
                //Clip tx and ty by graph size:
                if (bb.x + tx < 0) {
                    tx += -1 * (bb.x + tx); //clip to left boundary
                } else if (bb.x + bb.width + tx  > minegraph.width) {
                    tx += (minegraph.width - bb.x - bb.width - tx); //clip to right boundary
                }
                if (bb.y + ty < 0) {
                    ty += -1 * (bb.y + ty); //clip to top boundary
                } else if (bb.y + bb.height + ty  > minegraph.height) {
                    ty += (minegraph.height - bb.y - bb.height - ty); //clip to bottom boundary
                }

                set.transform("...T"+tx+","+ty);
                //Update links...
                minegraph.update_links();
            }
        },
        //Start function
        function () {
            set.toFront();
            //set origin for this move
            set.oBB = set.getBBox();
            //Highlight stroke
            set.animate({
                "stroke": minegraph.core.colors.orange
            }, 250);
        },
        //End function
        function () {
            //remove highlight stroke
            set.animate({
                "stroke": minegraph.core.colors.black
            }, 250);
        });
};

/**
 * Smart path.
 *
 * This takes the bounding box of 2 objects and does a smart bezier curve between
 * them. By smart it will pick the best anchor side and direction depending on their
 * relative positions.
 *
 * @param obj1 first object
 * @param obj2 second object
 *
 * @return string svg path
 */
minegraph.getSmartPath = function(obj1, obj2) {
    // set up varibales
    var bb1 = obj1.getBBox(),
    bb2 = obj2.getBBox(),
    //This is the set of the position of the side lines of both rectangles
    // 0 - bottom
    // 1 - top
    // 2 - left
    // 3 - right
    p = [{
        x: bb1.x + bb1.width / 2,
        y: bb1.y - 1
    },

    {
        x: bb1.x + bb1.width / 2,
        y: bb1.y + bb1.height + 1
    },

    {
        x: bb1.x - 1,
        y: bb1.y + bb1.height / 2
    },

    {
        x: bb1.x + bb1.width + 1,
        y: bb1.y + bb1.height / 2
    },
    // box 2
    {
        x: bb2.x + bb2.width / 2,
        y: bb2.y - 1
    },
    {
        x: bb2.x + bb2.width / 2,
        y: bb2.y + bb2.height + 1
    },
    {
        x: bb2.x - 1,
        y: bb2.y + bb2.height / 2
    },
    {
        x: bb2.x + bb2.width + 1,
        y: bb2.y + bb2.height / 2
    }],
    //This object is an array of arrays, each array will specify a set of sides in p [0-3,4-7] to know what side to anchor the path too.
    d = {},
    // ??
    dis = [];

    //loop throught the sides of the first box
    for (var i = 0; i < 4; i++) {
        //loop through the sides of the second box
        for (var j = 4; j < 8; j++) {
            //get the absolute delta between the lines.
            var dx = Math.abs(p[i].x - p[j].x),
            dy = Math.abs(p[i].y - p[j].y);
            // what the ???
            // go figure what this does...
            if ((i == j - 4) || (((i != 3 && j != 6) || p[i].x < p[j].x) && ((i != 2 && j != 7) || p[i].x > p[j].x) && ((i != 0 && j != 5) || p[i].y > p[j].y) && ((i != 1 && j != 4) || p[i].y < p[j].y))) {
                dis.push(dx + dy);
                d[dis[dis.length - 1]] = [i, j];
            }
        }
    }
    //figure out which 2 sides to anchor path from.
    if (dis.length == 0) {
        //default to top top because that makes no sense?
        var res = [0, 4];
    } else if (!(obj1.direction === undefined) && !(obj2.direction === undefined)) {
        //console.log("detected cell to cell link with set direction!");
        if (obj1.direction == "down" && obj2.direction == "down") {
            res = [1, 5];
        } else if (obj1.direction == "right" && obj2.direction == "down") {
            res = [3, 5];
        } else if (obj1.direction == "down" && obj2.direction == "right") {
            res = [1, 7];
        } else if (obj1.direction == "right" && obj2.direction == "right") {
            res = [3, 7];
        } else {
            res = [0, 4]; // Default fallback
        }
    } else {
        /**
         * Smart thingy here???
         */
        //this some how picks what sides to go from?
        //res = [0, 5];
        res = d[Math.min.apply(Math, dis)];
    }
    // Set Curve Origin
    var x1 = p[res[0]].x,
    y1 = p[res[0]].y,
    // Destination point
    x4 = p[res[1]].x,
    y4 = p[res[1]].y;
    // get max of 20 or half the difference in the distance between the 2 points
    // this will be the offests for the control points
    dx = Math.max(Math.abs(x1 - x4) / 2, 20);
    dy = Math.max(Math.abs(y1 - y4) / 2, 20);
    // set curve control point #1 depending on the side we're on
    var x2 = [x1, x1, x1 - dx, x1 + dx][res[0]].toFixed(3),
    y2 = [y1 - dy, y1 + dy, y1, y1][res[0]].toFixed(3),
    // set curve control point #2
    x3 = [0, 0, 0, 0, x4, x4, x4 - dx, x4 + dx][res[1]].toFixed(3),
    y3 = [0, 0, 0, 0, y1 + dy, y1 - dy, y4, y4][res[1]].toFixed(3);

    //set up bezier curve and return it
    return ["M", x1.toFixed(3), y1.toFixed(3), "C", x2, y2, x3, y3, x4.toFixed(3), y4.toFixed(3)].join(",");
};

/**
 * Dynamic Linking
 * Takes one object store it, once it has 2 create a link... then start over
 * @param obj linking object
 */
minegraph.linking = function(obj) {
    /*
     * We got the first object
     */
    if (this.core.linking_obj1 == null) {
        console.debug("Linking: got 1st object");
        this.core.linking_obj1 = obj
    } else {
        /**
         * We got the second object
         */
        console.debug("Linking: got 2nd object");
        var obj1 = this.core.linking_obj1;
        //Reset for next link
        this.core.linking_obj1 = null;

        if (obj1 != obj) {
            this.link(obj1, obj, true);
            console.debug("Linking: created new link!");
        } else {
            minegraph.alert("Can't link an item to itself!");
        }
    }
};

/**
 * Creates a visual link between 2 objects
 * either bic or doc
 * @param obj1 object first object to link
 * @param obj2 object second object to link
 * @param userlink boolean wether it's a user created link or system link
 * @return created link
 */
minegraph.link = function(obj1, obj2, userlink) {
    if (userlink == null) {
        userlink = false;
    }
    var link;
    var type;
    var cell_a;
    var cell_b;
    var link_count = 0;

    // check if a link already exists
    if (this.findLinks(obj1, obj2).length > 0) {
        minegraph.alert("There is already a link between those 2 objects");
        return null;
    }

    // figure out link type
    if (obj1.type == 'document' && obj2.type == 'document') {
        type = "doc2doc";
    } else if (obj1.type == 'document' && obj2.type == 'bicluster') {
        type = "doc2bic";
    } else if (obj1.type == 'bicluster' && obj2.type == 'document') {
        type = "bic2doc";
    }else if (obj1.type == 'bicluster' && obj2.type == 'bicluster') {
        type = "bic2bic";
    }
    // console.log("new link of type " + type);
    //console.log(obj1);
    //console.log(obj2);

    /*
     * Bic to bic special stuff
     */
    if (type == 'bic2bic') {
        if(obj1.source_data.row_type == obj2.source_data.row_type) {
            //console.log('    row row match');
            //Loop through all of obj's entities
            for( ent_a in obj1.source_data.rows ) {
                ent_a = ent_a*1;
                //Loop through all of obj 2's entities
                for (ent_b in obj2.source_data.rows ) {
                    //If it matches create a link between those cells
                    ent_b = ent_b*1;
                    if (obj1.source_data.rows[ent_a].name == obj2.source_data.rows[ent_b].name) {
                        // console.log("    Match: " + obj1.source_data.rows[ent_a].name);
                        /* create links from cell ent_a to cell ent_b:
                           1 take the cell in the last col of row ent_a
                           2 link it to the last cell in col of row ent_b
                           convertion to linear row id * col count - 1 */
                        cell_a = obj1.grid[(ent_a+1) * obj1.source_data.grid[0].length - 1];
                        cell_b = obj2.grid[(ent_b+1) * obj2.source_data.grid[0].length - 1];
                        link = this.create_line(cell_a, cell_b, type, obj1.type+obj1.id, obj2.type+obj2.id);
                        // now override default smart path with custom direction based on row/col:
                        link.from.direction = "right";
                        link.to.direction = "right";
                        // mark that this is a cell link and to which cell
                        link.cell = obj1.source_data.rows[ent_a].name;
                        this.update_link(link);
                        link_count++;
                    }
                }
            }
        } else if(obj1.source_data.col_type == obj2.source_data.col_type) {
            //console.log('   col col match');
            //Loop through all of obj's entities
            for( ent_a in obj1.source_data.cols ) {
                ent_a = ent_a*1;
                //Loop through all of obj 2's entities
                for (ent_b in obj2.source_data.cols ) {
                    //If it matches create a link between those cells
                    ent_b = ent_b*1;
                    if (obj1.source_data.cols[ent_a].name == obj2.source_data.cols[ent_b].name) {
                        // console.log("    Match: " + obj1.source_data.cols[ent_a].name);
                        cell_a = obj1.grid[(ent_a) + obj1.source_data.grid[0].length * (obj1.source_data.grid.length-1)];
                        cell_b = obj2.grid[(ent_b) + obj2.source_data.grid[0].length * (obj2.source_data.grid.length-1)];
                        link = this.create_line(cell_a, cell_b, type, obj1.type+obj1.id, obj2.type+obj2.id);
                        // now override default smart path with custom direction based on row/col:
                        link.from.direction = "down";
                        link.to.direction = "down";
                        // mark that this is a cell link and to which cell
                        link.cell = obj1.source_data.cols[ent_a].name;
                        this.update_link(link);
                        link_count++;
                    }
                }
            }
        } else if(obj1.source_data.col_type == obj2.source_data.row_type) {
            //Loop through all of obj's entities
            for( ent_a in obj1.source_data.cols ) {
                ent_a = ent_a*1;
                //Loop through all of obj 2's entities
                for (ent_b in obj2.source_data.rows ) {
                    //If it matches create a link between those cells
                    ent_b = ent_b*1;
                    if (obj1.source_data.cols[ent_a].name == obj2.source_data.rows[ent_b].name) {
                        // console.log("    Match: " + obj1.source_data.cols[ent_a].name);
                        cell_a = obj1.grid[(ent_a) + obj1.source_data.grid[0].length * (obj1.source_data.grid.length-1)];
                        cell_b = obj2.grid[(ent_b+1) * obj2.source_data.grid[0].length - 1];
                        link = this.create_line(cell_a, cell_b, type, obj1.type+obj1.id, obj2.type+obj2.id);
                        // now override default smart path with custom direction based on row/col:
                        link.from.direction = "down";
                        link.to.direction = "right";
                        // mark that this is a cell link and to which cell
                        link.cell = obj1.source_data.cols[ent_a].name;
                        this.update_link(link);
                        link_count++;
                    }
                }
            }
        } else if(obj1.source_data.row_type == obj2.source_data.col_type) {
            //console.log('    row col match');
            //Loop through all of obj's entities
            for( ent_a in obj1.source_data.rows ) {
                ent_a = ent_a*1;
                //Loop through all of obj 2's entities
                for (ent_b in obj2.source_data.cols ) {
                    //If it matches create a link between those cells
                    ent_b = ent_b*1;
                    if (obj1.source_data.rows[ent_a].name == obj2.source_data.cols[ent_b].name) {
                        // console.log("    Match: " + obj1.source_data.rows[ent_a].name);
                        cell_a = obj1.grid[(ent_a+1) * obj1.source_data.grid[0].length - 1];
                        cell_b = obj2.grid[(ent_b) + obj2.source_data.grid[0].length * (obj2.source_data.grid.length-1)];
                        link = this.create_line(cell_a, cell_b, type, obj1.type+obj1.id, obj2.type+obj2.id);
                        // now override default smart path with custom direction based on row/col:
                        link.from.direction = "right";
                        link.to.direction = "down";
                        // mark that this is a cell link and to which cell
                        link.cell = obj1.source_data.rows[ent_a].name;
                        this.update_link(link);
                        link_count++;
                    }
                }
            }
        } else {
            // console.log('    no type match, creating simple link');
        }
    }
    /**
     * Else just do regular single link
     */
    if (link_count == 0) {
        link = this.create_line(obj1, obj2, type, obj1.type+obj1.id, obj2.type+obj2.id, userlink);
    }

    return link;
};

/**
 * private helper create line between 2 objects
 * also set the object 1 and 2 typeids
 * a type id is a combination of the type and the id.
 * This data is later used to find links between 2 elements...
 *
 * @param obj1 Object link start object
 * @param obj2 Object link end object
 * @param type string link type
 * @param typeid1 string type of object 1
 * @param typeid2 string type of object 2
 * @param userlink boolean if true make the link a different color
 * @return Object the link
 */
minegraph.create_line = function(obj1, obj2, type, typeid1, typeid2, userlink) {
    if (userlink == null) {
        userlink = false;
    }
    var path, frame1, frame2;
    // if it's a bic cluster select the frame without the external labels
    if (obj1.type == "set" || obj1.type == "bicluster") {
        frame1 = obj1.frame;
    } else {
        frame1 = obj1;
    }
    if (obj2.type == "set" || obj2.type == "bicluster") {
        frame2 = obj2.frame;
    } else {
        frame2 = obj2;
    }
    path = this.getSmartPath(frame1, frame2);
    //console.debug("new link path " + path);
    // create link
    var link = {
        bg: this.graph.path(path).attr({
            stroke: "rgba(255,255,255,.1)",
            fill: "none",
            "stroke-width": 4
        }),
        from: frame1,
        to: frame2,
        cell: false,
        type: type,
        userlink: userlink
    };
    if (userlink) {
        link.bg.attr('stroke', minegraph.core.colors.blue_10);
    }
    link.typeid1 = typeid1;
    link.typeid2 = typeid2;
    // do link events stuff
    this.set_link_context_menu(link)
    this.core.links.push(link);
    link.bg.toBack();
    return link;
};

/**
 * Sets up a call back function for an event
 * @param event_name string name of the event to set callback for
 * @param callback function function to call when even is triggered
 */
minegraph.event = function(event_name, callback) {
    minegraph.core.events[event_name].push(callback);
};

/**
 * Update all links function...
 */
minegraph.update_links = function() {
    var link;
    var path;
    /**
     * Update connections
     */
    for (var i = minegraph.core.links.length; i--;) {
        link = minegraph.core.links[i];
        //make sure link doesn't have removed elements
        //get new path
        path = minegraph.getSmartPath(link.from, link.to);
        //update path
        link.bg && link.bg.attr({
            path: path
        });
    }

    this.graph.safari();
};

/**
 * Update a single link
 * @param link Object link to update
 */
minegraph.update_link = function(link) {
    //make sure link doesn't have removed elements
    if (link.to.removed || link.from.removed) {
        this.removeLink(link);
    } else {
        //get new path
        path = this.getSmartPath(link.from, link.to);
        //update path
        link.bg && link.bg.attr({
            path: path
        });
    }
    this.graph.safari();
};

/**
 * Adds the document context menu to a set.
 *
 * @param set element set to add context menu to.
 */
minegraph.set_doc_context_menu = function(set) {
    set.forEach( function(element) {
        $(element.node).contextMenu({
            menu: 'docMenu'
        },
        function(action, el, pos) {
            if (action == 'close') {
                /**
                 * Close Document Action
                 */
                minegraph.removeDocument(set);
            } else if (action == 'link') {
                /**
                 * Start linking process...
                 */
                minegraph.linking(set);
            } else if (action == 'bics') {
                /**
                 * Show Biclusters
                 * Use event callback
                 */
                for( var i = 0; i < minegraph.core.events.doc_show_bics.length; i++) {
                    minegraph.core.events.doc_show_bics[i](set);
                }
            } else  {
                /**
                 * Unknown Action
                 */
                console.log("Doc Menu: action " + action);
            }
        });
    },1);
};

/**
 * Adds the bicluster context menu to a set.
 *
 * @param set element set to add context menu to.
 */
minegraph.set_bic_context_menu = function(set) {
    /**
     * Do it for the set
     */
    set.forEach( function(element) {
        $(element.node).contextMenu({
            menu: 'bicMenu'
        },
        function(action, el, pos) {
            if (action == 'close') {
                /**
                 * Close BiCluster Action
                 */
                minegraph.removeBicluster(set);
            } else if (action == 'link') {
                /**
                 * Start linking process...
                 */
                minegraph.linking(set);
            } else if (action == 'docs') {
                /**
                 * Show Documents for this bicluster
                 * Use event callback
                 */
                for( var i = 0; i < minegraph.core.events.bic_show_docs.length; i++) {
                    minegraph.core.events.bic_show_docs[i](set);
                }
            } else if (action == 'links') {
                /**
                 * Show Biclusters linked to this bicluster
                 * Use event callback
                 */
                for( i = 0; i < minegraph.core.events.bic_show_bics.length; i++) {
                    minegraph.core.events.bic_show_bics[i](set);
                }
            } else {
                /**
                 * Unknown Action
                 */
                console.debug("Bic Menu: action " + action);
            }
        });
    },1);

    /**
     * It's stupid but let's do it for the grid too...
     */
    // set.grid.forEach( function(element) {
    //     $(element.node).contextMenu({
    //         menu: 'bicMenu'
    //     },
    //     function(action, el, pos) {
    //         if (action == 'close') {
    //             /**
    //              * Close BiCluster Action
    //              */
    //             minegraph.removeBicluster(set);
    //         } else if (action == 'link') {
    //             /**
    //              * Start linking process...
    //              */
    //             minegraph.linking(set);
    //         } else if (action == 'docs') {
    //             /**
    //              * Show Documents for this bicluster
    //              * Use event callback
    //              */
    //             for( var i = 0; i < minegraph.core.events.bic_show_docs.length; i++) {
    //                 minegraph.core.events.bic_show_docs[i](set);
    //             }
    //         } else if (action == 'links') {
    //             /**
    //              * Show Biclusters linked to this bicluster
    //              * Use event callback
    //              */
    //             for( i = 0; i < minegraph.core.events.bic_show_bics.length; i++) {
    //                 minegraph.core.events.bic_show_bics[i](set);
    //             }
    //         } else  {
    //             /**
    //              * Unknown Action
    //              */
    //             console.debug("Bic Menu: action " + action);
    //         }
    //     });
    // },1);
};


/**
 * Adds the thin bicluster context menu to a set.
 *
 * @param set element set to add context menu to.
 */
minegraph.set_thinBic_context_menu = function(set) {

    /**
     * Do it for the set
     */
    set.forEach( function(element) {
        $(element.node).contextMenu({
            menu: 'thinBicMenu'
        },
        function(action, el, pos) {
            if (action == 'close') {
                /**
                 * Close BiCluster Action
                 */
                minegraph.removeBicluster(set);
            } else if (action == 'link') {
                /**
                 * Start linking process...
                 */
                minegraph.linking(set);
            } else {
                /**
                 * Unknown Action
                 */
                console.debug("ThinBic Menu: action " + action);
            }
        });
    },1);

}


/**
 * Adds the grid context menu to a set.
 *
 * @param set element set to add context menu to.
 */
minegraph.set_grid_context_menu = function(set) {
    /**
     * Do it for the set
     */
    set.grid.forEach( function(element) {
        $(element.node).contextMenu({
            menu: 'gridMenu'
        },
        function(action, el, pos) {

            // total number of columns 
            var cElementNum = set.xlabels.length;
            // row position of the selected grid
            var rPos = parseInt((element.id - set.grid[0].id) / cElementNum);
            // column position of the selected grid
            var cPos = (element.id - set.grid[0].id) % cElementNum;

            var cName = $(set.xlabels[cPos].node).text();   // get column name
            var rName = $(set.ylabels[rPos].node).text();   // get row name

            if (action == 'thinBicByRow') {
                /**
                * Show thin bicluster by row name
                */

                for( i = 0; i < minegraph.core.events.bic_show_thin_bics.length; i++) {
                    minegraph.core.events.bic_show_thin_bics[i](set, rName, cName, rPos, cPos, 0);
                }

            } else if (action == 'thinBicByCol') {
                /**
                 * Show thin bicluster by col name
                 */
                for( i = 0; i < minegraph.core.events.bic_show_thin_bics.length; i++) {
                    minegraph.core.events.bic_show_thin_bics[i](set, rName, cName, rPos, cPos, 1);
                }

            } else {
                /**
                 * Unknown Action
                 */
                console.debug("Bic Menu: action " + action);
            }
        });
    },1);
}



minegraph.set_thinBic_grid_context_menu = function(set) {
    /**
     * Do it for the set
     */
    set.grid.forEach( function(element) {
        $(element.node).contextMenu({
            menu: 'thinBicGridMenu'
        },
        function(action, el, pos) {

            // total number of columns 
            var cElementNum = set.xlabels.length;
            // row position of the selected grid
            var rPos = parseInt((element.id - set.grid[0].id) / cElementNum);
            // column position of the selected grid
            var cPos = (element.id - set.grid[0].id) % cElementNum;

            var cName = $(set.xlabels[cPos].node).text();   // get column name
            var rName = $(set.ylabels[rPos].node).text();   // get row name 

            if (action == 'showMetaBic') {
                /**
                * Show thin bicluster by row name
                */

                for( i = 0; i < minegraph.core.events.thinBic_show_metaBic.length; i++) {
                    minegraph.core.events.thinBic_show_metaBic[i](set, rName, cName, rPos, cPos);
                }

            } else {
                /**
                 * Unknown Action
                 */
                console.debug("Bic Menu: action " + action);
            }

        });
    },1);            
}




/**
 * Highlight or unhighlight a link depending on it's current state
 *
 * @param link Object link
 */
minegraph.toggle_link_highlight = function(link) {
    if (minegraph.is_link_highlighted(link)) {
        link.bg.attr('stroke-width', 4);
        if (link.userlink) {
            link.bg.attr('stroke', minegraph.core.colors.blue);
        } else {
            link.bg.attr('stroke', minegraph.core.colors.white);
        }
    } else {
        link.bg.attr('stroke-width', 8);
        if (!link.userlink) {
            link.bg.attr('stroke', minegraph.core.colors.green);
        }
    }
};

/**
 * Checks if a link is highlighted
 * @param link Object link to check
 * @return true if highlighted false otherwise
 */
minegraph.is_link_highlighted = function(link) {
    if (link.bg.attr('stroke-width') == 8) {
        return true;
    } else {
        return false;
    }
}

/**
 * Adds the bicluster context menu to a link.
 *
 * @param line path to add context menu to.
 */
minegraph.set_link_context_menu = function(line) {
    line.bg.hover(function() {
        line.bg.animate({
            "stroke-opacity": .8
        }, 100);
    }, function() {
        line.bg.animate({
            "stroke-opacity": .1
        }, 100);
    });
    $(line.bg.node).contextMenu({
        menu: 'linkMenu'
    },
    function(action, el, pos) {
        if (action == 'close') {
            /**
             * Close Link Action
             */
            minegraph.removeLink(line);
        }else if (action == 'up') {
            /**
             * Change the thickness and oppacity of the link...
             */
            console.log("Link Menu: Highlight Link");
            //console.debug(line.bg.attr('stroke'));
            minegraph.toggle_link_highlight(line);
        } else  {
            /**
             * Unknown Action
             */
            console.log("Link Menu: unknown action " + action);
        }
    });
};


/**
 * This replaces alert with a more multiscreen friendly and notification like
 * popup that fades away.
 * @param message string what the message will be
 * @param title string optional title
 * @param timeout int time the message will stay on the screen
 */
minegraph.alert = function(message, title, timeout) {
    if (title == null) {
        title = "Alert Message";
    }
    if (timeout == null) {
        timeout = 5000;
    }
    $('#minegraph-dialog').text(message);
    $('#minegraph-dialog').dialog('option', 'title', title);
    $('#minegraph-dialog').dialog('open');
    setTimeout("$('#minegraph-dialog').dialog('close');", timeout);

}

/*
* color for coloring cells in a bicluster
*/

var color_level7 = 'rgba(178, 71, 0, 0.8)',
    color_level6 = 'rgba(238, 0, 0, 0.8)',           
    color_level5 = 'rgba(204, 81, 0, 0.8)',
    color_level4 = 'rgba(230, 91, 0, 0.8)',       
    color_level3 = 'rgba(255, 101, 0, 0.8)',
    color_level2 = 'rgba(255, 132, 51, 0.8)',
    color_level1 = 'rgba(255, 178, 128, 0.8)';
    color_thinBic_by_row = 'rgba(255, 255, 51, 0.8)',
    color_thinBic_by_col = 'rgba(0, 255, 0, 0.8)',
    color_metaBic = 'rgba(102, 102, 255, 0.8)',
    // color used for the selected cells
    color_grid_selected = "rgba(156, 89, 66, 0.8)";   // "rgba(131, 111, 255, 0.7)"     

/*
* two array mapping the state name with its abbreviation
*/
var stateAbb = ["AK", "AL", "AR", "AZ", "CA", 
                "CO", "CT", "DE", "FL", "GA", 
                "HI", "IA", "ID", "IL", "IN", 
                "KS", "KY", "LA", "MA", "MD", 
                "ME", "MI", "MN", "MO", "MS", 
                "MT", "NC", "ND", "NE", "NH", 
                "NJ", "NM", "NV", "NY", "OH", 
                "OK", "OR", "PA", "RI", "SC", 
                "SD", "TN", "TX", "UT", "VA", 
                "VT", "WA", "WI", "WV", "WY"],

    state = ["Alaska", "Alabama", "Arkansas", "Arizona", "California",
            "Colorado", "Connecticut", "Delaware", "Florida", "Georgia",
            "Hawaii", "Iowa", "Idaho", "Illinois", "Indiana",
            "Kansas", "Kentucky", "Louisiana", "Massachusetts", "Maryland",
            "Maine", "Michigan", "Minnesota", "Missouri", "Mississippi",
            "Montana", "North Carolina", "North Dakota", "Nebraska", "New Hampshire",
            "New Jersey", "New Mexico", "Nevada", "New York", "Ohio",
            "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island", "South Carolina", 
            "South Dakota", "Tennessee", "Texas", "Utah", "Virginia",
            "Vermont", "Washington", "Wisconsin", "West Virginia", "Wyoming"];

// find the abbreviation of a state name
function stateName(string, stateAbb, state) {
    for (var i = 0; i < state.length; i++) {
        if (string == state[i])
            return stateAbb[i];
    }
    return 0;
}
