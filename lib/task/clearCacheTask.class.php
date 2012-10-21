<?php

/**
 * This class will be used to clear the cached data for mining and chaining.
 *
 * this includes
 * logs
 * compiled asc files
 * output files (.txt) with resluts.
 *
 * clear files... links should help:
 * http://www.symfony-project.org/more-with-symfony/1_4/en/13-Leveraging-the-Power-of-the-Command-Line#chapter_13_helper_methods_user_interaction
 * http://www.symfony-project.org/api/1_1/sfFilesystem
 */
class clearCacheTask extends sfBaseTask {

    private static $ERR_EXT = '.err';
    private static $LOG_EXT = '.log';
    private $sfRoot;
    //assume clear all by default
    private $clearMining = true;
    private $clearChaining = true;
    private $clearInput = true;

    /**
     * Config here.
     *
     * Add a few options
     */
    protected function configure() {

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            new sfCommandOption('clearOutput', 'o', sfCommandOption::PARAMETER_NONE, 'Clear only outputfiles', null),
            new sfCommandOption('mining', 'm', sfCommandOption::PARAMETER_NONE, 'Clear only mining caches', null),
            new sfCommandOption('chaining', 'c', sfCommandOption::PARAMETER_NONE, 'Clear only chaining caches', null),
        ));

        $this->namespace = 'minevis';
        $this->name = 'clearCache';
        $this->briefDescription = 'Clear the caches in the cache folder';
        $this->detailedDescription = <<<EOF
The [clearCache|INFO] task cleans up the algorithms folder. What is usually found
    in there is files generated for charm or other input or output.
    You can clear just the output with the -o option.
Call it with: TODO update this to show parameters...
-m mining only
-c chaing only
  [php symfony clearCache|INFO]
or
  [php symfony clearCache -c|INFO] clear chaining caches only
or
  [php symfony clearCache -m|INFO] clear mining caches only
or
  [php symfony clearCache -o|INFO]
to clear only output files.
or a combination of m/c and o.
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        // init root directory
        $this->sfRoot = sfConfig::get('sf_root_dir');
        //print title
        $this->logSection('task', 'Clear cache');
        /*
         * Are we clearing output only or all cache including input?
         */
        if ($options['clearOutput'] == true) {
            $this->clearInput = false;
            $this->logSection('options', 'Only clearing output files');
        }

        /*
         * Are we clearing both mining and chaining cache or just one of them?
         */
        if ($options['mining'] == true && $options['chaining'] == true) {
            $this->logSection('aborting', 'mining and chaining options cannot be used together', null, 'ERROR');
            return;
        } else if ($options['mining'] == true) {
            $this->clearChaining = false;
            $this->logSection('options', 'Only clearing Mining cache');
        } else if ($options['chaining'] == true) {
            $this->clearMining = false;
            $this->logSection('options', 'Only Clearing Chaining cache');
        }

        /**
         * Just make sure we know what we're doing.
         */
        $confirm = $this->askConfirmation('Are you sure you want to clear the cache?', null, false);
        if ($confirm == false) {
            $this->logSection('aborting', 'Cache clearing canceled', null, 'ERROR');
            return;
        }

        /*
         * Clear Minings first
         */
        if ($this->clearMining == true) {
            $this->logSection('mining', 'Clearing mining caches');
            /*
             * Get a list of the algorithms
             */
            $algorithms = Doctrine_Core::getTable('Mining')->getMiningAlgorithms();

            /**
             * Loop through all the algorithms and clear each
             */
            foreach ($algorithms as $mining) {
                $this->clearMining($mining);
            }
        }
        /*
         * Clear chainings
         */
        if ($this->clearChaining == true) {
            $this->logSection('chaining', 'Clearing chaining caches');
            /**
             * For each algorithm
             */
            $chaining = 'MineTree';
            $this->clearChaining($chaining);
        }
    }

    /**
     * This clears a mining by calling some stuff on it's class via reflection...
     * @param string $mining class name of MiningAlgorithm
     */
    protected function clearMining($mining = 'Charm') {
        $this->log($mining . ' algorithm:');

        $folder_path = $mining::getCachePath();
        $exe = $mining::getExeName();

        $this->log('Clearing output files...');
        $pattern = $exe . "_output";
        $this->clearFolderByExt($folder_path, $pattern);

        $this->log('Clearing logs files...');
        $this->clearLogsHelper($folder_path);

        if ($this->clearInput == true) {
            $this->log('Clearing input files...');
            $pattern = $exe . "_input";
            $this->clearFolderByExt($folder_path, $pattern);
        }
    }

    /**
     * This clears the logging and stuff for the minetree algorithm but it should be
     * refactored to work with other algorithms! so that using the class name and reflection
     * it can call the statics to get the names paths and patterns needed to clear the cache.
     *
     * @param string $chaining Name of the chaining class
     */
    protected function clearChaining($chaining = 'MineTree') {
        $this->log($chaining . ' Algorithm:');

        $this->log('Clearing output files...');
        $this->clearChainingOutput();

        $this->log('Clearing logs...');
        $this->clearLogsHelper('/cache/algorithms/minetree/');
        /*
         * If not clearing output only also clear cached inputs
         */
        if ($this->clearInput == true) {
            $this->log('Clearing input files...');
            $this->clearChainingInput(null);
        }
    }

    /**
     * Clears the output files and logs for a chaining algorithm.
     * @param ChainingAlgorithm $chaining Chaining algorithm.
     * not yet implemented.
     */
    protected function clearChainingOutput($chaining = null) {
        /**
         * TODO mulitipleChaining fix only works with one alg for now
         */
        $path = '/cache/algorithms/minetree/';
        $ext1 = '.json';
        $pat = 'chaining_result';
        /**
         * Clear output files
         */
        $this->clearFolderByExt($path, $pat, $ext1); //clear JSON files
    }

    /**
     * Clears the input files for a chaining algorithm.
     * @param ChainingAlgorithm $chaining Chaining algorithm.
     * Not yet implemented.
     */
    protected function clearChainingInput($chaining = null) {
        /**
         * TODO mulitipleChaining fix only works with one alg for now
         */
        $path = '/cache/algorithms/minetree/';
        $ext1 = '.json';
        $ext2 = '.arff';
        $pat = 'chaining_data';
        /**
         * Clear input files
         */
        $this->clearFolderByExt($path, $pat, $ext1); //clear JSON files
        $this->clearFolderByExt($path, $pat, $ext2); //clear ARRF files
    }

    /**
     * This clears the logs given a folder path.
     *
     * @param string folder_path path of directory for which to clear logs
     */
    protected function clearLogsHelper($folder_path) {
        $this->clearFolderByExt($folder_path, '', self::$ERR_EXT);
        $this->clearFolderByExt($folder_path, '', self::$LOG_EXT);
    }

    /**
     * Clears all the files from a folder that match the extention
     * With pattern matching so ie: pattern=chaining_data and ext=json
     * 'chaining_data_id_23429.json' would get removed
     * 'chaining_result_20934.json' would not.
     * This allows for separating output/input removal when extensions are the same.
     *
     * @param string $folder_path Path to clear files from
     * @param string $ext extension to match
     * @param string $pattern pattern of filename to match
     */
    private function clearFolderByExt($folder_path, $pattern = '', $ext = '') {
        $globbing = $this->sfRoot . $folder_path . $pattern . '*' . $ext;
        $inputs = glob($globbing);
        //echo 'glob ' . $globbing . ":\n";
        //var_dump($inputs);
        if ($inputs != null) {
            $this->getFilesystem()->remove($inputs);
        }
    }

}
