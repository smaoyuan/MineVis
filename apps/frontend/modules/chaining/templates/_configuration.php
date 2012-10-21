<?php
$links = $chaining->getLinkTypes();
?>

<p>This chaining try to link BiClusters containing the following types:</p>
<ul>
    <?php foreach ($links as $link) : ?>
        <li><?php echo $link->getName() ?></li>
    <?php endforeach; ?>
</ul><br/>

<?php
$mining_complete = $chaining->getMining()->getComplete();
if ($mining_complete == 1) {
    echo link_to('Run Chaining!', 'chaining_run', array( 'id' => $chaining->getId()), array('class' => 'button'));
} else {
    echo 'Cannot run chaining algorithm until mining algorithm is complete.';
}
?>