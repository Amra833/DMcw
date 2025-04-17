<?php
// mongodb_config.php

require 'vendor/autoload.php';  // Make sure MongoDB library is installed via Composer

// MongoDB connection settings
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");  // Default MongoDB connection
$database = $mongoClient->urbanfood;  // Database name
$collection = $database->feedbacks;  // Collection for customer feedbacks
?>
