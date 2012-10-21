<?php

class getVisStatsTask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('path', sfCommandArgument::OPTIONAL, 'Path for output'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
                // add your own options here
        ));

        $this->namespace = 'minevis';
        $this->name = 'getVisStats';
        $this->briefDescription = 'Prints some numbers on the visualizations in the system.';
        $this->detailedDescription = <<<EOF
The [getVisStats|INFO] task prints a table of the # of docs bics and other things in
    a vis
Call it with:

  [php symfony getVisStats|INFO]
  or to get a csv file
  [php symfony getVisStats file_path|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $this->logSection("Visualizations Stats", "");

        $path = $arguments['path'];

        //make sure that arguments are valid:


        $visualizations = Doctrine_Core::getTable('Visualization')
                ->createQuery('a')
                ->orderBy('a.name ASC')
                ->execute();

        if (count($visualizations) == 0) {
            $this->log("No visualizations available in the system...");
            return;
        }

        if ($path == "") {
            $this->log('dumping stats to console');
            $this->dumpToConsole($visualizations);
        } else {
            $this->log('path parameter:' . $path);
            $this->dumptoCSV($visualizations, $path);
        }
    }

    private function dumpToConsole($visualizations) {
        $this->log("Stats table loading...");
        $this->logBlock("| Visualization Name "
                . "\t| Bicluster #"
                . "\t| Document #"
                . "\t| User link #"
                . "\t| Highlight #"
                . "\t|"
                , "INFO");
        foreach ($visualizations as $vis) {
            //Get Data here
            $json = json_decode($vis->getJsondata());

            $bicluster_cnt = count($json->biclusters);
            $document_cnt = count($json->documents);
            $user_link_cnt = 0;
            $highlight_cnt = count($json->highlights);
            foreach ($json->links as $link) {
                if ($link->userlink == true) {
                    $user_link_cnt++;
                }
            }
            //Print the resutls into the table.
            $this->log(" | " . $vis->getName()
                    . "\t| \t" . $bicluster_cnt
                    . "\t| \t" . $document_cnt
                    . "\t| \t" . $user_link_cnt
                    . "\t| \t" . $highlight_cnt
                    . "\t| "
            );
        }

        $this->log("Link details table loading...");
        $this->logBlock("| Visualization Name "
                . "\t| Total Links #"
                . "\t| Bic 2 Bic #"
                . "\t| Doc 2 Doc #"
                . "\t| Bic 2 Doc #"
                . "\t| Doc 2 Bic #"
                . "\t|"
                , "INFO");
        foreach ($visualizations as $vis) {
            //Get Data here
            $json = json_decode($vis->getJsondata());

            $links = count($json->links);
            $b2b = 0;
            $d2d = 0;
            $b2d = 0;
            $d2b = 0;
            foreach ($json->links as $link) {
                $t1 = strstr($link->tid1, 'bicluster'); //True for bic false for doc
                //$this->log($t1);
                //$this->log($t1 == False);
                $t2 = strstr($link->tid2, 'bicluster');
                if ($t1 && $t2)
                    $b2b++;
                else if ($t1 == False && $t2)
                    $d2b++;
                else if ($t1 && $t2 == False)
                    $b2d++;
                else
                    $d2d++;
            }
            //Print the resutls into the table.
            $this->log(" | " . $vis->getName()
                    . "\t| \t" . $links
                    . "\t| \t" . $b2b
                    . "\t| \t" . $d2d
                    . "\t| \t" . $b2d
                    . "\t| \t" . $d2b
                    . "\t| "
            );
        }
    }

    private function dumptoCSV($visualizations, $path) {
        $file_path = $path . '/VisualizationStats.csv';
        $file = fopen($file_path, 'w') or $this->logSection('Dump Failed', 'Could not open bic dump file', null, 'ERROR');
        $this->log('dumping stats to: ' . $file_path);

        $this->log("Stats table loading...");
        fwrite($file, "Visualization Name "
                . ",Bicluster #"
                . ",Document #"
                . ",User link #"
                . ",Highlight #"
                . "\n"
        );
        foreach ($visualizations as $vis) {
            //Get Data here
            $json = json_decode($vis->getJsondata());

            $bicluster_cnt = count($json->biclusters);
            $document_cnt = count($json->documents);
            $user_link_cnt = 0;
            $highlight_cnt = count($json->highlights);
            foreach ($json->links as $link) {
                if ($link->userlink == true) {
                    $user_link_cnt++;
                }
            }
            //Print the resutls into the table.
            fwrite($file, $vis->getName()
                    . "," . $bicluster_cnt
                    . "," . $document_cnt
                    . "," . $user_link_cnt
                    . "," . $highlight_cnt
                    . "\n"
            );
        }

        fwrite($file, ",,,,\n");

        fwrite($file, "Visualization Name "
                . ",Total Links #"
                . ",Bic 2 Bic #"
                . ",Doc 2 Doc #"
                . ",Bic 2 Doc #"
                . ",Doc 2 Bic #"
                . "\n"
                );
        foreach ($visualizations as $vis) {
            //Get Data here
            $json = json_decode($vis->getJsondata());

            $links = count($json->links);
            $b2b = 0;
            $d2d = 0;
            $b2d = 0;
            $d2b = 0;
            foreach ($json->links as $link) {
                $t1 = strstr($link->tid1, 'bicluster'); //True for bic false for doc
                //$this->log($t1);
                //$this->log($t1 == False);
                $t2 = strstr($link->tid2, 'bicluster');
                if ($t1 && $t2)
                    $b2b++;
                else if ($t1 == False && $t2)
                    $d2b++;
                else if ($t1 && $t2 == False)
                    $b2d++;
                else
                    $d2d++;
            }
            //Print the resutls into the table.
            fwrite($file, $vis->getName()
                    . "," . $links
                    . "," . $b2b
                    . "," . $d2d
                    . "," . $b2d
                    . "," . $d2b
                    . "\n"
            );
        }
        fclose($file);
        $this->logSection('done', '');
    }

}
