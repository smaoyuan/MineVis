<h2>Manually Running Mining Job #<?php echo $mining->getId() ?>: <?php echo $mining->getName() ?></h2>

<?php foreach ($mining_job_mappings as $mining_job_mapping): ?>

<div class="togglebox">
    <div class="toggletitle">
        <h3>Mining Job #<?php echo $mining_job_mapping->getId() ?></h3>
    </div>
    <div class="toggledetails">
            <?php
//            include_partial('jobDetails', array(
//                    'mining_job_mapping' => $mining_job_mapping,
//                    'mining' => $mining,
//                    'config' => $config,
//                    'input_file' => $input_file,
//                    'output_file' => $output_file,
//                    'output' => $output
//            ));
            ?>
        <a href="<?php echo url_for('mining_job_mapping/show?id='.$mining_job_mapping->getId()) ?>">
            Open job's page</a>
    </div>
</div>

<?php endforeach; ?>
