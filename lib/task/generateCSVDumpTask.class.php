<?php

/*
 * This is a task to dump the resutls of a mining and a chaining
 * into CSV files.
 */

class generateCSVDumpTask extends sfBaseTask {

    private $bic_types;
    private $link_types;
    private $bic_cache;
    private $db;

    /**
     *
     */
    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('path', sfCommandArgument::REQUIRED, 'Path for output'),
            new sfCommandArgument('mining_id', sfCommandArgument::REQUIRED, 'Mining Id'),
            new sfCommandArgument('chaining_id', sfCommandArgument::REQUIRED, 'Chaining Id'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        ));

        $this->namespace = 'minevis';
        $this->name = 'generateCSVDump';
        $this->briefDescription = 'Dumps a set of biclusters and links to csv files.';
        $this->detailedDescription = <<<EOF
The [generateCSVDump|INFO] task takes a file path, a mining and a chaining and
dumps 2 csv files one with the biclusters and one with the links.
It will create 2 the following 2 files:
mining_#id.csv
chaining_#id.csv
Call it with:

  [php symfony minevis:runChaining #output_path #mining_id #chaining_id|INFO]
EOF;
    }

    /**
     *
     * @param type $arguments
     * @param type $options
     * @return type
     */
    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $this->bic_types = array();
        $this->link_types = array();
        $this->bic_cache = array();

        // check arguments
        $path = $arguments['path'];
        $this->logSection('output path', 'path: ' . $path);
        $mining_id = $arguments['mining_id'];
        $this->logSection('loading', 'mining_id: ' . $mining_id);
        $chaining_id = $arguments['chaining_id'];
        $this->logSection('loading', 'chaining_id: ' . $chaining_id);
        $this->log('running from: ' . $_SERVER['PHP_SELF']);

        //make sure that arguments are valid:
        if (!file_exists($path)) {
            $this->logSection('Invalid Path', 'The given path does not exsist', null, 'ERROR');
            return; //Fail task here
        }
        $mining = Doctrine_Core::getTable('Mining')->find(array($mining_id));
        if (!$mining) {
            $this->logSection('Invalid mining_id', 'The given mining does not exsist', null, 'ERROR');
            return; //Fail task here
        }
        $this->log('loaded chaining: ' . $mining->getName());

        $chaining = Doctrine_Core::getTable('Chaining')->find(array($chaining_id));
        if (!$chaining) {
            $this->logSection('Invalid chaining_id', 'The given chaining does not exsist', null, 'ERROR');
            return; //Fail task here
        }
        $this->log('loaded chaining: ' . $chaining->getName());

        /**
         * Set up connection to raw db to get labels
         */
        $project = $mining->getProject();
        $this->db = new MineVisDb($project->getExternalDatabase());

        /*
         * Preload biclusters types to reduce sql queries
         */
        $this->logSection('loading', 'bicluster types', null);
        $temp_types = $mining->getProject()->getProjectConfig();
        foreach ($temp_types as $type) {
            $this->bic_types[$type->getId()] = array();
            $this->bic_types[$type->getId()][0] = $type->getTableA();
            $this->bic_types[$type->getId()][1] = $type->getTableB();
            $this->bic_types[$type->getId()][2] = $type;
        }

        /*
         * Dump the biclusters
         */
        $file_path = $path . '/mining_' . $mining_id . '.csv';
        $file = fopen($file_path, 'w') or $this->logSection('Dump Failed', 'Could not open bic dump file', null, 'ERROR');

        $this->logSection('dumping', 'biclusters: ', null);
        fwrite($file, "BiCluster Id, Row Type, Array of Rows, Column Type, Array of Columns\n");

        $biclusters = $mining->getBiClusters();
        $count = 0;
        $status = 0;

        foreach ($biclusters as $bic) {
            fwrite($file, $this->biclusterToString($bic) . "\n");
            $count++;
            $status++;
            if ($status > 500) {
                $status = 0;
                $this->log("dumped $count..");
            }
        }
        fwrite($file, "\n");

        fclose($file);
        $this->log('done (dumped ' . $count . ' biclusters)');

        /*
         * Preload link types to reduce sql queries
         */
        $this->logSection('loading', 'link types', null);
        $temp_types = $chaining->getLinkTypes();
        foreach ($temp_types as $type) {
            $this->link_types[$type->getId()] = $type->getName();
        }

        /*
         * Dump the links
         */
        $this->logSection('dumping', 'links: ', null);
        $file_path = $path . '/chaining_' . $chaining_id . '.csv';
        $file = fopen($file_path, 'w') or $this->logSection('Dump Failed', 'Could not open link dump file', null, 'ERROR');

        fwrite($file, "Link Id, Link Type, Distance, Source Id, Row Type, Source Rows, Column Type, Source Columns, Destination Id, Row Type, Destination Rows, Column Type, Destination Columns\n");

        $links = $chaining->getLinks();
        $count = 0;
        $status = 0;
        $line_count = 0;

        foreach ($links as $link) {
            $source = $link->getTargetBiCluster();
            $dests = $link->getDestinations();
            foreach ($dests as $dest) {
                fwrite($file, $link->getId() . ',' . $this->getLinkType($link) . ',' . $dest->getDistance() . ','
                        . $this->biclusterToString($source) . ','
                        . $this->biclusterToString($dest->getDestinationBiCluster()) . "\n");
                $line_count++;
            }
            $count++;
            $status++;
            if ($status > 500) {
                $status = 0;
                $this->log("dumped $count..");
            }
        }

        fclose($file);
        $this->log('done (dumped ' . $count . ' links, ' . $line_count . ' total lines expended)');


        $this->logSection('Complete', 'Dump completed', null);
    }

    /**
     * Returns a string describing the bicluster in this format
     * Bicluster id, Rows, Columns (3 fields always)
     * BiC_id,"row1,row2,...","col1,col2,..."
     *
     * This also caches the resutls when it parses them the first time so that
     * when linking it doesn't have to query the database again!
     *
     * @param type $bic bicluster object
     */
    private function biclusterToString($bic) {
        $str = '';
        if (array_key_exists($bic->getId(), $this->bic_cache)) {
            $str = $this->bic_cache[$bic->getId()];
        } else {
            //get types
            $type = $this->getBiClusterType($bic);
            $config = $this->bic_types[$bic->getConfigId()][2];

            // Compile the rows
            $row_labels = array();
            $row_ids = array();
            foreach ($bic->getRows() as $row) {
                $row_ids[$row->getRowId()] = $row->getRowId();
            }
            $rows = $this->db->getTableADescriptionWhere($config, $row_ids);
            foreach ($rows as $r) {
                $row_labels[$r[0]] = $r[1];
            }

            // Compile the colums
            $col_labels = array();
            $col_ids = array();
            foreach ($bic->getCols() as $col) {
                $col_ids[$col->getColId()] = $col->getColId();
            }
            $columns = $this->db->getTableBDescriptionWhere($config, $col_ids);
            foreach ($columns as $c) {
                $col_labels[$c[0]] = $c[1];
            }

            $str = $bic->getId() . ',';
            $str .= $type[0] . ',"';
            foreach ($row_labels as $row) {
                $str .= $row . ",";
            }
            $str .= '",';
            $str .= $type[1] . ',"';
            foreach ($col_labels as $col) {
                $str .= $col . ",";
            }
            $str .= '"';
            $this->bic_cache[$bic->getId()] = $str;
        }
        return $str;
    }

    /**
     * This just goes through the types and pulls out the one with matching ID.
     * @param Array $types
     * @param ChainingLink $link
     * @return string
     */
    private function getLinkType($link) {
        return $this->link_types[$link->getChainingLinkTypeId()];
    }

    /**
     * This goes through the type and returns the correct one.
     * @param array $btypes cached types
     * @param MiningBiCluster $bic bicluster to lookup type for
     * @return array where 0 is row type and 1 is col type
     */
    private function getBiClusterType($bic) {
        return $this->bic_types[$bic->getConfigId()];
    }

}
