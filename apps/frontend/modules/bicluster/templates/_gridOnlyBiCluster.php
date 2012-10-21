<?php use_javascript('raphael-min.js') ?>

<script type="text/javascript">
    //data
    var biclusterdata = <?php echo $sf_data->getRaw('json'); ?>
            
    //drawing script
    window.onload = function() {  
        var margin = 15;
        var c_empty = '#aaa';
        var c_empty_h = '#aaa';
        var c_rel = '#555';
        var c_bic = '#FF6F00';
        var gridX = biclusterdata.grid[0].length;
        var gridY = biclusterdata.grid.length;
        //alert('gx ' + gridX + ' y ' + gridY);
        var cellW = 15;
        var cellH = 15;
                
        //make size relative on margin and number of cells
        var sizex = margin + cellW * gridX;
        var sizey = margin + cellH * gridY;
                
        var paper = new Raphael(document.getElementById('canvas_container'), sizex, sizey);
                
        //loop rows
        for(var r = 0; r < gridY; r+=1) {
            for(var c = 0; c < gridX; c+=1) {
                var x = margin + c * cellW;
                var y = margin + r * cellH;
                var rectangle = paper.rect(x, y, cellW, cellH); 
                var color = c_empty;
                if (biclusterdata.grid[r][c] == 1) {
                    color = c_rel;
                } else if (biclusterdata.grid[r][c] == 2) {
                    color = c_bic;
                }
                //alert('r: ' + r + ' c: ' + c);
                rectangle.attr({fill: color, stroke: '#fff', 'stroke-width': 2}); 
                //add pop up description?
                        
            }
        }
    } 
</script>

<div id="canvas_container"></div>  