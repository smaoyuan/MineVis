<?php
$dests = $chaining_link->getDestinations();
?>
<h2>Link Piece</h2>
<table>
    <tbody>
        <tr>
            <th>Id:</th>
            <td><?php echo $chaining_link->getId() ?></td>
        </tr>
        <tr>
            <th>Target bicluster:</th>
            <td><?php echo $chaining_link->getTargetBiclusterId() ?></td>
        </tr>
        <tr>
            <th>Destination bicluster:</th>
            <td><?php echo count($dests) ?></td>
        </tr>
        <tr>
            <th>Chaining:</th>
            <td><?php echo $chaining_link->getChainingId() ?></td>
        </tr>
        <tr>
            <th>Chaining link type:</th>
            <td><?php echo $chaining_link->getChainingLinkTypeId() ?></td>
        </tr>
    </tbody>
</table>

<h2>Whole Link</h2>
<div>
    <strong>Target:</strong>
    <?php echo link_to('Bicluster ' . $chaining_link->getTargetBiclusterId(), 'bicluster_vis', array('id' => $chaining_link->getTargetBiclusterId()), array('class' => 'vis_box', 'title' => 'Bicluster id ' . $chaining_link->getTargetBiclusterId())); ?>
</div>
<div>
    <strong>Destinations:</strong>
    <ul>
        <?php foreach ($dests as $dest) : ?>
            <li><?php echo link_to('Bicluster ' . $dest->getDestinationBiclusterId(), 'bicluster_vis', array('id' => $dest->getDestinationBiclusterId()), array('class' => 'vis_box', 'rel' => 'groupA', 'title' => 'Bicluster id ' . $dest->getDestinationBiclusterId())); ?></li>
            <?php ?>
            <?php ?>
        <?php endforeach; ?>
    </ul>
</div>


<h2>Link Visualization</h2>
<?php echo link_to('BiCluster Link ' . $chaining_link->getId(), 'bicluster_link_vis', array('id' => $chaining_link->getId()), array('class' => 'vis_box', 'title' => 'BiC. Link id ' . $chaining_link->getId())); ?>
<div>
    <?php include_partial('visBiClusterLink', array('link' => $chaining_link,'vis_count'=> 0)) ?>
</div>