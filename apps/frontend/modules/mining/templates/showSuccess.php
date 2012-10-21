<?php
use_javascript('mining.js');

$pid = $mining->getProcessId();
$running = false;
exec("ps " . $pid, $output, $result);
if (count($output) >= 2) {
    $running = true;
}

$project = $mining->getProject();
?>

<h2>Mining: <?php echo $mining->getName() ?></h2>

<nav class="secondary">
    <?php
//they have to be echoed all together otherwise it will add spacing between them.
    echo link_to("Go to " . $project->getName() . " Project", 'project/show?id=' . $project->getId());
    echo link_to("Go to Minings list", 'mining/index');
    echo link_to("Edit Mining", 'mining/edit?id=' . $mining->getId());
    echo link_to('Delete Mining', 'mining/delete?id=' . $mining->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) . "\n";
    ?>
</nav>

<div>
    <h3>Info</h3>
    <div><strong>Mining Id#:</strong> <?php echo $mining->getId() ?></div>
    <div><strong>Created at</strong>: <?php echo $mining->getCreatedAt() ?>, Last Updated <?php echo $mining->getUpdatedAt() ?></div>
    <strong>Type:</strong><?php echo $types[$mining->getType()] ?><br/>
    <strong>Algorithm:</strong><?php echo $alorithms[$mining->getAlgorithm()] ?> (debug: <?php echo $mining->getAlgorithmName() ?>)<br/>
    <strong>Parameters:</strong><?php echo $mining->paramsToString() ?><br/>
</div>
<div>
    <h3>Status</h3>
    <?php
    if ($mining->getComplete() == 1) {
        echo '<strong>Complete</strong>';
    } else if ($mining->getStarted() == 1) {
        echo '<strong>Started</strong>';
    } else {
        echo '<strong>Pending</strong>';
    }
    ?>
    <div><strong>Process ID:</strong> <?php echo $pid . ($running ? " (running)" : " (not running)") ?></div>
    <?php if ($project->getJigsawBased() == true and $mining->getComplete() == 1) : ?>
        <div>
            <strong>Document Linking Process:</strong>
            <?php
            echo $mining->getDocumentLinkingStatus() . " ";
            if ($mining->getDocumentLinkStatus() == 0) {
                echo link_to('Run Now', 'mining_run_documentlink', array('id' => $mining->getId()), array('class' => 'button'));
            }
            ?>
        </div>
        <div>
            <strong>Entity Frequency Generation Process: </strong>
            <?php
            echo $mining->getEntityFrequenciesStatus() . " ";
            if ($mining->getEntityFrequencyStatus() == 0) {
                echo link_to('Run Now', 'mining_run_entityfrequency', array('id' => $mining->getId()), array('class' => 'button'));
            }
            ?>
        </div>
    <?php endif; ?>
    <br/>
</div>
<div>
    <h3>Details</h3>
    <?php
    if ($mining->getStarted() == 1 and $mining->getComplete() == 1):

        //display the complete mining partial
        include_partial('showComplete', array('mining' => $mining));

    elseif ($mining->getStarted() == 1 and $mining->getComplete() == 0):

        //display the running partial
        include_partial('showRunning', array('jobMappings' => $jobMappings, 'mining' => $mining));

    elseif ($mining->getStarted() == 0 and $mining->getComplete() == 0):

        //display the config partial
        include_partial('showConfig', array('relationships' => $relationships, 'mining' => $mining));
    endif;
    ?>
</div>