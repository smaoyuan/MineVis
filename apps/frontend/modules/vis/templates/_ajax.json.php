<?php

/**
 * Get All the bics with that entity in their rows
 * @param type $entity_id Entity that must be contained
 * @param type $config Config we wanna search
 * @param type $mining_id Mining we're using
 * @return array list of bics
 */
function findMiningBicInRows($entity_id, $config, $mining_id) {
    $bics = array();
    /**
     * Get the list of
     */
    $raw_bics = Doctrine_Core::getTable('MiningBiCluster')->findByConfig($mining_id, $config->getId());
    /**
     * Go through the list and see which ones contain entity x
     */
    foreach ($raw_bics as $bic) {
        if ($bic->rowContains($entity_id)) {
            $bics[] = $bic;
        }
    }
    return $bics;
}

/**
 * Get all the bics with that entity in their columns
 * @param type $entity_id Entity that must be contained
 * @param type $config Config we wanna search
 * @param type $mining_id Mining we're using
 * @return array list of bics
 */
function findMiningBicInCols($entity_id, $config, $mining_id) {
    $bics = array();
    /**
     * Get the list of
     */
    $raw_bics = Doctrine_Core::getTable('MiningBiCluster')->findByConfig($mining_id, $config->getId());
    /**
     * Go through the list and see which ones contain entity x
     */
    foreach ($raw_bics as $bic) {
        if ($bic->columnContains($entity_id)) {
            $bics[] = $bic;
        }
    }
    return $bics;
}

//Connect to the database
$project = $vis->getProject();
$db = new MineVisDb($project->getExternalDatabase());

/** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 *
 *
 *
 *
 * Entity Search Tabs
 *
 *
 *
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
if ($type == "ent-search-autocomplete") {
    /**
     * Entity Search Autocomplete Request
     *
     * Note: IMPROVE
     * Dude to some design decisions there's no easy way to do this and to avoid
     * doing 50 queries i'll hack something together!
     * IT WILL BE ASSUMED:
     * - The chaining link type contains the name of all the entity type tables
     * - In each of these the id field is 'id'
     * - In each of these the descriptive name is the field 'name'
     * - This will be true if the dataset is improted using my jigsaw to mysql script
     */
    $data = array();
    //Get the entity types
    $chaining = $vis->getChaining();
    $types = $chaining->getLinkTypes();

    //call minevizdb...
    $data = $db->getEntitiesLike($term, $chaining);

    //return resutls
    echo json_encode($data);
} else if ($type == 'ent-search') {
    /**
     * We're searching for an entity or a string.
     */
    if ($term == $ent_name && $term != "") {
        /**
         * Find a project config with:
         *  - Current project id
         *  - Documents on one side
         *  - ent_type on the other side
         */
        $document_config = null;
        $row_configs = array();
        $col_configs = array();

        foreach ($project->getProjectConfig() as $conf) {
            /**
             * Entity could be in a bic row
             */
            if ($conf->getTableA() == $ent_type) {
                $row_configs[] = $conf;
            }
            /**
             * Entity could be in a bic col
             */
            if ($conf->getTableB() == $ent_type) {
                $col_configs[] = $conf;
            }
            /**
             * Entity could be in a document
             */
            if ($conf->getTableAxb() == "document_" . $ent_type or $conf->getTableAxb() == $ent_type . "_document") {
                $config = $conf;
            }
        }
        echo "<ul class='items'>\n";

        /**
         * Display a list of matching biclusters
         */
        echo "<li><span class='ui-icon ui-icon-folder-collapsed'></span>Biclusters<ul>\n";

        /**
         * Rows
         */
        foreach ($row_configs as $c) {
            $bics = findMiningBicInRows($ent_id, $c, $vis->getMiningId());
            foreach ($bics as $bic) {
                ?>
                <li><a class="item"><span class="ui-icon ui-icon-calculator"></span>Bicluster <?php echo $bic->getId() . " (" . $c->getTableAxb(); ?>)<span class="item_id"><?php echo $bic->getId(); ?></span><span class="item_type">bic</span></a></li>
                <?php
            }
        }

        /**
         * Cols
         */
        foreach ($col_configs as $c) {
            $bics = findMiningBicInCols($ent_id, $c, $vis->getMiningId());
            foreach ($bics as $bic) {
                ?>
                <li><a class="item"><span class="ui-icon ui-icon-calculator"></span>Bicluster <?php echo $bic->getId() . " (" . $c->getTableAxb(); ?>)<span class="item_id"><?php echo $bic->getId(); ?></span><span class="item_type">bic</span></a></li>
                <?php
            }
        }

        echo "</ul></li>\n";

        /**
         * Display a list of matching documents
         */
        $docs = $db->getDocumentsContaining($ent_id, $ent_type, $config);
        if (count($docs) > 0) {
            echo "<li><span class='ui-icon ui-icon-folder-collapsed'></span>Documents<ul>\n";
            foreach ($docs as $doc) :
                ?>
                <li><a class="item"><span class='ui-icon ui-icon-document'></span><?php echo $doc['name']; ?><span class="item_id"><?php echo $doc['id']; ?></span><span class="item_type">doc</span></a></li>
                <?php
            endforeach;
            echo "</ul></li>\n";
        } else {
            echo "<li>No document resutls for this query.</li>";
        }
        echo "</ul>\n";
    } else {
        ?>
        <p>Plain Text search since query is not an entity, results will only contain documents.</p>
        <ul class='items'>
            <li><span class='ui-icon ui-icon-folder-collapsed'></span>Documents<ul>
                    <?php
                    $data = $db->getDocumentMatching($term);
                    if (count($data) > 0) {
                        foreach ($data as $doc) :
                            ?>
                            <li><a class="item"><span class='ui-icon ui-icon-document'></span><?php echo $doc['name']; ?><span class="item_id"><?php echo $doc['id']; ?></span><span class="item_type">doc</span></a></li>
                            <?php
                        endforeach;
                    } else {
                        echo "<li>No resutls for this query. Hint: search is case sensitve.</li>";
                    }
                    ?>
                </ul></ul>
        <?php
    }
    /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     *
     *
     *
     *
     * Browser Tabs
     *
     *
     *
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
} else if ($type == 'entity_frequencies') {
    /*
     * Get entities for this mining and order by most frequent
     */
    $q = Doctrine_Core::getTable('EntityFrequency')->createQuery();
    $q->where('EntityFrequency.mining_id = ? AND EntityFrequency.bicluster_count <> 0', array($vis->getMiningId()))
            ->orderBy('EntityFrequency.bicluster_count DESC');
    $entities = $q->execute();

    if (count($entities) > 0) {
        echo "<ul class='items'>\n";
        foreach ($entities as $ent_fq) {
            $json = json_encode(array($ent_fq->getEntityName(), $ent_fq->getEntityType(), $ent_fq->getEntityId()));
            echo "<li><a class=\"item\"><span class='ui-icon ui-icon-contact'></span>Entity \"" . $ent_fq->getEntityName() . "\" (" . $ent_fq->getEntityType() . ") was found in " . $ent_fq->getBiclusterCount() . " biclusters<span class='item_id'>" . $json . "</span><span class='item_type'>ent</span></a></li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p>No entities found, make sure you ran the Entity Frequencies Generating on the mining page.</p>";
    }
} else if ($type == 'browse_bic') {
    /**
     * Browse Biclusters
     */
    $mining = $vis->getMining();
    include_partial('mining/listResults', array('mining' => $mining));
} else if ($type == 'browse_doc') {
    /**
     * Browse Documents
     */
    $documents = $db->getDocumentList();
    echo "<ul class='items'>\n";
    foreach ($documents as $id => $name) {
        echo "<li><a class=\"item\"><span class='ui-icon ui-icon-document'></span>Document " . $id . ": " . $name . "<span class='item_id'>" . $id . "</span><span class='item_type'>doc</span></a></li>\n";
    }
    echo "</ul>\n";
} else if ($type == 'browse_chain_link') {
    /**
     * Browse Links
     */
    $chaining = $vis->getChaining();
    $linkTypes = $chaining->getLinkTypes();
    $links = $chaining->getLinks();
    echo "<ul class='items'>\n";
    if (count($links) > 0) :
        foreach ($linkTypes as $type) :
            ?>
            <li><span class="ui-icon ui-icon-folder-collapsed"></span>Links starting on <?php echo $type->getName() ?>
                <ul>
                    <?php
                    $links = $type->getLinks();
                    if (count($links) > 0) :
                        foreach ($links as $link) :
                            ?>
                            <li>
                                <a class="item"><span class="ui-icon ui-icon-link"></span>Link <?php echo $link->getId() ?> Origin BiCluster: <?php echo $link->getTargetBiclusterId() ?><span class="item_id"><?php echo $link->getId() ?></span><span class="item_type">link</span></a>
                            </li>
                            <?php
                        endforeach;
                    else :
                        echo "<li>No links of this type.</li>";
                    endif;
                    ?>
                </ul>
            </li>
            <?php
        endforeach;
    endif;
    echo "</ul>\n";
    /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     *
     *
     *
     *
     * Previews
     *
     *
     *
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
} else if ($type == 'preview_doc') {
    /**
     * Preview a document
     */
    $document = $db->getDocument($ent_id);
    $document['type'] = 'document';
    ?>
    <div><strong>Document <?php echo $document['id']; ?>:</strong> <?php echo $document['name']; ?></div>
    <script type="text/javascript" charset="utf-8">
        var preview_data = <?php echo json_encode($document) ?>;
    </script>
    <p><?php echo nl2br($document['text']); ?></p>
    <?php
} else if ($type == 'preview_bic') {
    /**
     * Preview a bicluster
     */
    $bic = Doctrine_Core::getTable('MiningBiCluster')->find(array($ent_id));
    ?>
    <div><strong>Bicluster <?php echo $ent_id; ?>:</strong></div>
    <script type="text/javascript" charset="utf-8">
        var preview_data = <?php echo $bic->getMiniJSON(); ?>;

        $(document).ready(function() {
            miniBiClusterVis(preview_data,'preview_vis');
        });
    </script>
    <div id="preview_vis"></div>
    <?php
} else if ($type == 'preview_link') {
    /**
     * Preview a Chain Link
     */
    $link = Doctrine_Core::getTable('ChainingLink')->find(array($ent_id));
    $json = $link->getJSON();
    ?>
    <div><strong>Link <?php echo $ent_id; ?>:</strong></div>
    Origin Bicluster: <?php echo $link->getTargetBiclusterId(); ?>
    <script type="text/javascript" charset="utf-8">
        var preview_data = <?php echo $json; ?>;

        $(document).ready(function() {
            biClusterLinkVis(preview_data, 'preview_vis');
        });
    </script>
    <div id="preview_vis"></div>
    <?php
    /** - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
     *
     *
     *
     *
     * In workspace graph context menu actions
     *
     *
     *
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
} else if ($type == 'show_bic_docs') {
    /**
     * Show the bicluster's documents
     */
    $documents = array();
    $bicluster = Doctrine_Core::getTable('MiningBiCluster')->find(array($ent_id));

    if (!is_null($bicluster)) {
        $id_list = $bicluster->getDocuments();
        foreach ($id_list as $doc_id) {
            $documents[] = $db->getDocument($doc_id);
        }
    }
    echo json_encode($documents);
} else if ($type == 'show_bic_links') {
    /**
     * Show bicluster's links
     */
    $biclusters = array();
    $chaining_id = $vis->getChainingId();

    $links = Doctrine_Core::getTable('ChainingLink')->findByDql(
            'target_bicluster_id = ? AND chaining_id = ?', array($ent_id, $chaining_id)
    );
    foreach ($links as $link) {
        foreach ($link->getDestinations() as $dest) {
            $biclusters[] = $dest->getDestinationBiCluster()->getMiniRaw();
        }
    }

    echo json_encode($biclusters);
} else if ($type == 'show_doc_bics') {
    /**
     * Show Document's Biclusters
     */
    $biclusters = array();
    $q = Doctrine_Core::getTable('DocumentLink')->createQuery();
    $q->where('DocumentLink.document_id = ? AND DocumentLink.mining_id = ?', array($ent_id, $vis->getMiningId()));
    $q->limit(15);
    $doclinks = $q->execute();

    foreach ($doclinks as $link) {
        $bic = $link->getMiningBiCluster();
        $biclusters[] = $bic->getMiniRaw();
    }

    echo json_encode($biclusters);
} else {
    /**
     *
     *
     * Unknown request type.
     *
     *
     */
    $error_msg = array(array('label' => "unknown type error: '" . $type . "'", 'category' => 'Errors'));
    echo json_encode($error_msg);
}
?>
