<?php
slot('title', ('MineVis - Project ' . $project->getName()));
slot('current_menu', 'project');
use_javascript('visual_analytics.js');
?>

<h2><?php echo $project->getName() ?> - Visual Analytics</h2>
<div>
    <a href="<?php echo url_for('project/edit?id=' . $project->getId()) ?>">Edit</a> | <a href="<?php echo url_for('project_show', $project) ?>">Back to project</a>
</div>

<?php if (!$project->getJigsawBased()) : ?>
    <div>
        <h3>This is not a Jigsaw based project. Unable to continue.</h3>
    </div>
    <?php
    return;
endif;
?>

<aside class="doclist" id="<?php echo $project->getId(); ?>">
    <h3>Documents:</h3>
    <select multiple="multiple" size="30">
        <?php foreach ($documents as $id => $name) : ?>
            <option value="<?php echo $id ?>"><?php echo $name ?></option>
        <?php endforeach; ?>
    </select>
</aside>

<div id="documents">
    <div id="doc50" class="document" title="Basic dialog">
        <p>This is an animated dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.</p>
    </div>
</div>