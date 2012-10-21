<?php

class runChainingTask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('chaining_id', sfCommandArgument::REQUIRED, 'Chaining Id'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        ));

        $this->namespace = 'minevis';
        $this->name = 'runChaining';
        $this->briefDescription = 'Runs all the jobs/link types for a given mining.';
        $this->detailedDescription = <<<EOF
The [runChaining|INFO] task runs all the jobs/link types for a given mining
assuming that they have been created prior to calling run.
Call it with:

  [php symfony minevis:runChaining #chaining_id|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        //get chaining argument
        $chaining_id = $arguments['chaining_id'];
        $this->logSection('loading', 'chaining_id: ' . $chaining_id);
        $this->log('running from: ' . $_SERVER['PHP_SELF']);
        $minetree_path = 'lib/algorithms/minetree/MineVisCoverTree.jar';
        $file_exists = file_exists($minetree_path);
        $this->log('MineVisCoverTree path: ' . $minetree_path . ($file_exists ? ' (exists)' : ' (not found)'));

        //load chaining
        $chaining = Doctrine_Core::getTable('Chaining')->find(array($chaining_id));
        $this->log('loaded chaining: ' . $chaining->getName());

        /**
         * Only Run jobs if algorithm found...
         */
        if ($file_exists) {
            $chaining->setProcessId(getmypid());
            $chaining->save();

            //load mappings
            $jobs = $chaining->getLinkTypes();
            $this->log('chaining contains ' . $jobs->count() . ' jobs/types');

            //run mappings
            //TODO based on type and algorithm
            foreach ($jobs as $job) {
                $this->logSection('running', 'job_id: ' . $job->getId());
                $job->runAlgorithm();
            }

            $chaining->setComplete(true);
            $chaining->save();
            $this->logSection('done', 'Chaining: ' . $chaining->getName() . ' all jobs completed.');
        } else {
            $this->log("Algorithm missing! Aborting operation & reseting chaining");
            $chaining->setProcessId(-1);
            $chaining->setComplete(false);
            $chaining->setStarted(false);
            $chaining->save();
        }
    }

}
