<?php
slot('title', ('MineVis - Configuring Project ' . $project_config->getProject()->getName()));
?>

<h2>Edit Project config table</h2>

<?php include_partial('form', array('form' => $form)) ?>

<a href="<?php echo url_for('project_config_index', $project_config) ?>">Back to list</a>
