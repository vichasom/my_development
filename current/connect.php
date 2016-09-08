<?php
$link = mysql_connect('localhost', 'heroes', '#holidayfitzgeraldartdrums@0809');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';
mysql_select_db('heroes', $link) or die( "Unable to select database");

$query="GRANT ALL PRIVILEGES ON heroes.* TO 'admiredb_mag2'@'%' WITH GRANT OPTION";

mysql_query($query) or die(mysql_error());

?>