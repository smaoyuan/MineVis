<?php

/**
 * Cover Tree Chaining Helper
 */
class MineTree {

    private $java_path = './lib/algorithms/minetree/';
    private $data_path = './cache/algorithms/minetree/';
    private $linkType = null;
    private $neighbors = 10;
    private $threshold = 1.0;
    private $inputArff = '';
    private $inputJson = '';
    private $outputJson = '';

    /**
     *
     * @param type $ChainingLinkType
     */
    public function __construct($ChainingLinkType) {
        $this->linkType = $ChainingLinkType;
        $id = $this->linkType->getId();
        $this->inputArff = 'chaining_data_linktype_id_' . $id . '.arff';
        $this->inputJson = 'chaining_data_linktype_id_' . $id . '.json';
        $this->outputJson = 'chaining_result_linktype_id_' . $id . '.json';
    }

    /**
     * Sets up the parameters to use when running the algorithm
     * @param int $ncount number of neighbors to get, 10 , 20 ...
     * @param int $thres threshold to use on limiting what a neighbor is
     */
    public function setParameters($ncount, $thres) {
        $this->neighbors = $ncount;
        $this->threshold = $thres;
    }

    /**
     * Returns the command line to use.
     * @return String command line to start program
     */
    public function getCmd() {
        return 'java -jar ' . $this->java_path . 'MineVisCoverTree.jar  '
                . $this->data_path . $this->inputArff . ' '
                . $this->data_path . $this->inputJson . ' '
                . $this->data_path . $this->outputJson . ' '
                . $this->neighbors . ' ' . $this->threshold;
        // inputARFFPath inputJSONPath outputJSONPath kneighbors overlap
    }

    /**
     * Starts running the data assuming the input exists
     */
    public function run($params = null) {
        if ($params != null) {
            $this->neighbors = $params['max_neighbors'];
            $this->threshold = $params['distance_threshold'];
        }
        $cmd = $this->getCmd();

        echo $cmd . "\n";
        exec($cmd);
        echo 'algorithm returned...' . "\n";
    }

    /**
     * Prases the output
     */
    public function parseOutput() {
        if (file_exists($this->data_path . $this->outputJson)) {
            echo "output file exists\n";
            //load data
            $json = file_get_contents($this->data_path . $this->outputJson);
            $data = json_decode($json)->LinkingResult;
            if ($data) {
                echo "output file loaded, begining to parse...\n";
                //prase each target cluster
                foreach ($data as $id => $target) {
                    //add a link entry for each destination cluster link
                    $first_link_dest = true;
                    $link = new ChainingLink();
                    $link->setChainingId($this->linkType->getChainingId());
                    $link->setChainingLinkTypeId($this->linkType->getId());
                    $link->setTargetBiclusterId($id);
                    //$link->save();
                    //echo "Parsing target:\n";
                    //echo $id . "\n";
                    foreach ($target as $destId) {
                        //avoid linking to itself... like we need that anyways
                        if ($destId[0] <> $id) {
                            if ($first_link_dest) {
                                //only save the link if there is at least
                                //on destination.
                                $first_link_dest = false;
                                $link->save();
                            }
                            //set up and save a destination here
                            $dest = new ChainingLinkDestination();
                            $dest->setChainingLinkId($link->getId());
                            $dest->setDestinationBiclusterId($destId[0]);
                            $dest->setDistance($destId[1]);
                            $dest->save();
                        } else {
                            echo "skipping itself form insertion, note: consider pruning self from list in java...\n";
                        }
                    }
                    //echo "target done loading next ....\n";
                }
            } else {
                echo "ERROR: Output file could not be json decoded.";
            }
            echo "done parsing.";
        } else {
            echo "ERROR: Output file could not be found!\n";
            echo "\tFile: " . $this->data_path . $this->outputJson . "\n";
        }
    }

    /**
     * Generates output needed to run alg
     * @param type $forceOverwrite
     */
    public function generateInput($forceOverwrite = false) {

        $id = $this->linkType->getId();
        $arffFilePath = $this->inputArff;
        $jsonFilePath = $this->inputJson;
        $arffFile = null;
        $jsonFile = null;

        /**
         * If can't overwrite files set up flag.
         */
        if ((file_exists($this->data_path . $arffFilePath)) and $forceOverwrite == false) {
            $arffFilePath = false;
        }
        if ((file_exists($this->data_path . $jsonFilePath)) and $forceOverwrite == false) {
            $jsonFilePath = false;
        }

        /*
         * Open Streams
         */
        if ($arffFilePath != false) {
            $arffFile = fopen($this->data_path . $arffFilePath, 'w') or $out[] = "Can't Write to intput file: " . $this->path . $arffFilePath;
            fwrite($arffFile, "% Bicluster Mapping\n"
                    . "@RELATION " . $this->linkType->getName() . "\n"
                    . "@ATTRIBUTE biCId  NUMERIC\n" // id
                    . "@ATTRIBUTE biCOr  {Row,Col}\n\n" // orientation
                    . "@DATA\n\n"
            );
        }
        if ($jsonFilePath != false) {
            $jsonFile = fopen($this->data_path . $jsonFilePath, 'w') or $out[] = "Can't Write to intput file: " . $this->path . $jsonFilePath;
        }

        /*
         * Get data and output it.
         */
        $mid = $this->linkType->getChaining()->getMiningId();
        $fieldName = $this->linkType->getName();
        $distData = array();

        $q = Doctrine_Core::getTable('MiningBiCluster')
                ->createQuery('b')
                ->innerJoin('b.ProjectConfig p')
                ->where('b.mining_id=' . $mid . ' AND ( '
                . 'p.table_a = \'' . $fieldName . '\''
                . ' OR '
                . 'p.table_b = \'' . $fieldName . '\''
                . ' )');
        //echo $q;
        $biclusters = $q->execute();
        foreach ($biclusters as $bic) {
            //load it's rows or colums and store them to the data file
            $config = $bic->getProjectConfig();
            $ids = array();


            if ($fieldName == $config->getTableA()) {
                //add it to the arff steam for covertree
                if ($arffFilePath != false) {
                    fwrite($arffFile, $bic->getId() . ",Row\n");
                }
                $rows = $bic->getRows();
                foreach ($rows as $r) {
                    $ids[] = $r->getRowId();
                }
            } else {
                //add it to the arff steam for covertree
                if ($arffFilePath != false) {
                    fwrite($arffFile, $bic->getId() . ",Col\n");
                }
                $cols = $bic->getCols();
                foreach ($cols as $r) {
                    $ids[] = $r->getColId();
                }
            }
            $distData[$bic->getId()] = $ids;
        }
        //var_dump(json_encode($distData));
        if ($jsonFilePath != false) {
            fwrite($jsonFile, json_encode($distData));
        }

        /*
         * Close Steams
         */
        if ($arffFilePath != false) {
            fclose($arffFile);
        }
        if ($jsonFilePath != false) {
            fclose($jsonFile);
        }
    }

}

?>
