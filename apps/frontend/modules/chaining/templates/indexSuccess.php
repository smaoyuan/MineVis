<h2>Chain Sets List</h2>

<a href="<?php echo url_for('chaining/new') ?>" class="button">Add New Chaining</a>


<div class="indexheader">
    <span>Project</span>
    <span>Mining</span>
    <span>Chaining Name</span>
    <span>Status</span>
</div>

<ul class="indexlist">
    <?php foreach ($chainings as $chaining): ?>
        <?php
        //do some stuff here
        $mining = $chaining->getMining();
        $project = $mining->getProject();
        ?>
        <li>
            <?php echo link_to($project->getName(),'project/show?id='.$project->getId()); ?>
            <?php echo link_to($mining->getName(),'mining/show?id='.$mining->getId()); ?>
            <a href="<?php echo url_for('chaining/show?id=' . $chaining->getId()) ?>"><span><?php echo $chaining->getName() ?></span>
                <span><?php
                            echo ($chaining->getComplete() ? "Complete" :
                                ($chaining->getStarted() ? "Running" :
                                ($chaining->getConfigured() ? "Configured" : "Created")));
                        ?></span></a>
        </li>
    <?php endforeach; ?>
</ul>

