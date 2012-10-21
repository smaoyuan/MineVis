<?php
slot('title', ('MineVis - Configuring Project'));
?>
<h2>Project config</h2>

<h3>Existing table mapping</h3>
<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Table axb</th>
      <th>Mapping Details</th>
    </tr>
  </thead>
  <tbody>
    <?php if($project_configs->count()>0): ?>
    <?php foreach ($project_configs as $project_config): ?>
    <tr>
      <td><a href="<?php echo url_for('project_config_show', $project_config) ?>"><?php echo $project_config->getId() ?></a></td>
      <td><a href="<?php echo url_for('project_config_show', $project_config) ?>"><?php echo $project_config->getTableAxb() ?></a></td>
      <td>(<?php echo $project_config->getTableADescriptionField() ?> to <?php echo $project_config->getTableBDescriptionField() ?>)</td>
    </tr>
    <?php endforeach; ?>
    <?php else: ?>
    <tr><td>No Configuration mappings available.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<h3>Add new table relationship:</h3>
<div>
<?php include_partial('form', array('form' => $form)) ?>
</div>
