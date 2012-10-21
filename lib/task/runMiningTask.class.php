<?php

/**
 * This simple task runs a mining with id x. Assuming the jobs were setup
 */
class runMiningTask extends sfBaseTask {

    private $microtime_start = null;

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
        // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('mining_id', sfCommandArgument::REQUIRED, 'Mining Id'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
                //new sfCommandOption('dry-run', null, sfCommandOption::PARAMETER_NONE, 'Executes a dry run'),
        ));

        $this->namespace = 'minevis';
        $this->name = 'runMining';
        $this->briefDescription = 'Runs all jobs for a mining';
        $this->detailedDescription = <<<EOF
The [runMining|INFO] runs all of the miningJobMappings for that job.

  [php symfony runMining mining_id|INFO]
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
        $mining = Doctrine_Core::getTable('Mining')->find(array($mining_id));
        if ($mining == null) {
            $this->logSection("Invalid Mining Id", "The mining with id " . $mining_id . " could not be found in the databse.", null, 'ERROR');
            return;
        }
        $this->log('loaded mining: ' . $mining->getName());

        //that they can all have different settings...
        if ($mining->getSetup() == false) {
            //Set Mining as started
            $mining->setSetup(1);
            //Add all configs to job
            $this->log('setting up jobs');
            $mining->setupJobs();
        }
        /*
         * Set process id so we can track it
         */
        $mining->setProcessId(getmypid());
        $mining->save();

        /*
         * Get Algorithm
         */
        $sf_root = sfConfig::get('sf_root_dir');
        $mining_algorithm = strtolower($mining->getAlgorithmName());
        $algorithm_path = $sf_root . $mining_algorithm::getAlgorithmPath() . $mining_algorithm::getExeName();
        if (file_exists($algorithm_path)) {
            $this->log($mining_algorithm . ' path: ' . $algorithm_path . ' (exists)');
        } else {
            $this->log($mining_algorithm . ' path: ' . $algorithm_path . ' (not found)');
            $this->logSection("Aborting", "File Missing", null, 'ERROR');
            $mining->setComplete(true); //no other way to make it done...
            $mining->save();
            return;
        }


        //load mappings
        $jobs = $mining->getJobMappings();
        $this->log('mining contains ' . $jobs->count() . ' jobs');

        /*
         * Run each mapping job:
         * 1. load algorithm
         * 2. generate input (if needed or use cache)
         * 3. run algorithm
         * 4. parse output
         */
        foreach ($jobs as $job) {
            $this->logSection('running', 'job_id: ' . $job->getId());
            $algorithm = new $mining_algorithm($job);
            $forceOverwrite = false; //use cache if possible.

            $this->logSection('input', '...');
            $this->get_time_interval(); //setup start time
            $algorithm->generateInput($forceOverwrite);
            $this->log('runtime: ' . $this->get_time_interval());

            $this->logSection('mining', '...');
            $algorithm->runAlgorithm();
            $this->log('runtime: ' . $this->get_time_interval());

            $this->logSection('output', '...');
            $algorithm->parseResults();
            $this->log('runtime: ' . $this->get_time_interval());
        }

        /*
         * Mark Mining as complete
         */
        $mining->setComplete(true);
        $mining->save();
        $this->logSection('done', 'Mining: ' . $mining->getName() . ' all jobs completed.');
    }

}
