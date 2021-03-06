/*
 * General Code for the overall application
 */

/**
 * Autoload some scripts
 */
$(document).ready(function() {
    //init scripts
    initToggleBox();
    initCollapseBox();
    initColorBox();

    //Turn range inputs to sliders
    formInputImprovements();

});

/**
 * If it detects any preprogramed ids it hides the inputs and adds a slider next
 * to it.
 *
 * A min of 50 makes it easier to slide it but you can still type one higher.
 *
 * Here's some inputs it changes to sliders:
 * Mining:
 *      mining_min_support
 *      mining_min_columns
 * Chaining:
 *      chaining_max_neighbors
 *      chaining_distance_threshold
 */
function formInputImprovements() {
    //mining
    inputToSlider("mining_min_support", 'max', 2, 50, 1);
    inputToSlider("mining_min_columns", 'max', 2, 50, 1);
    //chaining
    inputToSlider("chaining_max_neighbors", 'min', 1, 100, 1);
    inputToSlider("chaining_distance_threshold", 'min', 0.0001, 1, 0.0001);
}

/**
 * Appends a jq slider div after an input, then hide input and set up slider
 * @param inputid name of the input to turn into a slider.
 * @param rangeval is it a 'min' or a 'max' slider
 * @param minval min for slider
 * @param maxval max for slider.
 * @param stepval how much does the slider increment by
 */
function inputToSlider(inputid, rangeval, minval, maxval, stepval) {
    //only do it if the item exists
    if ($("#" + inputid).length > 0){
        // give slider a unique name
        var slider = inputid + "_slider";

        // add a div for the slider.
        $( "#" + inputid ).after("<div id=\""+slider+"\"></div>");

        // set up slider
        $( "#" + slider ).slider({
            range: rangeval,
            min: minval,
            max: maxval,
            step: stepval,
            value: $( "#" + inputid ).val(),
            slide: function( event, ui ) {
                //update input value when slider changes
                $( "#" + inputid ).val( ui.value );
            }
        });

        // update slider value when input changes
        $( "#" + inputid).keyup( function(){
            //increase slider max if value is too big
            if ($( "#" + inputid ).val() > $( "#" + slider ).slider( "option", "max")) {
                $( "#" + slider ).slider( "option", "max", $( "#" + inputid ).val() );
            //kinda buggy for some reason...
            }
            // update slider value
            $( "#" + slider ).slider( "option", "value", $( "#" + inputid ).val() );
        });
    }
}

/**
 * Initializes various color boxes.
 * Any link of class "vis_box" will have it's url included in an iframe color
 * box.
 */
function initColorBox() {
    //simple iframe big color box
    $(".vis_box").colorbox({
        width:"50%",
        height:"80%",
        iframe:true
    });
}

/**
 * This enables a toggle show/hide details box where the corresponding mark up is detected:
 * <div class='togglebox'>
 * <div class='toggleswitch'>TITLE HERE</div>
 * <div class='toggledetails'>DETAILS TO BE HIDDEN HERE</div>
 * </div>
 */
function initToggleBox() {
    $(".togglebox").append('<a href="#" class="toggleswitch">Show Details</a>');
    $(".toggledetails").hide();
    $(".toggleswitch").click(function(){
        $(this).prev().toggle("blind",{},250);
        if ($(this).text() == "Show Details") {
            $(this).text("Hide Details");
        } else {
            $(this).text("Show Details");
        }
        return false;
    });
}

/**
 * This enables a toggle =/- box where the corresponding mark up is detected:
 * <div class='collapsible'>
 * HIDDEN CONTENT HERE
 * </div>
 */
function initCollapseBox() {
    $(".collapsible").before('<a href="#" class="collapselink button">+</a>');
    $(".collapsible").hide();
    $(".collapselink").click(function(){
        $(this).next().toggle("blind",{},250);
        if ($(this).text() == "+") {
            $(this).text("-");
        } else {
            $(this).text("+");
        }
        return false;
    });
}

/**
 * This can be called to initiate a biclusterLinkVisualization.
 * @param linkData object cluster data in the format below:
 * {
 *      'target' => clusterData (see below for description)
 *      'destinations' => [
 *          clusterData,
 *          clusterData,
 *          ...
 *      ]
 * }
 * @param containerId string element to use for canvas
 *
 */

function biClusterLinkVis(linkData, containerId) {
    //make size relative on number of bics
    var sizex = 500;
    var sizey = 150 * linkData.destinations.length;
    var paper = new Raphael(document.getElementById(containerId), sizex, sizey);

    helperDrawBiCluster(paper, linkData.target, 0, 0);
    var ox, oy;
    ox = 150;
    oy = 0;
    for (i = 0; i < linkData.destinations.length; i++) {
        helperDrawBiCluster(paper, linkData.destinations[i], ox, oy);
        oy += 150;
    }
}


function helperDrawBiCluster(raf, bic, xOffset, yOffset) {
    var margin = 80;
    var c_bic = '#FF6F00';
    //console.debug(bic);
    var gridX = bic.grid[0].length;
    var gridY = bic.grid.length;
    //alert('gx ' + gridX + ' y ' + gridY);
    var cellW = 15;
    var cellH = 15;

    //temps
    var r, c, x, y, t, txt;

    //draw axis y
    for( r = 0; r < gridY; r+=1) {
        x = xOffset + margin - 5;
        y = yOffset + margin + r * cellH + 7;
        t = 'undefined';
        if (typeof bic.rows[r] != 'undefined' ) {
            t = bic.rows[r].name;
        }
        txt = raf.text(x,y,t);
        txt.attr({
            'text-anchor': 'end'
        });
    }
    //draw axis x
    for( c = 0; c < gridX; c+=1) {
        y = yOffset + margin-5;
        x = xOffset + margin + c * cellW + 7;
        t = 'undefined';
        if (typeof bic.cols[c] != 'undefined' ) {
            t = bic.cols[c].name;
        }
        txt = raf.text(x,y,t);
        txt.attr({
            'text-anchor': 'end'
        });
        txt.rotate(45,x,y);
    }
    //loop rows
    for( r = 0; r < gridY; r+=1) {
        for( c = 0; c < gridX; c+=1) {
            x = xOffset + margin + c * cellW;
            y = yOffset + margin + r * cellH;
            var rectangle = raf.rect(x, y, cellW, cellH);
            var color = c_bic;
            rectangle.attr({
                fill: color,
                stroke: '#fff',
                'stroke-width': 2
            });
        //add pop up description?
        }
    }
}

/**
 * This can be called to initiate a miniBiclusterVisualization.
 * @param clusterData object cluster data in the format below:
 * {
 *      'grid' => [2d array]
 *      'rows' => [{id,'name'},...]
 *      'cols' => [{id,'name'},...]
 * }
 * @param containerId string element to use for canvas
 *
 */
function miniBiClusterVis(clusterData, containerId) {
    var gridX = clusterData.grid[0].length;
    var gridY = clusterData.grid.length;
    var margin = 80;
    var cellW = 15;
    var cellH = 15;
    //alert('gx ' + gridX + ' y ' + gridY);

    //make size relative on margin and number of cells
    var sizex = margin + cellW * gridX;
    var sizey = margin + cellH * gridY;
    var preview_vis = new Raphael(document.getElementById(containerId), sizex, sizey);

    //call helper to draw
    helperDrawBiCluster(preview_vis, clusterData, 0, 0);
}

/**
 * This can be called to initiate a simpleBiclusterVisualization.
 * @param clusterData object cluster data in the format below:
 * {
 *      'grid' => [2d array]
 *      'rows' => [{id,'name'},...]
 *      'cols' => [{id,'name'},...]
 * }
 * @param containerId string element to use for canvas
 *
 */
function simpleBiClusterVis(clusterData, containerId) {
    var margin = 150;
    var c_empty = '#aaa';       // empty entries
    var c_empty_h = '#aaa';     // empty entries
    var c_rel = '#555';
    var c_bic = '#FF6F00';      // bicluster entries
    var gridX = clusterData.cols.length;
    var gridY = clusterData.rows.length;
    var cellW = 15;
    var cellH = 15;
    //make size relative on margin and number of cells
    var sizex = margin + cellW * gridX;
    var sizey = margin + cellH * gridY;
    var r, c, x, y, t, txt;

    //init canvas
    var preview_vis = new Raphael(document.getElementById(containerId), sizex, sizey);

    //text array to store names of the column and row
    var crName = new Array();
    //column names
    crName[0] = new Array();
    //row names
    crName[1] = new Array();

    //element array to store retangles
    var rectangle = new Array();
    for( r = 0; r < gridY; r+=1) {
        rectangle[r] = new Array();
    }

    //draw axis y
    for( r = 0; r < gridY; r+=1) {
        x = margin - 5;
        y = margin + r * cellH + 7;
        t = 'undefined';
        if (typeof clusterData.rows[r] != 'undefined' ) {
            t = clusterData.rows[r].name;

            // initial row names array
            // crName[1][r] = t;
        }
        // txt = preview_vis.text(x,y,t);

        crName[1][r] = preview_vis.text(x,y,t);

        crName[1][r].attr({
            'text-anchor': 'end'
        });
    }
    //draw axis x
    for( c = 0; c < gridX; c+=1) {
        y = margin - 5;
        x = margin + c * cellW + 7;
        t = 'undefined';
        if (typeof clusterData.cols[c] != 'undefined' ) {
            t = clusterData.cols[c].name;

            // initial column names array
            crName[0][c] = t;
        }
        //txt = preview_vis.text(x,y,t);
        crName[1][r] = preview_vis.text(x,y,t);
        crName[1][r].attr({
            'text-anchor': 'end'
        });
        // txt.rotate(90,x,y);
        crName[1][r].rotate(90,x,y);
    }

    // starting choosing box elements in the "preview_vis" canvas
    preview_vis.setStart();

    //loop rows
    for( r = 0; r < gridY; r+=1) {
        for( c = 0; c < gridX; c+=1) {
            x = margin + c * cellW;
            y = margin + r * cellH;

            rectangle[r][c] = preview_vis.rect(x, y, cellW, cellH, 3);

            // var rectangle = preview_vis.rect(x, y, cellW, cellH, 3);

            var color = c_empty;
            if (clusterData.grid[r][c] == 1) {
                color = c_rel;
            } else if (clusterData.grid[r][c] == 2) {
                color = c_bic;
            }
            // alert('r: ' + r + ' c: ' + c);
            rectangle[r][c].attr({
                fill: color,
                stroke: '#fff',
                'stroke-width': 2,
            });
        //add pop up description?
        }
    }

    // return all box elements in the "preview_vis" canvas
    mineVisTable = preview_vis.setFinish();

    // mouse move in: changing color
    var over = function() {
        this.color = this.color || this.attr("fill");
        this.stop().animate({fill: "#5555FF"}, 350);       
    }

    // mouse move out: change back to original color
    var out = function() {
        this.stop().animate({fill: this.color}, 350);       
    }

    // changing color for boxes when mouse moves in/out
    mineVisTable.hover(over, out);

    // row index for selected box
    var rowIndex;
    // column index for selected box
    var columnIndex;
    // find index for a certain rectangle
    var findCRIndex = function(id) {
        rowIndex = parseInt((id - rectangle[0][0].id) / gridX);
        columnIndex = parseInt(id - (rectangle[0][0].id + rowIndex * gridX));
    }

    // click an item in the table to popup details
    mineVisTable.click(function(){
        if (this.color == '#aaa') 
            alert("there is no bicluster for this item");
        else if (this.color == '#FF6F00') {
            findCRIndex(this.id);
            crName[1][rowIndex].animate({fill: "#5555FF"}, 350);   
            // crName[0][columnIndex].stop().animate({fill: "#5555FF"}, 350);                        
            alert("Connection between: \"" + crName[1][rowIndex] + "\" and \"" + crName[0][columnIndex] + "\".\n MiniView is shown below.");         
        } else {
            findCRIndex(this.id);
            alert("Connection between: \"" + crName[1][rowIndex] + "\" and \"" + crName[0][columnIndex] + "\".");                    
        }

    });
}







