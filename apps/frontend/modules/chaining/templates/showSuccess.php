<?php
$pid = $chaining->getProcessId();
$mining = $chaining->getMining();
$running = false;
exec("ps " . $pid, $output, $result);
if (count($output) >= 2) {
    $running = true;
}
?>

<h2>Chains Set <?php echo $chaining->getName() ?></h2>
<nav class="secondary">
    <?php
    echo link_to("Go to Project", 'project/show?id=' . $mining->getProjectId());
    echo link_to("Go to " . $mining->getName() . " Mining", 'mining/show?id=' . $mining->getId());
    echo link_to("Go to Chaining list", 'chaining/index');
    echo link_to("Edit Chaining", 'chaining/edit?id=' . $chaining->getId());
    echo link_to('Delete Chaining', 'chaining/delete?id=' . $chaining->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) . "\n";
    ?>
</nav>

<h3>Info:</h3>
<table>
    <tbody>
        <tr>
            <th>Param:</th>
            <td><?php echo $chaining->paramsToString() ?></td>
        </tr>
        <tr>
            <th>Status:</th>
            <td><?php
    echo ($chaining->getComplete() ? "Complete" :
            ($chaining->getStarted() ? "Running" :
                    ($chaining->getConfigured() ? "Configured" : "Created")));
    ?></td>
        </tr>
        <tr>
            <th>Created at:</th>
            <td><?php echo $chaining->getCreatedAt() ?></td>
        </tr>
        <tr>
            <th>Updated at:</th>
            <td><?php echo $chaining->getUpdatedAt() ?></td>
        </tr>
    </tbody>
</table>
<div><strong>Process ID:</strong> <?php echo $pid . ($running ? " (running)" : " (not running)") ?></div>

<h3>Results:</h3>
<?php
if ($chaining->getComplete()) :
    include_partial('complete', array('chaining' => $chaining));
elseif ($chaining->getStarted()) :
    include_partial('running', array('chaining' => $chaining));
else : //elseif ($chaining->getConfigured()) :
    include_partial('configuration', array('chaining' => $chaining));
endif;
?>






