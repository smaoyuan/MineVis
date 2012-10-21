<?php use_javascript('mining.js') ?>

<?php
        $jobs = $chaining->getLinkTypes();
        $total = $jobs->count();
        $done = 0;
        foreach ($jobs as $job) {
            if ($job->isDone()) {
                $done++;
            }
        }
        slot('auto_reload', 10000);
        ?>
    <div class="statusbar"></div>
    <div id="statuspercent"><?php echo intval($done / $total * 100);?></div>
        <?php  echo 'Link Types Chainged: ' . $done . '/' . $total; ?>
    <div class="togglebox">
        <div class="toggledetails">
            <div><strong>Progress Log:<br/></strong><?php echo nl2br( file_get_contents(sfConfig::get('sf_root_dir') . '/cache/algorithms/minetree/chaining_' . $chaining->getId().'.log') );?></div>
        </div>
    </div>