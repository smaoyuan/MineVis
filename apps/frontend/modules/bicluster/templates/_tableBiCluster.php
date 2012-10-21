<?php
function isCellInRelationship($relationships, $row, $col) {
    //return $relationships[$row][$col] == true;
    if ($relationships->offsetExists($row)) {
        if($relationships[$row]->offsetExists($col))
            return true;
    }
    return false;
}

function isCellInBiCluster($bicluster, $row, $col) {
    if (isset($bicluster[0][$row]) and isset($bicluster[1][$col])) {
        return true;
    }
    return false;
}
?>

<table class="biClusterTable">
        <thead>
            <tr>
                <th>Row Name/Col Name</th>
                <?php foreach ($columns as $col) : ?>
                <th><?php echo $col[1] ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row) : ?>
            <tr>
                <td><?php echo $row[1] ?></td>
                    <?php foreach ($columns as $col) : ?>
                <td <?php echo (isCellInBiCluster($bicluster, $row[0],$col[0]) ? "class='biClusterCell'" : '') ?>>
                            <?php echo (isCellInRelationship($relationships, $row[0],$col[0]) ? "X" : "&nbsp;") ?>
                </td>
                <td>test123</td>
                    <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>