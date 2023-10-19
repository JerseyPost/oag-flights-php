<?php
require_once 'bootstrap.php';

set_time_limit(7200); // 7200 = 2hr

// Get the directory path of the folder to scan.
$folderPath = "./uploads";

// Get an array of all the files in the folder.
$files = scandir($folderPath);

// Create two empty arrays to store the most recent Flights and Routes files.
$mostRecentFlightsFile = [];
$mostRecentRoutesFile = [];

// Loop through the array of files and find the most recent Flights and Routes files.
foreach ($files as $file) {
    if (strpos($file, "Flights") !== false) {
        $lastModifiedTime = filemtime($folderPath . "/" . $file);
        if (empty($mostRecentFlightsFile) || $lastModifiedTime > $mostRecentFlightsFile["time"]) {
            $mostRecentFlightsFile = ["file" => $file, "time" => $lastModifiedTime];
        }
    } else if (strpos($file, "Routes") !== false) {
        $lastModifiedTime = filemtime($folderPath . "/" . $file);
        if (empty($mostRecentRoutesFile) || $lastModifiedTime > $mostRecentRoutesFile["time"]) {
            $mostRecentRoutesFile = ["file" => $file, "time" => $lastModifiedTime];
        }
    }
}

// If a Flights file was found, print the file name.
if ($mostRecentFlightsFile) {
    echo "The most recent Flights file is: " . $mostRecentFlightsFile["file"] . "\n";
} else {
    echo "No Flights files were found.\n";
}

// If a Routes file was found, print the file name.
if ($mostRecentRoutesFile) {
    echo "The most recent Routes file is: " . $mostRecentRoutesFile["file"] . "\n";
} else {
    echo "No Routes files were found.\n";
}


OAGFileProcessor::run($mostRecentFlightsFile["file"], $mostRecentRoutesFile["file"]);