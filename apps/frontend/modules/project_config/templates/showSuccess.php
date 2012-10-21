<?php
slot('title', ('MineVis - Viewing Project Config'));
?>

<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $project_config->getId() ?></td>
    </tr>
    <tr>
      <th>Project:</th>
      <td><?php echo $project_config->getProjectId() ?></td>
    </tr>
    <tr>
      <th>Table a:</th>
      <td><?php echo $project_config->getTableA() ?></td>
    </tr>
    <tr>
      <th>Table axb:</th>
      <td><?php echo $project_config->getTableAxb() ?></td>
    </tr>
    <tr>
      <th>Table b:</th>
      <td><?php echo $project_config->getTableB() ?></td>
    </tr>
    <tr>
      <th>Table a id field:</th>
      <td><?php echo $project_config->getTableAIdField() ?></td>
    </tr>
    <tr>
      <th>Table b id filed:</th>
      <td><?php echo $project_config->getTableBIdField() ?></td>
    </tr>
    <tr>
      <th>Table a description field:</th>
      <td><?php echo $project_config->getTableADescriptionField() ?></td>
    </tr>
    <tr>
      <th>Table b description filed:</th>
      <td><?php echo $project_config->getTableBDescriptionField() ?></td>
    </tr>
    <tr>
      <th>Table axb table a id:</th>
      <td><?php echo $project_config->getTableAXBTableAIdField() ?></td>
    </tr>
    <tr>
      <th>Table abx table b id:</th>
      <td><?php echo $project_config->getTableAXBTableBIdField() ?></td>
    </tr>
  </tbody>
</table>

<a href="<?php echo url_for('project_config_index', $project_config) ?>">Back to config page</a>
 |
<a href="<?php echo url_for('project_config_edit',$project_config) ?>">Edit</a>
 |
<?php echo link_to('Delete', 'project_config/delete?id='.$project_config->getId(),
        array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>

