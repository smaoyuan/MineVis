<?php
slot(
  'title',
  'MineVis - Projects');
slot('current_menu', 'project');
?>

<h2>Projects List</h2>
  <a href="<?php echo url_for('project/new') ?>" class="button">Add New Project</a>

    <div class="indexheader">
      <span>Project Name</span>
      <span>External database</span>
      <span>Created at</span>
      <span>Updated at</span>
    </div>
  <ul class="indexlist">
    <?php foreach ($projects as $project): ?>
    <li>
      <a href="<?php echo url_for('project/show?id='.$project->getId()) ?>">
      <span><?php echo $project->getName() ?></span>
      <span><?php echo $project->getExternalDatabase() ?></span>
      <span><?php echo $project->getCreatedAt() ?></span>
      <span><?php echo $project->getUpdatedAt() ?></span></a>
    </li>
    <?php endforeach; ?>
  </ul>
</table>