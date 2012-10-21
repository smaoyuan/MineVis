<?php

/**
 * Dumps the starwars db to an sql file.
 * TODO:
 * - add code to load it up into the db... (perhaps with db parameters)
 * - Add parameter to specify probability
 *
 */
class generateSWSQLTask extends sfBaseTask {

    var $index = 0;
    var $outpath = "dump/";
    var $filename = "starwars.sql";
    var $file_h;
    var $probability = 4;
    /*
     * The goal here is to generate sql for building a synthetic relational database.
     *
     * Table 1 People
     * Table 2 Places
     * Table 3 Dates
     * Table 4 Activities
     * Table 5 Items
     * Table 6 Events
     *
     * Relations
     * People - Places
     * People - Activities
     * People - Items
     * ...
     */
    var $people = array(
        'Obi-Wan Kenobi',
        'R2-D2',
        'Yoda',
        'Jar Jar Binks',
        'Jabba the Hutt',
        'Jango Fett',
        'Admiral Ackbar',
        'Chewbacca',
        'Han Solo',
        'Qui-Gon Jinn',
        'Padme Amidala'
    );

    /*
     * http://en.wikipedia.org/wiki/List_of_Star_Wars_planets
     */
    var $planets = array(
        'Alderaan',
        'Ansion',
        'Bespin',
        'Boz Pity',
        'Cato Neimoidia',
        'Coruscant',
        'Dagobah',
        'Dantooine',
        'Endor (moon)',
        'Felucia',
        'Geonosis',
        'Hoth',
        'Iego',
        'Kamino',
        'Kashyyyk',
        'Kessel',
        'Malastare',
        'Mustafar',
        'Mygeeto',
        'Naboo',
        'Nar Shaddaa aka Smugglers moon',
        'Ord Mantell aka Ord Mandell',
        'Polis Massa (asteroid)',
        'Saleucami',
        'Subterrel',
        'Tatooine',
        'Tund',
        'Utapau',
        'Yavin',
        'Yavin',
    );
    var $dates = array();
    var $activities = array(
        'Looking for droids',
        'Bullzeye some womp rats',
        'Joining The empire',
        'Starting a rebellion',
        'Partying with wall-e',
        'Cloning people',
        'Sneaking',
        'Using the force',
        'Joining the dark side',
        'Setting up an ambush',
        'Telling people to move along',
        'Fixing Robots',
    );
    var $items = array(
        'X-wing',
        'Death Star',
        'Millennium Falcon',
        'Naboo royal cruiser',
        'Starfreighter',
        'Trade Federation battleship',
        'E-11 Blaster',
        'Bowcasters',
        'BlasTech DL-44',
        'Lightsaber',
        'Landspeeder',
        'AT-AT',
        'AT-ST',
        'Snowspeeder',
        'Tauntaun',
        'Jawa',
    );

    /*
     * Helpers
     */
    protected function CreateTable($table_name, $title_field) {
        fwrite($this->file_h, 'CREATE TABLE IF NOT EXISTS ' . $table_name . "\n"
        . '(' . "\n"
        . '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,' . "\n"
        . '`' . $title_field . '` varchar(255)' . "\n"
        . ');' . "\n");

        fwrite($this->file_h, "\n");
    }

    protected function CreateXTable($table_a, $table_b) {
        fwrite($this->file_h, 'CREATE TABLE IF NOT EXISTS ' . $table_a . '_' . $table_b . "\n"
        . '(' . "\n"
        . '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,' . "\n"
        . '`' . $table_a . '_id` INT' . ",\n"
        . '`' . $table_b . '_id` INT' . "\n"
        . ');' . "\n");

        fwrite($this->file_h, "\n");
    }

    protected function InsertValues($table, $values) {
        foreach ($values as $str) {
            fwrite($this->file_h, 'INSERT INTO ' . $table . ' VALUES ('.$this->index++.',"' . $str . '");' . "\n");
        }
        fwrite($this->file_h, "\n");
    }

    protected function GenerateMappings($table_a, $set_a, $table_b, $set_b) {
        foreach ($set_a as $ka => &$a) {
            foreach ($set_b as $kb => &$b) {
                $r = rand(0, $this->probability);
                if ($r == 1) {
                    fwrite($this->file_h, 'INSERT INTO ' . $table_a . '_' . $table_b
                    . ' VALUES ('.$this->index++.',' . ($ka + 1) . ',' . ($kb + 1) . ');' . "\n");
                }
            }
        }
    }

    protected function configure() {

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
                // add your own options here
        ));

        $this->namespace = 'minevis';
        $this->name = 'generateSWSQL';
        $this->briefDescription = 'dumps an sql file to build the starwars dataset';
        $this->detailedDescription = <<<EOF
The [generateSWSQL|INFO] dumps a database of starwars entities with
    randomly generated relationships between them.
    This sql file can then be loaded and used into MineVis as a dummy/testing
    dataset.

Call it with:
  [php symfony generateSWSQL|INFO]
EOF;
    }

    private function resetIndex() {
        $this->index = 1;
    }

    protected function execute($arguments = array(), $options = array()) {

        /**
         * Init stuff
         */
        $this->logSection("Initializing", "...");
        $this->initDates();
        $this->log("Dumping file: " . $this->filename);
        $this->log("Dumping at: " . $this->outpath);
        $this->log("Probability factor: 1/" . $this->probability);

        /**
         * Open the file
         */
        $this->file_h = fopen($this->outpath . $this->filename, 'w');
        if ($this->file_h == false) {
            $this->logSection("ERROR", "Could not open file handle for writing... aborting", null, 'ERROR');
            return;
        }
        /**
         * Dump the data
         */
        /*
         * events hapen over a time span and only once...
         * http://en.wikipedia.org/wiki/Chronology_of_Star_Wars
         * need to generate a range of dates from 1 to 38? and assign each a span??
         */

        /*
         * Generate Sql tables
         */
        $this->logSection("Generating tables", "...");
        $this->CreateTable("people", "name");
        $this->CreateTable('planets', 'planet');
        $this->CreateTable('dates', 'date');
        $this->CreateTable('activities', 'desc');
        $this->CreateTable('items', 'name');

        /*
         * Generate many to many tables
         */
        $this->logSection("Generating relation tables", "...");
        $this->CreateXTable('people', 'planets');
        $this->CreateXTable('people', 'activities');
        $this->CreateXTable('people', 'items');

        $this->CreateXTable('items', 'activities');
        $this->CreateXTable('items', 'planets');

        $this->CreateXTable('dates', 'activities');

        $this->CreateXTable('planets', 'activities');

        /*
         * Populate Tables
         */
        $this->logSection("Inserting values", "...");
        $this->resetIndex();
        $this->InsertValues('people', $this->people);
        $this->resetIndex();
        $this->InsertValues('planets', $this->planets);
        $this->resetIndex();
        $this->InsertValues('dates', $this->dates);
        $this->resetIndex();
        $this->InsertValues('activities', $this->activities);
        $this->resetIndex();
        $this->InsertValues('items', $this->items);

        /*
         * Generate Random Mappings
         */
        $this->logSection("Generating random Mappings", "...");
        $this->resetIndex();
        $this->GenerateMappings('people', $this->people, 'planets', $this->planets);
        $this->resetIndex();
        $this->GenerateMappings('people', $this->people, 'activities', $this->activities);
        $this->resetIndex();
        $this->GenerateMappings('people', $this->people, 'items', $this->items);

        $this->resetIndex();
        $this->GenerateMappings('items', $this->items, 'activities', $this->activities);
        $this->resetIndex();
        $this->GenerateMappings('items', $this->items, 'planets', $this->planets);

        $this->resetIndex();
        $this->GenerateMappings('dates', $this->dates, 'activities', $this->activities);

        $this->resetIndex();
        $this->GenerateMappings('planets', $this->planets, 'activities', $this->activities);

        /**
         * Close the file.
         */
        fclose($this->file_h);
        $this->logSection("done", $this->filename . " written at " . $this->outpath);
    }

    private function initDates() {
        for ($i = 32; $i > 0; $i--) {
            $this->dates[] = $i . " BBY";
        }
        $this->dates[] = "0 BBY/ABY";
        for ($i = 0; $i < 4; $i++) {
            $this->dates[] = $i . " ABY";
        }
    }

}
