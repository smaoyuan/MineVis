<?php

/**
 * LCM Wrapper for MineVis
 *
 * LCM finds all subsets, called patterns, frequently appearing in a database.
 * LCM also can enumerate maximal patterns and closed patterns. This code is a
 * bug fixed version of the code which got the best implementation award in
 * FIMI 2004, which is a workshop of programing competition of data mining.
 * It can enumerate bipartite cliques in a bipartite graph.
 * From:
 * http://research.nii.ac.jp/~uno/codes.htm
 *
 * To execute LCM, just type lcm and give some parameters as follows.
 * lcm [command] [options] input-filename support [output-filename]
 *
 */
class LCM implements MiningAlgorithm {

    /**
     * @var string execuatable path
     */
    private static $algorithm_path = '/lib/algorithms/lcm/';

    /**
     * @var string cache path
     */
    private static $cache_path = '/cache/algorithms/lcm/';

    /**
     * @var string executable name
     */
    private static $exec_name = 'lcm';

    /**
     * A Mining job to run on charm...
     * @var MiningJogMapping mining job
     */
    private $mining_job;

    /**
     *
     * @var Mining
     */
    private $mining;

    /**
     *
     * @var Project
     */
    private $project;

    /**
     *
     * @var ProjectConfig
     */
    private $config;

    /**
     * The first parameter [command] is composed of some letters, and given
     * to the program to indicate the task.
     *  F: enumerate frequent itemsets,
     *  C: enumerate closed frequent itemsets
     *  M: enumerate maximal frequent itemsets
     *
     * OPTIONS:
     *  I: output ID's of transactions including each itemset; ID of a
      transaction is given by the number of line in which the transaction
      is written. The ID starts from 0.
     *
     * In MineVis we'll probably only use C it's predetermined the user doesn't pick
     * @var string commnad parameter
     */
    private $command = 'CI';

    /**
     * Constant Parameters. Here's a description of the ones in use below:
     *   + When we give -S option and a number X, the program will terminate
     *     if it finds X solutions, even if there are more solutions.
     *     2000 is the current threshold.
     *   +
     *
     * For more detail see the readme.txt in lib/algorithm/lcm/.
     *
     * @var string a set of options to add to the cli
     */
    private $options = '-S 2000';

    /**
     * User determined...
     * @var int support threshold value
     */
    private $support = 10;

    /**
     * This is our second control parameter. Number of rows.
     * It's set through the -l option but since it's not constant we separate it
     * from the other options.
     * @var int least value for number of transactions
     */
    private $least = 10;

    /* ---------------------------------------------------------------------- *\
     *
     * Public Static Functions
     *
      \* ---------------------------------------------------------------------- */

    /**
     * Ensures the cache directory is there and writeable.
     * The cache directory can be accessed with getCachePath.
     * This will recursively create the directory if it's missing.
     *
     * @return boolean true if succeded false if it failed.
     */
    public static function checkCache() {
        $sfDir = sfConfig::get('sf_root_dir');

        if (!file_exists($sfDir . self::$cache_path)) {
            if (!mkdir($sfDir . self::$cache_path, 0751, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * This returns the path that this algorithm is currently using to
     * write it's temporary data files (input or output).
     *
     * Use Check cache to make sure the directory is exsitant.
     *
     * Note path is relative.
     *
     * @return string path relative to symphony root.
     */
    public static function getCachePath() {
        return self::$cache_path;
    }

    /**
     * This returns the path that this algorithm is currently
     * stored at and should be run from.
     *
     * @return string path of exe relative to symphony root
     */
    public static function getAlgorithmPath() {
        return self::$algorithm_path;
    }

    /**
     * Returns the executable name
     * @return string executable name...
     */
    public static function getExeName() {
        return self::$exec_name;
    }

    /**
     * Get the root relative paths of the log files for a given mining
     * Returns an associative array with the paths for the
     * error and regular log.
     * 'err' errorlog
     * 'log' log
     * note takes a mining because logs aren't job dependent.
     * @precondition mining is valid
     * @param Mining $mining mining we want the files for
     * @return array of strings
     */
    public static function getLogPaths($mining) {
        $paths = array();
        if ($mining) {
            $paths['log'] = self::$cache_path . 'mining_' . $mining->getId() . '.log';
            $paths['err'] = self::$cache_path . 'mining_' . $mining->getId() . '.err';
        }
        return $paths;
    }

    /**
     * Get the root relative paths of the input files for a given mining
     * Returns an associative array with the paths.
     * 'input' main input
     * 'translation' translation file to convert back to database ids
     *
     * @param MiningJobMapping $mining_job Job to get the files for.
     * @return array of string representing absolute paths...
     */
    public static function getInputPaths($mining_job) {
        $paths = array();
        if ($mining_job != null) {
            $paths['input'] = self::$cache_path . 'lcm_input_config_id_' . $mining_job->getConfigId() . '.dat';
            $paths['translation'] = self::$cache_path . 'lcm_input_trans_config_id_' . $mining_job->getConfigId() . '.json';
        } else {
            echo "Error getting input paths: Mining Job Null!\n";
        }
        return $paths;
    }

    /**
     * Get the root relative paths of the output files for a given mining
     * Returns an associative array with the paths.
     * 'output' main output
     * charm only has 1 output file
     * @param MiningJobMapping $mining_job Job to get the files for.
     * @return array of string representing absolute paths...
     */
    public static function getOutputPaths($mining_job) {
        $paths = array();
        if ($mining_job) {
            $paths['output'] = self::$cache_path . 'lcm_output_config_id_'
                    . $mining_job->getConfigId() . '_job_id_' . $mining_job->getId() . '.txt';
            $paths['console'] = self::$cache_path . 'lcm_output_config_id_'
                    . $mining_job->getConfigId() . '_job_id_' . $mining_job->getId() . '.log';
        }
        return $paths;
    }

    /* ---------------------------------------------------------------------- *\
     *
     * Public Functions
     *
      \* ---------------------------------------------------------------------- */

    /**
     * Base constructor takes in a mining job and mining to get parameters from.
     * @param MiningJobMapping mining job to run
     */
    public function __construct($mining_job) {
        $this->mining_job = $mining_job;
        $this->mining = $this->mining_job->getMining();
        $this->config = $this->mining_job->getProjectConfig();
        $this->project = $this->mining->getProject();
    }

    /**
     * Generate the input this algorithm needs to run...
     * Echoes console outputs
     *
     * @param boolean $forceOverwrite force overwriting the currently cached files
     * even if they exsist
     */
    public function generateInput($forceOverwrite = false) {
        $sf_root = sfConfig::get('sf_root_dir');
        /**
         * Set start time
         */
        $this->mining_job->setStartTime(date('c'));
        $this->mining_job->setStatusCode(1); //set to generating output
        $this->mining_job->save();
        $inputs = self::getInputPaths($this->mining_job);
        $input_file = $inputs['input'];
        $translation_file = $inputs['translation'];

        /**
         * Generate input file if needed
         */
        //echo "input file " . $input_file . "\n";
        echo "input path: " . $sf_root . $input_file . "\n";
        echo "translation path: " . $sf_root . $translation_file . "\n";
        if ((!file_exists($sf_root . $input_file)) or $forceOverwrite == true) {
            $out = null;
            if ($forceOverwrite == true) {
                echo "Input file found, but force overwrite on. Generating...\n";
            } else {
                echo "Input file not found, generating...\n";
            }
            //setup db access
            $db = new MineVisDb($this->project->getExternalDatabase());
            //open outfile
            echo "Config Id: " . $this->config->getId() . "\n";
            $file_h = fopen($sf_root . $input_file, 'w') or $out = "Can't Write to intput file: " . $sf_root . $input_file;
            if ($out) {
                echo $out . "\n";
                $out = null;
            }
            $trans_h = fopen($sf_root . $translation_file, 'w') or $out = "Can't Write to intput file: " . $sf_root . $translation_file;
            if ($out)
                echo $out . "\n";
            //get data into file
            $db->getLCMInput(
                    $this->config->getTableAxb(), $this->config->getTableAxbTableAIdField(), $this->config->getTableAxbTableBIdField(), $file_h, $trans_h
            );
            fclose($file_h);
            fclose($trans_h);
        } else {
            echo "Input file found, using cache.\n";
        }
    }

    /*
     * This runs the minning job on this entry unless it's
     * already complete in that case does nothing.
     * Echoes console outputs
     */

    public function runAlgorithm() {
        /**
         * Check status, don't wanna re run it or run it if not ready
         */
        if ($this->mining_job->isDone()) {
            echo 'Mining already ran...' . "\n";
            return;
        } else if ($this->mining_job->getStatusCode() != 1) {
            //make sure the output is done (technically only checks that it started...)
            echo 'Data Parsing did not run, cannot mine' . "\n";
            return;
        }
        /**
         * Set up Parameters
         */
        $this->mining_job->setStatusCode(2); //set to charm
        $this->mining_job->save();
        $params = $this->mining->getParams();
        $support = $params['min_support'];
        echo "Min Support: " . $support . "\n";
        if (is_numeric($support)) {
            $this->support = $support;
        }
        $least = $params['min_columns'];
        echo "Min Columns: " . $least . "\n";
        if (is_numeric($least)) {
            $this->support = $least;
        }
        $this->least = $support;

        /**
         * Run LCM
         */
        $charm_command = $this->getCommand();
        echo "Command:\n" . $charm_command . "\n";
        //run (this is the TIME CONSUMING part)
        echo "Console Output:\n";
        $charm_console_output = $this->run();
    }

    /**
     * Parses the output from the mining job if it ran...
     * Echoes console outputs
     */
    public function parseResults() {
        $sf_root = sfConfig::get('sf_root_dir');
        /*
         * Perform status checks
         */
        if ($this->mining_job->isDone()) {
            echo 'Mining already ran...' . "\n";
            return;
        } else if ($this->mining_job->getStatusCode() != 2) {
            //make sure the mining is done (technically only checks that it started...)
            echo 'Mining did not run, cannot parse resutls' . "\n";
            return;
        }

        /*
         * set up vars
         */
        $inputs = self::getInputPaths($this->mining_job);
        $translation_file = $inputs['translation'];
        $output_file = $this->getOutputFile();
        echo "Files:\n\t" . $translation_file . "\n\t" . $output_file . "\n";

        /**
         * Open translation (or fail if not found)
         */
        echo "loading translation file... ";
        $translate = array();
        if (file_exists($sf_root . $translation_file)) {
            $encoded = file_get_contents($sf_root . $translation_file);
            $translate = json_decode($encoded);
            echo "done \n";
        } else {
            echo "fail!\n Error: Couldn't find translation file, cache might be corrupt.\n";
            $this->mining_job->setEndTime(date('c'));
            $this->mining_job->setStatusCode(5); //set to error status
            $this->mining_job->save();
            return;
        }

        /**
         * Read results and parse results file.
         *
         * Retulsts are read line by line.
         * 0 and even lines contain items
         * odd lines start with a space and contain transactions
         *
         * clusters[cluster#][col/row][id#]
         */
        $this->mining_job->setStatusCode(3); //set to parse
        $this->mining_job->save();
        $item_line = true; //true if item line, false if transaction line.
        $line_count = 0;
        $items;
        $transactions;
        $cluster_count = 0;
        if (file_exists($sf_root . $output_file)) {
            $file = fopen($sf_root . $output_file, "r");
            if (!$file) {
                echo("Error: Unable to open output file $output_file!" . "\n");
                $this->mining_job->setEndTime(date('c'));
                $this->mining_job->setStatusCode(5); //set to error
                $this->mining_job->save();
                return;
            }

            echo "beining to parse results...\n";
            /**
             * Read file line by line.
             * Every 2 lines we should have a cluster, parse and save it.
             */
            while (($line = fgets($file)) !== false) {
                if ($line == '') {
                    /**
                     * Skip Empty line
                     */
                } else if ($item_line) {
                    /**
                     * Parse a set of items and get ready for transactions
                     */
                    $items = explode(' ', trim($line));
                    //echo "Items parsed:\n[" . implode(' ', $items) . "]\n";
                    $item_line = false; //expect to parse transactions after this
                } else {
                    /**
                     * Parse a set of transactions, translate them, add bicluster
                     */
                    $raw_transactions = explode(' ', trim($line));
                    //echo "Transactions parsed:\n[" . implode(' ', $raw_transactions) . "]\n";
                    $transactions = $this->translateTransactions($raw_transactions, $translate);
                    //echo "Transactions translated:\n[" . implode(' ', $transactions) . "]\n";

                    echo "Bic: Items[" . implode(' ', $items)
                    . "] x Trans[" . implode(' ', $transactions) . "] (Raw:"
                    . implode(' ', $raw_transactions) . ")\n";

                    $cluster = array();
                    $cluster[] = $items;
                    $cluster[] = $transactions;
                    $cluster_count++;
                    $this->saveBiCluster($cluster, $this->mining, $this->config);
                    $item_line = true; //expect to parse a new cluster after this.
                }
                $line_count++;
            }
            echo 'Parsed cluster: ' . $cluster_count . " out of " . $line_count . " lines\n";
            fclose($file);
        } else {
            echo "Error: LCM output file could not be found.\n";
            $this->mining_job->setEndTime(date('c'));
            $this->mining_job->setStatusCode(5); //set to error
            $this->mining_job->save();
            return;
        }

        /**
         * Set end time
         */
        $this->mining_job->setEndTime(date('c'));
        $this->mining_job->setStatusCode(4); //set to generating done
        $this->mining_job->save();
    }

    /* ---------------------------------------------------------------------- *\
     *
     * Private Functions
     *
     * ---------------------------------------------------------------------- */

    /**
     * Helper function for running the mining.
     * Given a 2d array of size 2 where
     * [0]arr is the cluster cols
     * [1]arr is the cluster rows
     * Mining and config are the info to save along with the cluster.
     *
     * Creates the MiningBiCluster and the correpsonding MiningBiClusterRow
     * and MiningBiClusterCol.
     *
     * @param Array[][] $cluster_array
     * @param Mining $mining
     * @param ProjectConfig $config
     */
    private function saveBiCluster($cluster, $mining, $config) {
        //create bicluster item
        $biCluster = new MiningBiCluster();
        $biCluster->setMining($mining);
        $biCluster->setProjectConfig($config);
        $biCluster->save();

        //create rows
        foreach ($cluster[0] as $id) {
            $biClusterCol = new MiningBiClusterCol();
            $biClusterCol->setColId($id);
            $biClusterCol->setMiningBiCluster($biCluster);
            $biClusterCol->save();
        }

        //create cols
        foreach ($cluster[1] as $id) {
            echo $id;
            $biClusterRow = new MiningBiClusterRow();
            $biClusterRow->setRowId($id);
            $biClusterRow->setMiningBiCluster($biCluster);
            $biClusterRow->save();
        }
    }

    /**
     * Converts the list of line number encoded transactions to transaction ids.
     *
     * Since line numbers are linear they match directly the translate array's
     * index allowing for direct retrival. Hence allowing this function O(n).
     *
     * @param array $raw_transactions line numbers from output
     * @param array $translate conversion array to go from line number to trans id
     * @return array
     */
    private function translateTransactions($raw_transactions, $translate) {
        $transactions = array();
        foreach ($raw_transactions as $line_number) {
            $transactions[] = $translate[intval($line_number)];
        }
        return $transactions;
    }

    /**
     * Generates the command line to run the algorithm based on current
     * parameters.
     *
     * Sample:
     * ./lcm CI -l 2 config_id_39.dat 4 test_run_39.txt
     * @return string command line
     */
    private function getCommand() {
        $sf_root = sfConfig::get('sf_root_dir');
        $input_file = $sf_root . $this->getInputFile();
        $outputs = self::getOutputPaths($this->mining_job);
        $console_output = $sf_root . $outputs['console'];
        $output_file = $sf_root . $outputs['output'];
        $executable = $sf_root . self::getAlgorithmPath() . self::getExeName();

        $command = $executable . ' ' . $this->command
                //. ' ' . $this->options
                . ' -l ' . $this->least
                . ' ' . $input_file
                . ' ' . $this->support
                . ' ' . $output_file
                . ' > ' . $console_output;
        ;
        return $command;
    }

    /**
     * Runs LCM
     */
    private function run() {
        exec($this->getCommand());
    }

    /**
     * Gets the path to the input file
     * @return string relative path to input file.
     */
    private function getInputFile() {
        $inputs = self::getInputPaths($this->mining_job);
        return $inputs['input'];
    }

    /**
     * Gets the path to the input file
     * @return string relative path to input file.
     */
    private function getOutputFile() {
        $outputs = self::getOutputPaths($this->mining_job);
        return $outputs['output'];
    }

}

?>
