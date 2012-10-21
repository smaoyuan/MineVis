<?php

/*
 * Check for form data if not display form
 */
if (! isset($_POST['table']) ) :
?>
<html>
<body>
    <h2>YAML Generator</h2>
    <p>This pulls the matching sql data and turns it into YAML for easy
    fixuture creating before nuking db...</p>
<form action="SQL_to_Yaml.php" method="post">
Table: <input type="text" name="table" /> (ie : project_config)<br/>
Parent Name: <input type="text" name="name" /> (ie : Project)<br/>
Parent Field: <input type="text" name="field" /> (ie : project_id)<br/>
Parent Id: <input type="text" name="id" /> (ie : 3)<br/>
YAML Alias: <input type="text" name="alias" /> (ie : starwars)<br/>

<input type="submit" />
</form>

</body>
</html>

<?php
//leave after sending form
EXIT;
endif;

/* Display as text to avoid dealing with <br/> */
header("Content-type: text/plain");

/*
 * Get params
*/
$alias = $_POST["alias"];
$table = $_POST['table'];
$name = $_POST["name"];
$field = $_POST["field"];
$id = $_POST["id"];

if ($alias=="" or $id=="" or $name=="" or $field=="" or $table=="") {
    println('WRONG PARAMETERS!!');
    if ($name=="")println(' $name empty');
    if ($field=="")println(' $field empty');
    if ($project_id=="")println(' $id empty');
    if ($table=="")println(' $table empty');
    if ($alias=="")println(' $alias empty');

    EXIT;
}

echo 'CONVERTING PROJECT CONFIG TO YAML FOR SYMFONY FIXTURE'  . "\n";
nl();
foreach($_POST as $k => $v) {
    println('  ' . $k . ': ' . $v);
}
nl();

$db_name = 'symfony';
$connection = mysql_connect('localhost', 'root', 'root');
mysql_select_db($db_name);

$query = 'SELECT * FROM `' . $_POST['table'] . '` WHERE ' . $_POST['field'] . '=' . $_POST['id'];
echo $query . "\n";
nl();

$result = mysql_query($query,$connection);

$row = mysql_fetch_assoc($result);

if ($row==false) {
    println('No rows found!');
    EXIT;
}
println($_POST['table'] . ':');

while ($row) {
    foreach( $row as $k => $a) {
        if ($k == 'id') {
            println('  ' . $alias . $a . ':');
        } else if ($k == $field) {
            println('    '.$name.': ' . $alias);
        } else {
            println('    ' . $k . ': ' . $a);
        }
    }
    nl();
    $row = mysql_fetch_assoc($result);
}


function nl() {
    echo "\n";
}

function println($text) {
    echo $text . "\n";
}
?>
