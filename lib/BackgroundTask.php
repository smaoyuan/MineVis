<?php

/**
 * BackgroundTask is a helper to simplify running  a symfony task in
 * the background like run mining from an action.
 */
class BackgroundTask {

    /**
     * Task name
     * @var string
     */
    private $taskName;
    /**
     * Parameters for this task.
     * @var array
     */
    private $params;
    /**
     * Relative Path of desired output stream
     * @var string
     */
    private $out;
    /**
     * Relative Path of desired error stream
     * @var string
     */
    private $err;
    /**
     * Relative Path of desired input
     * @var string
     */
    private $in;
    /**
     * php resource for process
     * @var resource
     */
    private $resource;
    /**
     * Array with info from proc_get_status
     * @var array
     */
    private $status;
    /**
     * symfony root directory
     * @var string
     */
    private $sfDir;

    /**
     * Process Id
     * @var int pid
     */
    public $pid;
    /**
     * Full Command line
     * @var String command used in this task.
     */
    public $command;

    /**
     * Creates a BackgroundTask object.
     * By default all the streams are set to /dev/null
     * @param string $name Name of task to run
     * @param array $parameters Parameters needed to run task
     */
    public function __construct($name, $parameters) {
        $this->taskName = $name;
        $this->params = $parameters;
        $this->resource = null;
        $this->out = null;
        $this->err = null;
        $this->in = null;
        $this->pid = -1;
        $this->command = '';
        $this->sfDir = sfConfig::get('sf_root_dir');
    }

    /**
     * Tells the background job which streams to use and who to redirect them.
     * set to null or '/dev/null' for null.
     * to redirect to a file put in a relative path (it will stick sfDir in
     * front of it)
     * @param string $out
     * @param string $err
     * @param string $in
     */
    public function setPipes($out, $err, $in) {
        $this->out = $out;
        $this->err = $err;
        $this->in = $in;
    }

    /**
     * Starts off the background job.
     * This also updates the pid and the command name which are accessible after
     * this call completes.
     */
    public function run() {
        $params = ' ';
        foreach( $this->params as $param) {
            $params .= $param . ' ';
        }
        $command = 'php ' . $this->sfDir . '/symfony minevis:' .  $this->taskName .
                $params .
                ' > ' . $this->formatPipe($this->out) .
                ' 2> ' . $this->formatPipe($this->err) .
                ' < '.$this->formatPipe($this->in). '  &';
        //starts process in background...
        $this->resource = proc_open($command, Array(), $ret);
        $this->status = proc_get_status($this->resource);
        proc_close($this->resource);

        $this->command = $this->status['command'];
        $this->pid = $this->status['pid'];
    }

    /**
     * Helper to format the steams to pipe to.
     * If null or set to dev/null then dev/null
     * if not make sure it's relative to symfony and turn it into an absolute
     * path to be suure.
     * @param string $path raw path
     * @return string formated path
     */
    private function formatPipe($path) {
        if ($path == null) {
            return '/dev/null';
        } elseif ($path == '/dev/null') {
            return $path;
        } else {
            return $this->sfDir . $path;
        }
    }

}

?>
