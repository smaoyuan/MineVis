<?php
//Include Graphics JS
use_javascript('raphael-min.js');

//Generate a unique id
$vis_id = 'single_bicluster_' . $bicluster->getId();
$var = 'bicluster_data_' . $vis_count;
?>
<script type="text/javascript">
    //data
    var <?php echo $var; ?> = <?php echo $sf_data->getRaw('bicluster')->getFullJSON(); ?>

    //drawing script
    $(document).ready(function() {
    	// console.log(<?php echo $var; ?>);
        simpleBiClusterVis(<?php echo $var . ', "' . $vis_id . '"'; ?>)
    });
</script>

<div id="<?php echo $vis_id ?>"></div>
