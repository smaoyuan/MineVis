<?php
$group_id = 0;
$current_target = 0;
?>
<h2>Chaining links List</h2>

<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Grouped Id</th>
            <th>Target bicluster</th>
            <th>Chaining</th>
            <th>Chaining link type</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($chaining_links as $chaining_link): ?>
            <tr>
                <td><a href="<?php echo url_for('bicluster_link/show?id=' . $chaining_link->getId()) ?>"><?php echo $chaining_link->getId() ?></a></td>

                <td><?php
        if ($current_target != $chaining_link->getTargetBiclusterId()) {
            $current_target = $chaining_link->getTargetBiclusterId();
            $group_id++;
        }
        echo $group_id;
            ?></td>
                <td><?php echo $chaining_link->getTargetBiclusterId() ?></td>
                <td><?php echo $chaining_link->getChainingId() ?></td>
                <td><?php echo $chaining_link->getChainingLinkTypeId() ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
