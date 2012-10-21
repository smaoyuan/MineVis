<?php

class clearMiningsTask extends sfBaseTask {
    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
                new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
                new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
                new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
                // add your own options here
                new sfCommandOption('updateOnly', 'u', sfCommandOption::PARAMETER_NONE, 'Dont delete jobs, only clear them', null),

        ));

        $this->namespace        = 'MineVis';
        $this->name             = 'clearMinings';
        $this->briefDescription = 'Clear all Mining Resutls from db';
        $this->detailedDescription = <<<EOF
The [clearMinings|INFO] clears all the current mining mappings, resutls (biclusters)
    and resets mining copletion status.
Call it with:

  [php symfony clearMinings|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        /**
         * Manual sql queries because easier?
         */
        $db_connection = Doctrine_Manager::connection();
        $this->logSection('task', 'Clearing mining resutls from rb');
        //Clear the bi clusters
        $this->log('Deleting mining_bi_clusters');
        $query = 'DELETE FROM mining_bi_cluster;';
        $statement = $db_connection->execute($query);
        $statement->execute();
        //$resultset = $statement->fetch(PDO::FETCH_OBJ);

        //Clear the job mappings?
        if (  $options['updateOnly'] == true) {
            $this->log('Reseting mining_job_mappings');
            $query = 'UPDATE mining_job_mapping SET start_time=null, end_time=null';
        } else {
            $this->log('Dropping mining_job_mappings contents');
            $query = 'DELETE FROM mining_job_mapping;'; //add option to force delete?
        }
        $statement = $db_connection->execute($query);
        $statement->execute();

        //Reset Mining
        $this->log('Reseting minings as not started & not complete');
        $query = 'UPDATE mining SET complete=0, started=0;';
        $statement = $db_connection->execute($query);
        $statement->execute();
    }
}
