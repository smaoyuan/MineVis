<?php
//Include Graphics JS
use_javascript('raphael-min.js');

//Generate a unique id
$vis_id = 'bicluster_link_' . $link->getId();
$var = 'bicluster_link_data_' . $vis_count;
?>

<script type="text/javascript">
   var <?php echo $var; ?> = <?php echo $sf_data->getRaw('link')->getJSON(); ?>;

    $(document).ready(function() {
        biClusterLinkVis(<?php echo $var . ', "' . $vis_id . '"'; ?>);
    });
</script>

<div id="<?php echo $vis_id ?>"></div>
