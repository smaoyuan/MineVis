<?php
echo "<h1>Testing from php main</h1>\n";

phpinfo(INFO_ENVIRONMENT);
echo "java version<br/>\n";

$java = "java -version";
$out = array();
exec($java,$out);
foreach($out as $line) {
    echo $line . "<br/>\n";
}

echo "<h1>Testing from php process</h1>\n";
$cmd = "php -r 'phpinfo(INFO_ENVIRONMENT);'";
$out = array();
exec($cmd,$out);
foreach($out as $line) {
    echo $line . "<br/>\n";
}

echo "<h2>done</h2>\n";
?>
