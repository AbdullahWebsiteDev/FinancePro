<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

echo "<h1>PHP Session Information</h1>";
echo "<h2>Session Status: " . session_status() . "</h2>";
echo "<h2>Session ID: " . session_id() . "</h2>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Session Save Path:</h2>";
echo session_save_path();

echo "<h2>Session Configuration:</h2>";
echo "<pre>";
print_r(ini_get_all('session'));
echo "</pre>";

echo "<h2>Cookie Information:</h2>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";
?>