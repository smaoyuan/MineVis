<?php echo link_to('Run Mining!', 'mining_run', array('id' => $mining->getId()), array('class' => 'button')) ?>

<div class="togglebox">
    <div class="toggletitle">Target BiClusters Listing</div>
    <div class="toggledetails">
        BiClusters will be computer for the following relationships: <br/>
        <?php include_partial('project_config/list', array('project_configs' => $relationships)) ?>
    </div>
</div>
