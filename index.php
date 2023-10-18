<?php
/*
include_once('classes/airlabs.php');
include_once('classes/utils.php');
include_once('classes/oagflights.php');
include_once('classes/oagroutes.php');
*/

require 'bootstrap.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$doflights = true;
$doroutes = false;



const FLIGHTSFILE = "2023_Summer_Flights_20230324_100000.csv"; // Replace this with the path to your CSV file
$datetimestamp = date('Ymd_His');

echo "Starting with oagflightsObj" . PHP_EOL;

$airlabsConnection = new AirLabs();

$oagflightsObj = new OAGFlights($airlabsConnection, 1);
$oagroutesObj = new OAGRoutes($airlabsConnection, 3);


// FLIGHTS

if ($doflights) {
    $flightFromFlightFile = $oagflightsObj->fetchFlightsFromFlightsFile(FLIGHTSFILE);

    if ($flightFromFlightFile !== false) {

        extract($oagflightsObj->getAirportAndRoutDataForFlightNumbers($flightFromFlightFile));

        $flightsfile = 'oagflightsresults_' . $datetimestamp . '.csv';
        $oagflightsObj->getFlightDetailsAndOutputCSV($flightsArray, $flightsfile);

        echo "MISSING FLIGHTS (from FLIGHTS)" . PHP_EOL;
        print_r($missingFlights);
    }
} // doflights


// ROUTES
if ($doroutes) {
    $uniqueLegs = $oagroutesObj->fetchUniqueLegsFromRoutesFile('2023_Summer_Routes_20230324_110000.csv');

    extract($oagroutesObj->findFlightsForLegs($uniqueLegs));
    $routesfile = 'oagroutessresults_' . $datetimestamp . '.csv';
    $oagroutesObj->getFlightDetailsAndOutputCSV($flightsArray, $routesfile);

    echo "MISSING FLIGHTS (from ROUTES)" . PHP_EOL;
    print_r($missingFlights);
}

// Get content of the two files
$content1 = '';
$content2 = '';

echo "Combining ";
if (!empty($flightsfile)) {
    echo "Flights: $flightsfile and ";
    $content1 = file_get_contents($flightsfile);
} else {
    echo "No Fligts file" . PHP_EOL;
}

if (!empty($routesfile)) {
    echo "Routes: $routesfile" . PHP_EOL;
    $content2 = file_get_contents($routesfile);
} else {
    echo "No Routes file" . PHP_EOL;
}
$headerrow = JPUtils::stringToCsv('carrier,fltno,depapt,depctry,arrapt,arrctry,deptim,arrtim,arrday,days,stops,efffrom,effto');



// Concatenate the content
$combinedContent = $headerrow . $content1 . $content2;

// Save the concatenated content to a new file
$combinedFilename = "combined_$datetimestamp.csv";
file_put_contents($combinedFilename, $combinedContent);
echo "Combined file is $combinedFilename" . PHP_EOL;
