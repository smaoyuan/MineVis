<?php
$vis = $visualization;

$project = $vis->getProject();
$db = new MineVisDb($project->getExternalDatabase());

/**
 * Prepare json data
 * turn save data into load data by added all needed docsuments/bics
 */
$save = $sf_data->getRaw('visualization')->getJsondata();
if ($save == '') {
    $save = "false";
} else {
    // Load the object
    $save = json_decode($save);

    // Add documents
    for ($i = 0; $i < count($save->documents); $i++) {
        $document = $db->getDocument($save->documents[$i]->id);
        $document['type'] = 'document';
        $save->documents[$i]->data = $document;
    }

    // Add Biclusters
    for ($i = 0; $i < count($save->biclusters); $i++) {
        $bic = Doctrine_Core::getTable('MiningBiCluster')->find(array($save->biclusters[$i]->id));
        //$document['type'] = 'bicluster';
        $save->biclusters[$i]->data = $bic->getMiniRaw();
        ;
    }

    // Ready for JS
    $save = json_encode($save);
}
/**
 * Javascript
 */
use_javascript('jquery.contextMenu.js'); // Right Click Menus
use_javascript('raphael.js'); // Graph
use_javascript('minegraph.js'); // MineGraph framework
use_javascript('vis.js'); // Search stuff
/**
 * CSS
 */
use_stylesheet('fullscreen.css');
//use_stylesheet('lhrd.css'); //TODO remove after implementation of lhrd mode
use_stylesheet('jquery.contextMenu.css');
?>
<script type="text/javascript" charset="utf-8">
    /* Vis id */
    var vis_id = <?php echo $vis->getId(); ?>;
    var vis_data = <?php echo $save; ?>;

</script>

<h2><?php echo $vis->getName() ?> Visualization Workspace</h2>

<nav class="secondary">
    <?php
    echo link_to("Go to Project", 'project/show?id=' . $vis->getProjectId());
    echo link_to("Go to Mining", 'mining/show?id=' . $vis->getMiningId());
    echo link_to("Go to Chaining", 'chaining/show?id=' . $vis->getChainingId());
    echo link_to("Back to Vis list", 'vis/index');
    ?><a id="lhrd" href="#lhrd">LHRD Mode</a><a id="save" href="#save">Save Vis</a><?php
    echo link_to("Edit Vis", 'vis/edit?id=' . $visualization->getId());
    echo link_to('Delete Vis', 'vis/delete?id=' . $visualization->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) . "\n";
    ?>
</nav>
<?php
/*
 * Display nothing if not a jigsaw set
 */
if ($project->getJigsawBased() == false) {
    echo "<p>This project is not jigsaw based</p>";
    return;
}

/*
 * Check that both DocumentLink and EntityFrequencies have been
 * generated...
 */
$mining = $vis->getMining();
if ($mining->getDocumentLinkStatus() == 0) {
    echo "<p>Note: the document linking process hasn't run on the current mining. Show BiClusters on documents will not work until it has ran.</p>";
    echo link_to('Generate Document Links', 'mining_run_documentlink', array('id' => $mining->getId()), array('class' => 'button'));
}
if ($mining->getEntityFrequencyStatus() == 0) {
    echo "<p>Note: the entity frequncy index hasn't been generated on the current mining. The frequency tab will be empty until it has ran.</p>";
    echo link_to('Generate Entity Frequencies', 'mining_run_entityfrequency', array('id' => $mining->getId()), array('class' => 'button'));
}

?>

<div id="workspace">
    <h3>Workspace:</h3>
    <div id="resize">
        Workspace graph size:
        <a href="#12_6">1200 x 600</a>
        <a href="#24_6">2400 x 600</a>
        <a href="#24_9">2400 x 900</a>
        <a href="#36_9">3600 x 900</a>
        <a href="#76_32">7680 x 3200</a>
    </div>
    <div id="graph"></div>
</div>
<div id ="browser">
    <ul>
        <li><a href="#searcha">Simple Search</a></li>
        <li><a href="#frequency">Frequency List</a></li>
        <li><a href="#browsebic">Browse Bics</a></li>
        <li><a href="#browsedoc">Browse Docs</a></li>
        <li><a href="#browsechain">Browse Links</a></li>      
    </ul>
    <div id="searcha">
        <h3>Simple Search:</h3>
        <form id="ent_search">
            <input type="text" title="entity search" id="ent_query" size ="40"/>
            <input type="hidden" id="ent_name" />
            <input type="hidden" id="ent_id" />
            <input type="hidden" id="ent_type" />
            <button value="Search" type="submit" name="gobutton"> Search </button>
        </form>
        <div id="ent_results">

        </div>
    </div>

    <div id="frequency">
        <h3>Frequency List:</h3>
        <div id="f_ents" class="loading-results">
            . . . Loading data
        </div>
    </div>

    <div id="browsebic">
        <h3>Bic Browser:</h3>
        <div id="b_bics" class="loading-results">
            . . . Loading data
        </div>
    </div>

    <div id="browsedoc">
        <h3>Document Browser:</h3>
        <div id="b_docs" class="loading-results">
            . . . Loading data
        </div>
    </div>

    <div id="browsechain">
        <h3>Link Browser:</h3>
        <div id="b_chains" class="loading-results">
            . . . Loading data
        </div>
    </div>
</div>
<div id="preview">
    <h3>Preview</h3>
    <a id="add_to_workspace" class="button" href="#check">Add to workspace</a>
    <div class="content">

    </div>
</div>

<div id="file" class="clearfix">
    Created on: <?php echo $visualization->getCreatedAt() ?>,
    Last save on <span><?php echo $visualization->getUpdatedAt() ?></span>
</div>

<ul id="bicMenu" class="contextMenu">
    <li class=""><a href="#link">Link to...</a></li>
    <li class=""><a href="#docs">Show Docs</a></li>
    <li class=""><a href="#links">Show Bic Links</a></li>
<!--     <li class="highlight"><a href="#highlight">Highlight Bic</a><li> -->
    <li class=" separator"><a href="#close">Close</a></li>
</ul>

<!-- menu for thin bicluster -->
<ul id="thinBicMenu" class="contextMenu">
    <li class=""><a href="#link">Link to...</a></li>    
    <li class=" separator"><a href="#close">Close</a></li>
</ul>

<!-- menu for grid in each bicluster -->
<ul id="gridMenu" class="contextMenu">
    <li class=""><a href="#thinBicByRow">Thin Bic by Row</a></li>
    <li class=""><a href="#thinBicByCol">Thin Bic by Col</a></li>    
</ul>

<!-- menu for grid in each thin bicluster -->
<ul id="thinBicGridMenu" class="contextMenu">
    <li class=""><a href="#showEntity">Show Entity</a></li>   
</ul>

<ul id="docMenu" class="contextMenu">
    <li class=""><a href="#link">Link to..</a></li>
    <li class=""><a href="#bics">Show Bics</a></li>
    <li class=" separator"><a href="#close">Close</a></li>
</ul>
<ul id="linkMenu" class="contextMenu">
    <li class=""><a href="#up">Highlight</a></li>
    <li class=" separator"><a href="#close">Close</a></li>
</ul>




