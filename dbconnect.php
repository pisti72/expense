<?php
// http://mysql.atw.hu
//
$kapcsolat = mysql_connect("127.0.0.1", "expense", "Start123");
if (!$kapcsolat) die("Nem sikerült kapcsolódni az adatbázishoz!");
mysql_select_db("expense", $kapcsolat) or die("Nem sikerült kiválasztani az adatbázist!");
mysql_close($kapcsolat);
?>
