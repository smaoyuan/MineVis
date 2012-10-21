<?php

class generateEntityFrequenciesTask extends sfBaseTask {

    private $microtime_start = null;

    /**
     * Trying to make doc stuff work...
     * @var Mining mining
     */
    private $mining;

    /**
     * This is a helper function to get the runtime of functions,
     * It's designed to be called multiple time, each time it returns
     * the difference from the last time it was called.
     *
     * @staticvar string $microtime_start last call memory
     * @return float microtime interval
     */
    private function get_time_interval() {
        if ($this->microtime_start === null) {
            $this->microtime_start = microtime(true);
            return 0.0;
        }
        $now = microtime(true);
        $dif = $now - $this->microtime_start;
        $this->microtime_start = $now;
        return $dif;
    }

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('mining_id', sfCommandArgument::REQUIRED, 'Mining to generate links for'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
                // add your own options here
        ));

        $this->namespace = 'minevis';
        $this->name = 'generateEntityFrequencies';
        $this->briefDescription = 'Generate the frequence of each entity\'s bicluster occurence';
        $this->detailedDescription = <<<EOF
The [generateEntityFrequencies|INFO] goes through al the entities, figures out how many times it occurres
    in biclusters and stores that...

Call it with:

  [php symfony generateEntityFrequencies mining_id|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = Doctrine_Manager::connection();
        //$connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        //var_dump( $databaseManager->getDatabase($options['connection'])->getConnection());
        //get mining argument
        $mining_id = $arguments['mining_id'];
        $this->logSection('loading', 'mining_id: ' . $mining_id);

        //path
        $this->log('  running from: ' . $_SERVER['PHP_SELF']);

        //load mining
        $this->mining = Doctrine_Core::getTable('Mining')->find(array($mining_id));
        if ($this->mining == null) {
            $this->logSection("Invalid Mining Id", "The mining with id " . $mining_id . " could not be found in the databse.", null, 'ERROR');
            return;
        }

        $this->log('  loaded mining: ' . $this->mining->getName());
        $project = $this->mining->getProject();
        $configs = $project->getProjectConfig();
        $db = new MineVisDb($project->getExternalDatabase());

        if ($this->mining->getComplete() != true) {
            $this->logSection("Incomplete Mining Id", "Cannot generate links until mining algorithm ran, run mining first!", null, 'ERROR');
            return;
        } else if ($project->getJigsawBased() != true) {
            $this->logSection("Non JigSAW project", "Cannot generate links from non jigsaw projects", null, 'ERROR');
            return;
        } else if ($this->mining->getEntityFrequencyStatus() == 2) {
            $this->logSection("generateEntityFrequencies done", "the task is already complete", null, 'INFO');
            return;
        } else if ($this->mining->getEntityFrequencyStatus() == 1) {
            $this->logSection("generateEntityFrequencies running", "the task is currently running already", null, 'ERROR');
            return;
        } else if ($this->mining->getEntityFrequencyStatus() == 0) {
            /*
             * Mark generateEntityFrequencies as running (avoids doubling up)
             */
            $this->mining->setEntityFrequencyStatus(1);
            $this->mining->save();
            $this->get_time_interval(); //setup start time
            $this->logSection("gathering config info", "...", null, "INFO");

            /**
             * Get a list of the entity types
             */
            $types = $project->getUniqueTypes();
            $this->log('  ' . count($types) . ' entity tables');

            /**
             * Get a count of the total number of entities
             */
            $entities_count = 0;
            foreach ($types as $table_name) {
                $table_name = $table_name[0];
                if ($table_name != "document") {
                    $table_count = $db->getTableEntityCount($table_name);
                    //$this->log('  ' . $table_name . ' entity count: ' . $table_count);
                    $entities_count += $table_count;
                }
            }
            $this->log('  ' . $entities_count . " total entities");

            $this->logSection("generating entity frequencies", "...", null, "INFO");

            //Set up some status printing stuff
            $print_interval = $entities_count / 10; //print every 10%
            $print_next = $print_interval;
            $current = 0;

            /*
             * Loop through types and then entities for each type
             */
            foreach ($types as $table_name) {
                $type = $table_name[0];

                /*
                 * Skip the document type
                 */
                if ($type != "document") {

                    /*
                     * Pre load config filters
                     */
                    $row_configs = array();
                    $col_configs = array();
                    foreach ($configs as $config) {
                        if ($config->getTableA() == $type) {
                            $row_configs[] = $config->getId();
                        } else if ($config->getTableB() == $type) {
                            $col_configs[] = $config->getId();
                        }
                    }
                    $row_where = $db->getWhereString('b.config_id', $row_configs);
                    $col_where = $db->getWhereString('b.config_id', $col_configs);
                    $this->log("  Row Filter: " . $row_where);
                    $this->log("  Column Filter: " . $col_where);

                    /*
                     * Look up entities in that table
                     */
                    $entity_iterator = $db->getTableEntityIterator($type);
                    if ($entity_iterator) {
                        while ($row = mysql_fetch_row($entity_iterator)) {
                            if ($current >= $print_next) {
                                $this->log('  ' . 100 * $current / $entities_count . '%');
                                $print_next += $print_interval;
                            }

                            /*
                             * Count Biclusters for current entity
                             */
                            $count = 0;


                            if ($row_where != '') {
                                $q = new Doctrine_RawSql();
                                $q->select('{b.*}, {r.*}')
                                        ->from('mining_bi_cluster b JOIN mining_bi_cluster_row r ON b.id = r.bicluster_id')
                                        ->addComponent('b', 'MiningBiCluster')
                                        ->addComponent('r', 'MiningBiClusterCol')
                                        ->where('r.row_id = ? AND (' . $row_where . ') AND b.mining_id = ' . $this->mining->getId(), $row[0]);
                                //$this->log($q->getCountSqlQuery());
                                $result = $q->count();
                                $count += $result;
                            }

                            if ($col_where != '') {
                                $q = new Doctrine_RawSql();
                                $q->select('{b.*}, {c.*}')
                                        ->from('mining_bi_cluster b JOIN mining_bi_cluster_col c ON b.id = c.bicluster_id')
                                        ->addComponent('b', 'MiningBiCluster')
                                        ->addComponent('c', 'MiningBiClusterRow')
                                        ->where('c.col_id = ? AND (' . $col_where . ')', $row[0]);
                                // $this->log($q->getCountSqlQuery());
                                $result = $q->count();
                                $count += $result;
                            }

                            $entity_fq = new EntityFrequency();
                            $entity_fq->setEntityId($row[0]);
                            $entity_fq->setEntityName($row[1]);
                            $entity_fq->setEntityType($type);
                            $entity_fq->setBiclusterCount($count);
                            $entity_fq->setMiningId($this->mining->getId());
                            $entity_fq->save();
                            //$this->log('  ' . $row[1] . "\tid: " . $row[0] . "\tt: " . $type . "\tcount: " . $count );

                            /*
                             * save the entity frequency
                             */

                            $current++;
                        }
                    } else {
                        $this->logSection("Entity Iterator", 'Could not get an entity iterator for table: ' . $type, null, 'ERROR');
                    }
                }
                $this->log('  ' . 100 * $current / $entities_count . '%');
            }

            /*
             * Mark EntityFrequency as complete
             */
            $this->mining->setEntityFrequencyStatus(2);
            $this->mining->save();
            $this->log('runtime: ' . $this->get_time_interval());
            $this->logSection('Task Complete', '');
        }
    }

}

