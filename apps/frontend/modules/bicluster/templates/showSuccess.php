<?php
/*
 * Here I just have a set of helper functions
 */

function getFieldName($rows, $id) {
    $temp = $rows->getRaw($id);
    return $temp[1];
}

$vis_count = 0;
?>
<h2>
    BiCluster <?php echo $mining_bi_cluster->getId() ?> from <?php echo $mining->getName() ?>
</h2>
<div class="nav"><?php echo link_to('Back to mining', 'mining_show', $mining) ?>  | <a href="<?php echo url_for('bicluster/index') ?>">List</a></div>

<?php
/*<div>
    <h3>Table</h3>
 include_partial('tableBiCluster', array('columns' => $columns, 'rows' => $rows, 'bicluster' => $bicluster, 'relationships' => $relationships)) 
</div>*/
?>
<div>
    <h3>BiCluster</h3>
    <?php include_partial('singleBiCluster', array('bicluster' => $mining_bi_cluster, 'vis_count' => $vis_count++)) ?>
</div>

<div>
    <h3>BiCluster Mini View</h3>
    <?php include_partial('miniBiCluster', array('bicluster' => $mining_bi_cluster, 'vis_count' => $vis_count++)) ?>
</div>