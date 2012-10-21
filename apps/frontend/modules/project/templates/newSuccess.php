<?php slot(
  'title',
  'MineVis - Create Project');
slot('current_menu', 'project');
?>

<h2>New Project</h2>

<?php include_partial('form', array('form' => $form)) ?>
