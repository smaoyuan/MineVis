<?php

class generateDocumentLinksTask extends sfBaseTask {

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
        ));

        $this->namespace = 'minevis';
        $this->name = 'generateDocumentLinks';
        $this->briefDescription = 'This generates a lookup tables for DocumentLinks';
        $this->detailedDescription = <<<EOF
The [generateDocumentLinks|INFO] task looks up a mining and if DocumentLinks
    were not yet generated it will generate them.
Call it with:

  [php symfony generateDocumentLinks mining_id|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        //get mining argument
        $mining_id = $arguments['mining_id'];
        $this->logSection('loading', 'mining_id: ' . $mining_id);

        //path
        $this->log('running from: ' . $_SERVER['PHP_SELF']);

        //load mining

        $this->mining = Doctrine_Core::getTable('Mining')->find(array($mining_id));
        if ($this->mining == null) {
            $this->logSection("Invalid Mining Id", "The mining with id " . $mining_id . " could not be found in the databse.", null, 'ERROR');
            return;
        }

        $this->log('loaded mining: ' . $this->mining->getName());
        $project = $this-> mining->getProject();

        if ($this->mining->getComplete() != true) {
            $this->logSection("Incomplete Mining Id", "Cannot generate links until mining algorithm ran, run mining first!", null, 'ERROR');
            return;
        } else if ($project->getJigsawBased() != true) {
            $this->logSection("Non JigSAW project", "Cannot generate links from non jigsaw projects", null, 'ERROR');
            return;
        } else if ($this->mining->getDocumentLinkStatus() == 2) {
            $this->logSection("generateDocumentLinks done", "the task is already complete", null, 'INFO');
            return;
        } else if ($this->mining->getDocumentLinkStatus() == 1) {
            $this->logSection("generateDocumentLinks running", "the task is currently running already", null, 'ERROR');
            return;
        } else if ($this->mining->getDocumentLinkStatus() == 0) {
            /*
             * Mark generateDocumentLinks as running (avoids doubling up)
             */
            $this->mining->setDocumentLinkStatus(1);
            $this->mining->save();
            $this->get_time_interval(); //setup start time
            $this->logSection("generating DocumenLinks","...",null,"INFO");
            /*
             * Loop through stuff
             */
            $biclusters = $this->mining->getBiClusters();
            $bic_count = count($biclusters);
            $link_count = 0;
            $step = $bic_count / 5; // print status every 20% or more
            // That will print at most 5 status lines to keep console output small
            $next_print = $step;
            $current = 0;

            foreach($biclusters as $bic) {
                // print status
                if ($current >= $next_print) {
                    $this->log("  " . 100*$current/$bic_count . "%");
                    $next_print += $step;
                }
                // get the documents for this bic
                $docs = $bic->getDocuments();
                // store each as a DocumentLink
                foreach($docs as $doc_id) {
                    //$this->log("    generating DocLink for: bic " . $bic->getId() . " doc " . $doc_id);
                    $link = new DocumentLink();
                    $link->setMining($this->mining);
                    $link->setBiclusterId($bic->getId());
                    $link->setDocumentId($doc_id);
                    $link->save();
                    $link_count++;
                }
                $current++;
            }
            $this->log("  " . 100*$current/$bic_count . "%");

            /*
             * Mark generateDocumentLinks as complete
             */
            $this->mining->setDocumentLinkStatus(2);
            $this->mining->save();
            $this->log('runtime: ' . $this->get_time_interval());
            $this->logSection('Task Complete', 'generated ' . $link_count . ' links total.');
        }
    }

}
