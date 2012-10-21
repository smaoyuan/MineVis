<?php
//Include Graphics JS
use_javascript('raphael-min.js');

//Generate a unique id
$vis_id = 'mini_bicluster_' . $bicluster->getId();
$var = 'bicluster_data_' . $vis_count;
?>

<script type="text/javascript">
    var <?php echo $var; ?> = <?php echo $sf_data->getRaw('bicluster')->getMiniJSON(); ?>;

    $(document).ready(function() {
        miniBiClusterVis(<?php echo $var . ', "' . $vis_id . '"'; ?>);
    });
</script>

<div id="<?php echo $vis_id ?>"></div>
