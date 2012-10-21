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
<ul class="items">
    <?php if ($biclusters->count() != 0) : ?>
        <li><span class="ui-icon ui-icon-folder-collapsed"></span><?php echo getConfigCaption($configs, $biclusters->getFirst()); ?>
            <?php $lastConfigId = ($biclusters->getFirst() != null) ? $biclusters->getFirst()->getConfigId() : -1; ?>
            <ul>
                <?php foreach ($mining->getBiClusters() as $biCluster) : ?>
                    <?php if ($biCluster->getConfigId() != $lastConfigId) : ?>
                        <?php $lastConfigId = ($biCluster != null) ? $biCluster->getConfigId() : -1; ?>
                    </ul>
                </li>
                <li><span class="ui-icon ui-icon-folder-collapsed"></span><?php echo getConfigCaption($configs, $biCluster); ?>
                    <ul>
                        <li>
                            <a class="item"><span class="ui-icon ui-icon-calculator"></span>Bicluster id <?php echo $biCluster->getId(); ?><span class="item_id"><?php echo $biCluster->getId(); ?></span><span class="item_type">bic</span></a>
                        </li>
                    <?php else : ?>
                        <li>
                            <a class="item"><span class="ui-icon ui-icon-calculator"></span>Bicluster id <?php echo $biCluster->getId(); ?><span class="item_id"><?php echo $biCluster->getId(); ?></span><span class="item_type">bic</span></a>
                        </li>
                    <?php endif; ?>

                <?php endforeach; ?>
            </ul>
        </li>
    <?php else : ?>
        <li>No BiCluster results found for this mining. Try a lower support number and run another mining.</li>
    <?php endif; ?>
</ul>