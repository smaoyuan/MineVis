<?php

/**
 * This is a base for all mining algorithms
 */
interface MiningAlgorithm {
    /* ---------------------------------------------------------------------- *\
     *
     * Public Static Functions
     *
    \* ---------------------------------------------------------------------- */

    /**
     * Ensures the cache directory is there and writeable.
     * The cache directory can be accessed with getCachePath.
     * This will recursively create the directory if it's missing.
     * @return boolean true if succeded false if it failed.
     */
    public static function checkCache();

    /**
     * This returns the path that this algorithm is currently using to
     * write it's temporary data files (input or output).
     *
     * Use Check cache to make sure the directory is exsitant.
     *
     * @return string path of cache folder relative to symphony root.
     */
    public static function getCachePath();

    /**
     * This returns the path that this algorithm is currently
     * stored at and should be run from.
     *
     * @return string path of exe relative to symphony root
     */
    public static function getAlgorithmPath();

    /**
     * Returns the executable name
     * @return string executable name...
     */
    public static function getExeName();

    /**
     * Get the root relative paths of the log files for a given mining
     * Returns an associative array with the paths for the
     * error and regular log.
     * 'err' errorlog
     * 'log' log
     * note takes a mining because logs aren't job dependent.
     * @param Mining $mining mining we want the files for
     * @return array of strings
     */
    public static function getLogPaths($mining);

    /**
     * Get the root relative paths of the input files for a given mining
     * Returns an associative array with the paths.
     * Each algorith will list it's respective input files.
     * @param MiningJobMapping $mining_job Job to get the files for.
     * @return array of string representing absolute paths...
     */
    public static function getInputPaths($mining_job);

    /**
     * Get the root relative paths of the output files for a given mining
     * Returns an associative array with the paths.
     * Each algorith will list it's respective output files.
     * @param MiningJobMapping $mining_job Job to get the files for.
     * @return array of string representing absolute paths...
     */
    public static function getOutputPaths($mining_jog);

    /* ---------------------------------------------------------------------- *\
     *
     * Public Functions
     *
    \* ---------------------------------------------------------------------- */

    /**
     * Base constructor takes in a mining job and mining to get parameters from.
     * @param MiningJobMapping mining job to run
     */
    public function __construct($mining_job);

    /**
     * Generate the input this algorithm needs to run...
     *
     * Echoes console outputs
     *
     * @param boolean $forceOverwrite force overwriting the currently cached files
     * even if they exsist
     */
    public function generateInput($forceOverwrite = false);

    /*
     * This runs the minning job on this entry unless it's
     * already complete in that case does nothing.
     * Echoes console outputs
     */
    public function runAlgorithm();

    /**
     * Parses the output from the mining job if it ran...
     *
     * Echoes console outputs
     */
    public function parseResults();
}

?>
