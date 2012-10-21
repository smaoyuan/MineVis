<?php
$vis_count = 0;
$vises = $chaining->getVisualizations();

/**
 * This display the raw data straight from the output dump files.
 *
 * Why is it here? I originaly wrote it before they got parsed into the db,
 * rather than delete it i commented it out to make it useful as debug.
 */
function displayRawData($chaining) {
    $jobs = $chaining->getLinkTypes();
    $job = 0;
    foreach ($jobs as $job) {
        $json = file_get_contents(sfConfig::get('sf_root_dir') . '/minetree/chaining_result_linktype_id_' . $job->getId() . '.json');
        $data = json_decode($json)->LinkingResult;
        echo '<h1>Job ' . $job->getId() . '</h1>';
        $clusters = 0;
        $links_total = 0;
        echo '<div class="togglebox"><div class="toggledetails">';
        foreach ($data as $id => $row) {
            $clusters++;
            $links_total+= count($row);
            echo '<div><strong>Cluster Id ' . $id . ' is related to the folowing:</strong><ul class="plain">';
            foreach ($row as $link) {
                echo '<li>LINK: Target ' . $id . ' Destination ' . $link . '</li>';
            }
            //var_dump($row);
            echo "</ul></div>";
        }
        echo "</div></div>";
        echo 'Found links for ' . $clusters . ' clusters <br/>';
        echo 'With an average of ' . $links_total / $clusters . ' links each.';
    }
}

//This is for debug only
//displayRawData($chaining);

$linkTypes = $chaining->getLinkTypes();
$links = $chaining->getLinks();
?>
<strong>Total links found: </strong> <?php echo count($links) ?>
<?php
if (count($links) > 0) :
    foreach ($linkTypes as $type) :
        ?>
        <h3>Links starting on <?php echo $type->getName() ?></h3>

            <?php
            $links = $type->getLinks();
            if (count($links) > 0) :
                ?>
        See Details<div class="collapsible">
            <?php foreach ($links as $link) : ?>
                <div>
                    <strong><?php echo 'Target BiCluster: ' . $link->getTargetBiclusterId() ?></strong>
                    <?php echo link_to('View Link ' . $link->getId(), 'bicluster_link_vis', array('id' => $link->getId()), array('class' => 'vis_box', 'rel' => 'group' . $type->getName(), 'title' => 'BiC. Link id ' . $link->getId())); ?>
                </div>
            <?php endforeach; ?>
            </div>
            <?php else :
                echo "<p>No links of this type.</p>";
            endif;
            ?>

        <?php
    endforeach;
endif;
?>
<div>
        <h3>Vis based on this Chaining</h3>
        <?php
        if ($vises->count() > 0):
            foreach ($vises as $vis):
                ?>
                <ul>
                    <li><a href="<?php echo url_for('vis/show?id=' . $vis->getId()) ?>"><?php echo $vis->getName() ?>
                        </a></li>
                </ul>
                <?php
            endforeach;
        else:
            ?>
            <p>No Visualizations Available based on this Chaining.</p>
    <?php endif; ?>
            <a href="<?php echo url_for('vis/new') ?>" class="button">Create New Vis</a>
    </div>
