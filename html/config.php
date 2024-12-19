<?php
// Basic connection settings
$databaseHost = 'db';
$databasePort = 3306;
if(isDev()) $databasePort = 3306;
$databaseUsername = 'sn32wosp';
$databasePassword = 'ZNbAfVyxYtGAIY5vga3u';
$databaseName = 'sn32wosp';

// Connect to the database
$mysqli = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName, $databasePort); 

$apiUrl = "https://stats.sp6zhp.pl/qso-list"
?>
