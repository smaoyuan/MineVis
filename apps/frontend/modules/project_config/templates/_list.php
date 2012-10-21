<ul>
    <?php if($project_configs->count()>0): ?>
        <?php foreach ($project_configs as $project_config): ?>
    <li><?php echo $project_config->getTableA() . ' <==> ' . $project_config->getTableB()  ?></li>
        <?php endforeach; ?>
    <?php else: ?>
    <li>No Configuration mappings available.</li>
    <?php endif; ?>
</ul>