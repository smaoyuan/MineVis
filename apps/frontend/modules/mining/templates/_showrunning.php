<?php
$total = $jobMappings->count();
$done = 0;
foreach ($jobMappings as $job) {
    if ($job->isDone()) {
        $done++;
    }
}
$algorithm = $mining->getAlgorithmName();
$logs = $algorithm::getLogPaths($mining);

slot('auto_reload', 10000);
?>

    <div class="statusbar"></div>
    <div id="statuspercent"><?php echo intval($done / $total * 100);?></div>
        <?php  echo 'Relationships Mined: ' . $done . '/' . $total; ?>
    <div class="togglebox">
        <div class="toggletitle">Details:</div>
        <div class="toggledetails">
        <?php include_partial('mining_job_mapping/table', array('mining_job_mappings' => $jobMappings)) ?>
            <div><strong>Progress Log:</strong><br/><?php echo nl2br( file_get_contents(sfConfig::get('sf_root_dir') . $logs['log'])); ?></div>
        </div>
    </div>