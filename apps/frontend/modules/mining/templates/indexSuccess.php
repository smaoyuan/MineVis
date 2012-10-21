<h2>Mining List</h2>

<p><a href="<?php echo url_for('mining/new') ?>" class="button">Add New Mining</a></p>


<div class="indexheader">
    <span>Project</span>
    <span>Mining Name</span>
    <span>Complete</span>
    <span>Type</span>
    <span>Algorithm</span>
</div>
<ul class="indexlist">
    <?php foreach ($minings as $mining): ?>
        <li>
            <?php echo link_to($mining->getProject()->getName(), 'project/show?id=' . $mining->getProject()->getId()); ?>
            <a href="<?php echo url_for('mining/show?id=' . $mining->getId()) ?>"><span><?php echo $mining->getName() ?></span>
                <span><?php echo ($mining->getComplete() == 1) ? "yes" : "no" ?></span>
                <span><?php echo $types[$mining->getType()] ?></span>
                <span><?php echo $alorithms[$mining->getAlgorithm()] ?></span></a>
        </li>
    <?php endforeach; ?>
</ul>
</table>