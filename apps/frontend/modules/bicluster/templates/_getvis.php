<html>
    <head>
        <?php
        use_javascript('jquery-1.7.1.min.js', 'first');
        use_javascript('raphael-min.js');
        use_javascript('minevis.js');
        use_javascript('jquery.colorbox-min.js');
        include_javascripts();
        ?>
    </head>
    <body>
        <?php
//first we included the scripts so this can run in it an iframe, now we include the vis
        include_partial($visualization, array('bicluster' => $bicluster, 'vis_count' => rand(10, 100)));
        ?>
    </body>
</html>

