<?php

class importJigsawTask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('jigsaw', sfCommandArgument::REQUIRED, 'Jigsaw File'),
            new sfCommandArgument('outpath', sfCommandArgument::REQUIRED, 'Output Folder Path'),
            new sfCommandArgument('database', sfCommandArgument::REQUIRED, 'Database name'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            //new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
                // add your own options here
        ));

        $this->namespace = 'minevis';
        $this->name = 'importJigsaw';
        $this->briefDescription = 'turns a jigsaw file into an sql file';
        $this->detailedDescription = <<<EOF
The [importJigsaw|INFO] task takes a jigsaw data file and
    converts it into the sql needed to create a db for MineVis.
    all that needs to be done after is source the sql file, and create
    a corresponding project in MineVis (also perhaps add the db to the databases
    in the project table file (model folder)...
Call it with:
  [php symfony importJigsaw|INFO] jigsaw_file output_path db_desired_name
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        /*
         * Set up stuff
         */
        $this->logSection("ImportJigsawTask", "parameters setup");
        $parser = new JigsawParser();

        $jigsaw = $arguments['jigsaw'];
        $this->logSection('jigsaw input', 'path: ' . $jigsaw);
        $outpath = $arguments['outpath'];
        $this->logSection('output path', 'path: ' . $outpath);
        $db = $arguments['database'];
        $this->logSection('database', 'db name: ' . $db);
        $this->log('running from: ' . $_SERVER['PHP_SELF']);

        //make sure that arguments are valid:
        if (!file_exists($jigsaw)) {
            $this->logSection('Invalid Jigsaw', 'The given jigsaw file does not exsist', null, 'ERROR');
            return; //Fail task here
        }
        if (!file_exists($outpath)) {
            $this->logSection('Invalid outpath', 'The given path does not exsist', null, 'ERROR');
            return; //Fail task here
        }
        if ($db == "") {
            $this->logSection('Invalid database', 'Db Name cannot be empty', null, 'ERROR');
            return; //Fail task here
        }


        /*
         * Parse File
         */
        $t0 = microtime(true);
        $this->logSection("Parsing", "jigsaw xml");

        $parser->parse($jigsaw);

        $t1 = microtime(true);
        $diff = ($t1 - $t0);
        $this->log("Parsing Complete (runtime: $diff)");
        $this->logSection("Parsing Stats:", '');
        $this->log("Peak Memory Usage: " . memory_get_peak_usage() / 1024 / 1024 . "MB");
        $this->log("Total Documents Parsed: " . $parser->getDocumentCount());
        $types = $parser->getEntityTypes();
        $this->log("Unique Entity Types:");
        foreach ($types as $t) {
            $this->log("\t*" . $t);
        }

        /*
         * Output sql
         */
        $this->logSection("Writing Output", "formatting into sql");
        $t0 = microtime(true);

        $output = fopen($outpath .$db.'.sql', 'w');
        $parser->writeSQL($output);
        fclose($output);

        $t1 = microtime(true);
        $diff = ($t1 - $t0);
        $this->log("Write Complete (runtime: $diff)");
        $this->logSection("Output Stats:", '');
        $this->log("Peak Memory Usage: " . memory_get_peak_usage() / 1024 / 1024 . "MB");
    }

}
