<?php

//Include detail here depending on mining type
if ($mining->getType() == 1) {
    $count =$mining->biClusterCount();
    echo '<div><strong>'.$count.' BiClusters Found</strong></div>' . "\n";
    if ($count > 1)
    echo link_to('Create a chaining based on this mining', 'chaining_new', array('mining' => $mining), array('class' => 'button'));


    if ($count < 5) {
          echo '<div>Try running a new mining with lower minimum cut off values to get more results</div>' . "\n";
    }
    if ($count > 0)
    include_partial('listResults', array('mining' => $mining));
} else {
    echo "Unrecognized Mining Type";
}

/**
 * Then include a list of the chainings and visualizations based on this mining for quick access....
 */
$chainings = $mining->getChainings();
$vises = $mining->getVisualizations();
?>

<div>
            <h3>Chainings based on this mining</h3>
            <?php
            if ($chainings->count() > 0):
                foreach ($chainings as $chain):
                    ?>
                    <ul>
                        <li><a href="<?php echo url_for('chaining/show?id=' . $chain->getId()) ?>"><?php echo $chain->getName() ?>
                            </a></li>
                    </ul>
                    <?php
                endforeach;
            else:
                ?>
                <p>No Chainings Available based on this mining.</p>
            <?php endif; ?>
        </div>
        <div>
            <h3>Vis based on this mining</h3>
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
                <p>No Visualizations Available based on this mining.</p>
            <?php endif; ?>
        </div>