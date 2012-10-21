<?php

/**
 * The charm class basically allows php to set up the parameters to call
 * charm and then just do run(). abstracting some of the stuff away...
 *
 * @author Pat
 */
class Charm implements MiningAlgorithm {

    private static $charm_path = '/lib/algorithms/charm/';
    private static $cache_path = '/cache/algorithms/charm/';
    private static $exec_name = 'charm';

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
     * Input file
     * @var string
     */
    public $i;

    /**
     * Output file or COUT
     * REQUIRED
     * @var string
     */
    public $o;

    /**
     * -S 5 means mine patterns with at least minsup of 5.
     * Must be at least 1
     * REQUIRED
     * @var int
     */
    public $s;

    /**
     * -C G1.cons means that G1.cons is a contraints file
     * not used if or null
     * REQUIRED
     * @var string
     */
    public $c;

    /**
     * The -l prints the geneids
     * @var boolean
     */
    public $l;

    /**
     * -d 0 turns off diffsets, so that each
     * list printed in [ ] after an itemset is indeed a tidset of gene ids.
     * @var int
     */
    public $d;
    /*
     * TODO: add optional algorithm parameters
     * -r
     * -R
     * -E
     * -Z
     * -F
     * -L
     */

    /* ---------------------------------------------------------------------- *\
     *
     * Public Static Functions
     *
     * ---------------------------------------------------------------------- */

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
        return self::$charm_path;
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
     * charm only has 1 input file
     * @param MiningJobMapping $mining_job Job to get the files for.
     * @return array of string representing absolute paths...
     */
    public static function getInputPaths($mining_job) {
        $paths = array();
        if ($mining_job) {
            $paths['input'] = self::$cache_path . 'charm_input_config_id_' . $mining_job->getConfigId() . '.asc';
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
            $paths['output'] = self::$cache_path . 'charm_output_job_id_' . $mining_job->getId() . '.txt';
        }
        return $paths;
    }

    /* ---------------------------------------------------------------------- *\
     *
     * Public Functions
     *
     * ---------------------------------------------------------------------- */

    /**
     * Base constructor takes in a mining job and mining to get parameters from.
     *
     * Sets some default values.
     * @param MiningJobMapping mining job to run
     */
    public function __construct($mining_job) {
        $this->mining_job = $mining_job;
        $this->mining = $this->mining_job->getMining();
        $this->config = $this->mining_job->getProjectConfig();
        $this->project = $this->mining->getProject();
        $this->i = '';
        $this->o = 'COUT';
        $this->s = 3;
        $this->d = 0;
        $this->l = true;
    }

    /**
     * Generate the input this algorithm needs to run...
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

        $input_file = $this->getInputFile();

        /**
         * Generate input file if needed
         */
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
            if ($out)
                echo $out . "\n";
            //get data into file
            $db->getCharmArray(
                    $this->config->getTableAxb(), $this->config->getTableAxbTableAIdField(), $this->config->getTableAxbTableBIdField(), $file_h
            );
            fclose($file_h);
        } else {
            echo "Input file found, using cache.\n";
        }
    }

    /**
     * Runs the job
     */
    public function runAlgorithm() {
        //Only run if job not complete already.
        if ($this->mining_job->isDone()) {
            echo 'Mining already ran...' . "\n";
            return;
        } else if ($this->mining_job->getStatusCode() != 1) {
            //make sure the output is done (technically only checks that it started...)
            echo 'Data Parsing did not run, cannot mine' . "\n";
            return;
        }

        /*
         * load config and stuff
         */
        $input_file = $this->getInputFile();
        $output_file = $this->getOutputFile();

        /**
         * Run Charm
         */
        $this->mining_job->setStatusCode(2); //set to charm
        $this->mining_job->save();
        //set parameters
        $this->i = $input_file;
        $this->o = $output_file;
        //set the step
        echo "Min Support:\n";
        $params = $this->mining->getParams();
        $support = $params['min_support'];
        echo $support . "\n";
        if (is_numeric($support)) {
            $this->s = $support;
        }
        //get command for debug output
        $charm_command = $this->getCommand();
        echo "Command\n";
        echo$charm_command . "\n";
        //run (this is the TIME CONSUMING part)
        echo "Console Output:\n";
        $charm_console_output = $this->run();
    }

    /**
     * Parses the output from the mining job if it ran...
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
        $params = $this->mining->getParams();
        $min_columns = $params['min_columns'];
        echo 'Filtering for a minimum of ' . $min_columns . ' columns' . "\n";
        $output_file = $this->getOutputFile();

        /**
         * load output results
         * Reads them from a file and parses it into a 3d array we can use.
         *
         * clusters[cluster#][col/row][id#]
         */
        $this->mining_job->setStatusCode(3); //set to parse
        $this->mining_job->save();
        $cluster_count = 0;
        $cluster_keep_count = 0;
        if (file_exists($sf_root . $output_file)) {
            $file = fopen($sf_root . $output_file, "r");

            if (!$file) {
                echo("Unable to open output file $output_file!" . "\n");
            }
            //Output a line of the file until the end is reached
            while (!feof($file)) {
                $line = fgets($file);
                //skip empty line
                if ($line != '') {
                    //parse
                    $cluster = $this->parseCharmBiClusterOutput($line);
                    $cluster_count++;
                    //filter by min columns
                    if (count($cluster[0]) >= $min_columns) {
                        //save
                        $this->saveBiCluster($cluster, $this->mining, $this->config);
                        $cluster_keep_count++;
                    }
                }
            }
            echo 'Parsed clusters. Keep/Total = ' . $cluster_keep_count . '/' . $cluster_count . "\n";
            fclose($file);
        } else {
            echo "Charm output file could not be found.\n";
            $this->mining_job->setEndTime(date('c'));
            $this->mining_job->setStatusCode(5); //set to generating error
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

    /**
     * The's the command line to be used in running the algorithm with the current
     * settings
     * @return string command line.
     */
    private function getCommand() {
        /*
         * Set up parameters
         */
        $sfDir = sfConfig::get('sf_root_dir');
        $params = ' ';
        //Input
        if ($this->i != '') {
            $params .= ' -i ' . $sfDir . $this->i;
        }
        //Support
        if ($this->s > 1) {
            $params .= ' -S ' . $this->s;
        } else {
            $params .= ' -S 1 ';
        }
        if ($this->o != '') {
            $params .= ' -o ' . $sfDir . $this->o;
        }
        if (isset($this->d)) {
            $params .= ' -d ' . $this->d;
        }
        if (isset($this->l) and $this->l == true) {
            $params .= ' -l ';
        }
        return $sfDir . self::$charm_path . self::$exec_name . $params;
    }

    /**
     * Runs charm
     */
    private function run() {
        //run charm here
        $result = 0;
        exec($this->getCommand(), $output, $result);
        foreach ($output as $line) {
            echo($line . "\n");
        }
        $this->clean_summary();
    }

    /**
     * This cleans up the summary file that charm leaves behind all the time...
     */
    private function clean_summary() {
        if (file_exists('./summary.out')) {
            unlink('./summary.out');
        }
    }

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
            $biClusterRow = new MiningBiClusterRow();
            $biClusterRow->setRowId($id);
            $biClusterRow->setMiningBiCluster($biCluster);
            $biClusterRow->save();
        }
    }

    /**
     * Read one output and parses it into a 3d array we can use.
     *
     * cluster[table#][id#]
     *
     * table# selects the array of rows/colums:
     *      0 for the x/rows
     *      1 for the y/colums
     *
     * id# is the id of the table_a/b row that's in this cluster.
     *
     * for example:
     * Cluster 0 [ 1 2 3 4 ], [ 7 11 ]
     * returns
     * [ 1 2 3 4 ]
     * [ 7 11 ]
     * would make a table like this:
     * [1 1 0 1 1 0 1 1 0 1 1 0 ]
     * [0 0 1 0 1 0 1 0 0 1 1 0 ]
     * [1 0 1 0 0 1 1 1 1 0 1 1 ]
     * [0 0 0 1 0 0 1 0 0 1 1 0 ]
     * [1 0 0 0 0 0 0 1 1 0 1 1 ]
     * [0 0 1 1 0 0 0 1 1 1 1 1 ]
     * [0 1 0 0 0 1 0 0 1 1 0 0 ]
     * [0 1 1 1 1 1 0 1 0 1 1 1 ]
     * [0 1 1 1 1 0 0 1 0 0 0 1 ]
     * [0 1 1 0 0 0 0 0 1 1 1 1 ]
     * [0 0 0 1 1 1 0 1 1 1 0 1 ]
     * @param String $line Charm ouput like '#c1 #c2 #... - #rs [#r1 #r2 #... ]'
     * @return Array[][] cluster 2d array [0] is cols and [1] is rows
     */
    private function parseCharmBiClusterOutput($line) {
        $cluster = array();
        $dox = true; //start with the x values
        $doy = false;
        $xids = array();
        $xc = $yc = 0;
        $yids = array();
        $ids = explode(" ", $line);
        foreach ($ids as $id) {
            //ignore the dash or opening bracket
            if ($id == "-" or substr($id, 0, 1) == "]") {
                $dox = false;
                $doy = false;
            } else //remove the bracket from first element in array
            if (substr($id, 0, 1) == "[") {
                $id = mb_substr($id, 1);
                $doy = true;
            }

            //now based on booleans add to corresponding arrays
            if ($dox) {
                $xids[$xc] = $id; //add as x value
                $xc++;
            } else if ($doy) {
                $yids[$yc] = $id; //add as y values
                $yc++;
            }
        }
        $cluster[0] = $xids;
        $cluster[1] = $yids;
        return $cluster;
    }

}

?>