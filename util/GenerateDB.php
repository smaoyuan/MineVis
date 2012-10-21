<?php

/* Display as text to avoid dealing with <br/> */
header("Content-type: text/plain");
$probability = 4;
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

$people = array(
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
$planets = array(
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

$dates = array();
for ($i = 32; $i > 0; $i--) {
    $dates[] = $i . " BBY";
}
$dates[] = "0 BBY/ABY";
for ($i = 0; $i < 4; $i++) {
    $dates[] = $i . " ABY";
}

$activities = array(
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

$items = array(
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
 * events hapen over a time span and only once...
 * http://en.wikipedia.org/wiki/Chronology_of_Star_Wars
 * need to generate a range of dates from 1 to 38? and assign each a span??
*/

/*
 * Generate Sql tables
*/
CreateTable("people", "name");
CreateTable('planets', 'planet');
CreateTable('dates', 'date');
CreateTable('activities', 'desc');
CreateTable('items', 'name');

/*
 * Generate many to many tables
*/
CreateXTable('people', 'planets');
CreateXTable('people', 'activities');
CreateXTable('people', 'items');

CreateXTable('items', 'activities');
CreateXTable('items', 'planets');

CreateXTable('dates', 'activities');

CreateXTable('planets', 'activities');

/*
 * Populate Tables
*/
InsertValues('people',$people);
InsertValues('planets',$planets);
InsertValues('dates',$dates);
InsertValues('activities',$activities);
InsertValues('items',$items);

/*
 * Generate Random Mappings
*/
GenerateMappings('people', $people, 'planets', $planets);
GenerateMappings('people', $people, 'activities', $activities);
GenerateMappings('people', $people, 'items', $items);

GenerateMappings('items', $items, 'activities', $activities);
GenerateMappings('items', $items, 'planets', $planets);

GenerateMappings('dates', $dates, 'activities', $activities);

GenerateMappings('planets', $planets, 'activities', $activities);

/*
 * Helpers
*/
function CreateTable($table_name, $title_field) {
    echo 'CREATE TABLE IF NOT EXISTS ' . $table_name . "\n"
            . '(' . "\n"
            . '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,' . "\n"
            . '`' . $title_field . '` varchar(255)' . "\n"
            . ');' . "\n";

    echo "\n";
}

function CreateXTable($table_a, $table_b) {
    echo 'CREATE TABLE IF NOT EXISTS ' . $table_a . '_' . $table_b . "\n"
            . '(' . "\n"
            . '`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,' . "\n"
            . '`' . $table_a . '_id` INT' . ",\n"
            . '`' . $table_b . '_id` INT' . "\n"
            . ');' . "\n";

    echo "\n";
}

function InsertValues($table, $values) {
    foreach( $values as $str ) {
        echo 'INSERT INTO '.$table.' VALUES (0,"'.$str.'");' . "\n";
    }
    echo "\n";
}

function GenerateMappings($table_a, $set_a, $table_b, $set_b) {
    global $probability;
    foreach( $set_a as $ka => &$a ) {
        foreach( $set_b as $kb => &$b ) {
            $r = rand(0,$probability);
            if ($r==1) {
                echo 'INSERT INTO '.$table_a . '_' . $table_b
                        .' VALUES (0,'.($ka+1).','.($kb+1).');' . "\n";
            }
        }
    }
}

?>
