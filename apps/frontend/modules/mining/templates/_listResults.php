<?php
$lastConfigId = -1;
$configs = $mining->getProject()->getProjectConfig();
$biclusters = $mining->getBiClusters();

function getConfigCaption($configs, $bcluster) {
    if ($bcluster != null) {
        $id = $bcluster->getConfigId();
        foreach ($configs as $config) {
            if ($config->getId() == $id) {
                return 'Mapping of ' . $config->getTableA() .
                        ' to ' . $config->getTableB();
            }
        }
    }
    return "Mapping Not Found Error";
}
?>
<div>
    <h4>BiClusters:</h4>
    <ul>
        <?php if ($biclusters->count() != 0) : ?>
            <li><?php echo getConfigCaption($configs, $biclusters->getFirst()); ?>
                <?php $lastConfigId = ($biclusters->getFirst() != null) ? $biclusters->getFirst()->getConfigId() : -1; ?>
                <ul>
                    <?php foreach ($mining->getBiClusters() as $biCluster) : ?>
                        <?php if ($biCluster->getConfigId() != $lastConfigId) : ?>
                            <?php $lastConfigId = ($biCluster != null) ? $biCluster->getConfigId() : -1; ?>
                        </ul>
                    </li>
                    <li><?php echo getConfigCaption($configs, $biCluster); ?>
                        <ul>
                            <li>
                                Bicluster id <?php echo $biCluster->getId(); ?>

                                <?php echo link_to('Click Here to preview', 'bicluster_vis', array('id' => $biCluster->getId()), array('class' => 'vis_box', 'rel' => 'group' . $lastConfigId, 'title' => 'Bicluster id ' . $biCluster->getId())); ?>
                                <?php echo link_to('(Details)', 'bicluster_show', $biCluster) ?>
                            </li>
                        <?php else : ?>
                            <li>
                                Bicluster id <?php echo $biCluster->getId(); ?>
                                <?php echo link_to('Click Here to preview', 'bicluster_vis', array('id' => $biCluster->getId()), array('class' => 'vis_box', 'rel' => 'group' . $lastConfigId, 'title' => 'Bicluster id ' . $biCluster->getId())); ?>
                                <?php echo link_to('(Details)', 'bicluster_show', $biCluster) ?>
                            </li>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </ul>
            </li>
        <?php else : ?>
            <li>No BiCluster results found for this mining. Try a lower support number and run another mining.</li>
        <?php endif; ?>
    </ul>
</div>