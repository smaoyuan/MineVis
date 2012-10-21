<h2>Visualizations List</h2>

<p>
    Here you can open a previous visualization or create a new one based on some
    new data mining results.
</p>

<a href="<?php echo url_for('vis/new') ?>" class="button">Add New Vis</a>

<div class="indexheader">
    <span>Project</span>
    <span>Mining</span>
    <span>Chaining</span>
    <span>Visualization</span>
</div>

<ul class="indexlist">
    <?php foreach ($visualizations as $vis): ?>
        <li>
            <?php echo link_to($vis->getProject()->getName(), 'project/show?id=' . $vis->getProjectId()); ?>
            <?php echo link_to($vis->getMining()->getName(), 'mining/show?id=' . $vis->getMiningId()); ?>
            <?php echo link_to($vis->getChaining()->getName(), 'chaining/show?id=' . $vis->getChainingId()); ?>
            <a href="<?php echo url_for('vis/show?id=' . $vis->getId()) ?>"><span><?php echo $vis->getName() ?></span>
                <span></span></a>
        </li>
    <?php endforeach; ?>
</ul>


