<ul>
    <?php if($mining_job_mappings->count() > 0): ?>
        <?php foreach ($mining_job_mappings as $mining_job_mapping): ?>
    <li>Mapping #<?php
                echo $mining_job_mapping->getId() . ' (' . $mining_job_mapping->getStatus() .
                        ', runtime: ' .$mining_job_mapping->getRunTime() .')';
                ?></li>
        <?php endforeach; ?>
    <?php else: ?>
    <li>No mining job mappings available.</li>
    <?php endif; ?>
</ul>