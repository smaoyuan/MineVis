<?php

class docLookupTask extends sfBaseTask {

    /**
     * Bicluster if in bicluster mode.
     * @var MiningBiCluster bic
     */
    private $bicluster;

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('element_id', sfCommandArgument::REQUIRED, 'Id of the element to look up'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            new sfCommandOption('link', 'l', sfCommandOption::PARAMETER_NONE, 'Look up a link'),
            new sfCommandOption('bicluster', 'b', sfCommandOption::PARAMETER_NONE, 'Look up a bicluster'),
        ));

        $this->namespace = 'minevis';
        $this->name = 'docLookup';
        $this->briefDescription = 'Look up the documents in a given BiC or Link';
        $this->detailedDescription = <<<EOF
The [docLookup|INFO] task looks up the documents a BiCluster or is made of
    or the documents the BiClusters of a Link are made of...

assume it's a bic by default...

  [php symfony docLookup|INFO] -l element_id
  look up link id 3's biclusters' documents
  [php symfony docLookup|INFO] -b element_id
  look up biclusters id 3's documents

EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $doBic = true;

        /**
         * Check arguments
         */
        $element_id = $arguments['element_id'];

        /*
         * Are we doing link or bicluster
         */
        if ($options['link'] == true && $options['bicluster'] == true) {
            $this->logSection('Error', 'link and bicluster options cannot be used together', null, 'ERROR');
            return;
        } else if ($options['link'] == true) {
            $doBic = false;
            $this->logSection('Mode', 'Link');
        } else if ($options['bicluster'] == true) {
            $this->logSection('Mode', 'Bicluster');
        } else {
            $this->logSection('Mode', 'Bicluster');
        }

        /**
         * Do different thing depending on mode.
         */
        if ($doBic === true) {
            /**
             * Load the bicluster
             */
            $this->bicluster = Doctrine_Core::getTable('MiningBiCluster')->find(array($element_id));
            if (is_null($this->bicluster)) {
                $this->logSection('Error', 'Bicluster not found', null, 'ERROR');
                return;
            }

            $db = new MineVisDb($this->bicluster->getProjectConfig()->getProject()->getExternalDatabase());

            /**
             * Get a list of the doc ids and Print it
             */
            $id_list = $this->bicluster->getDocuments();
            $doc_list = $db->getDocumentListWhere($id_list);
            $this->printBiClusterHelper($element_id, $doc_list);
        } else {
            /**
             * Load the link
             */
            $link = Doctrine_Core::getTable('ChainingLink')->find(array($element_id));
            if (is_null($link)) {
                $this->logSection('Error', 'Link not found', null, 'ERROR');
                return;
            }

            /**
             * Set up the main variables...
             */
            $origin = $link->getTargetBiCluster();
            $destinations = $link->getDestinations();
            $config = $origin->getProjectConfig();
            $project = $config->getProject();


            /**
             * Make sure it's a jigsaw project
             */
            if ($project->getJigsawBased() != true) {
                $this->logSection('Error', 'Project is not Jigsaw Based', null, 'ERROR');
                return;
            }

            /**
             * Connect to db
             */
            $db = new MineVisDb($project->getExternalDatabase());

            /**
             * Look up for Origin document
             */
            $id_list = $origin->getDocuments();
            $doc_list = $db->getDocumentListWhere($id_list);
            $this->logSection('Origin:','');
            $this->printBiClusterHelper($origin->getId(), $doc_list);

            /**
             * Loop through the destinations and look up their documents
             */
            $this->logSection('Destinations:','');
            foreach ($destinations as $dest) {
                $bicluster = $dest->getDestinationBiCluster();
                
                $id_list = $bicluster->getDocuments();
                $doc_list = $db->getDocumentListWhere($id_list);
                $this->printBiClusterHelper($bicluster->getId(), $doc_list);
            }
        }
    }

    /**
     * Prints a little cute table...
     * @param int $element_id Bicluster Id
     * @param array $doc_list Document names and ids (index)
     */
    private function printBiClusterHelper($element_id, $doc_list) {
        $this->log('Bicluster Id ' . $element_id);
        $this->log("| Id\t| Name\t|");
        foreach ($doc_list as $id => $doc) {
            $this->log("| " . $id . "\t| " . $doc . "\t|");
        }
    }

}
