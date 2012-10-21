<?php slot('title', ('MineVis - Project ' . $project->getName()));
slot('current_menu', 'project'); ?>

<h2><?php echo $project->getName() ?></h2>

<nav class="secondary">
    <?php
    echo link_to("Go to project list", 'project/index');
    echo link_to("Edit Project", 'project/edit?id=' . $project->getId());
    echo link_to("Configure Project", '@project_config_index?project_id=' . $project->getId()) . "\n";
    ?>
</nav>

<h3>Info:</h3>
<table>
    <tbody>
        <tr>
            <th>project Id:</th>
            <td><?php echo $project->getId() ?></td>
        </tr>
        <tr>
            <th>External DB:</th>
            <td><?php echo $project->getExternalDatabase() ?></td>
        </tr>
        <tr>
            <th>Created at:</th>
            <td><?php echo $project->getCreatedAt() ?></td>
        </tr>
        <tr>
            <th>Updated at:</th>
            <td><?php echo $project->getUpdatedAt() ?></td>
        </tr>
        <tr>
            <th>Number of mappings:</th>
            <td><?php echo $project->getConfigMappingsCount() ?></td>
        </tr>
    </tbody>
</table>

<a href="<?php echo url_for('project_va', $project) ?>">Visual Analytics Page</a>

<h3>Minings based on this project</h3>

<?php if ($minings->count() > 0): ?>
    <?php foreach ($minings as $mining): ?>
        <ul>
            <li><a href="<?php echo url_for('mining/show?id=' . $mining->getId()) ?>"><?php echo $mining->getName() ?>
                    (<?php echo $alorithms[$mining->getAlgorithm()] ?>/<?php echo $types[$mining->getType()] ?>)
                </a></li>
        </ul>
    <?php endforeach; ?>
<?php else: ?>
    <p>No Minings Available based on this project, create one below.</p>
<?php endif; ?>

<h3>Vis based on this project</h3>
<?php
$vises = $project->getVisualizations();
?>

<?php if ($vises->count() > 0): ?>
    <?php foreach ($vises as $vis): ?>
        <ul>
            <li><a href="<?php echo url_for('vis/show?id=' . $vis->getId()) ?>"><?php echo $vis->getName() ?>
                </a></li>
        </ul>
    <?php endforeach; ?>
<?php else: ?>
    <p>No Visualizations Available based on this project, create one below.</p>
<?php endif; ?>

<?php echo link_to('Create New Mining', 'project_mining_new', array('project_id' => $project->getId()), array('class' => 'button')) ?>