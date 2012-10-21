<h2>Manually Running Job: <?php echo $config->getTableAxb() ?> for
    <?php echo $mining->getName() ?></h2>

<h3>Information:</h3>
<table>
    <tbody>
        <tr>
            <th>Id:</th>
            <td><?php echo $mining_job_mapping->getId() ?></td>
        </tr>
        <tr>
            <th>Mining:</th>
            <td><?php echo $mining->getName() ?></td>
        </tr>
        <tr>
            <th>Config:</th>
            <td><?php echo $config->getTableAxb() ?></td>
        </tr>
        <tr>
            <th>Start time:</th>
            <td><?php echo $mining_job_mapping->getStartTime() ?></td>
        </tr>
        <tr>
            <th>End time:</th>
            <td><?php echo $mining_job_mapping->getEndTime() ?></td>
        </tr>
    </tbody>
</table>

<h3>Input:</h3>
Charm Format Input:
<?php echo 'Path: ' . $input_file. '<br>' ?>
<code>
    <?php
    $file = fopen($input_file, "r") or exit("Unable to open file!");
    while(!feof($file)) {
        echo fgets($file) . '<br>';
    }
    ?>
</code>
Console Output:<br/>
<code>
    <?php
    foreach($output as $line) {
        echo($line . "<br/>");
    }
    ?>
</code>

<h3>Results:</h3>
<?php echo 'Path: ' . $output_file. '<br>' ?>
<code>
    <?php
    $file = fopen($output_file, "r") or exit("Unable to open file!");
    while(!feof($file)) {
        echo fgets($file) . '<br>';
    }
    ?>
</code>
If there is no clusters above it's because the this mining didn't find any.