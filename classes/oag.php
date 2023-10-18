<?php
require_once 'bootstrap.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



class OAGFileProcessor
{
    const doflights = true;
    const doroutes = false;
    const uploadsfolder = 'uploads/';
    const downloadsfolder = 'uploads/';
    //const LINEENDING = PHP_EOL;
    const LINEENDING = '<br/>';

    public static function run($incomingflightsfile, $incomingroutesfile)
    {

        //const FLIGHTSFILE = "2023_Summer_Flights_20230324_100000.csv"; // Replace this with the path to your CSV file
        $datetimestamp = date('Ymd_His');

        $airlabsConnection = new AirLabs();

        $oagflightsObj = new OAGFlights($airlabsConnection, 1);
        $oagroutesObj = new OAGRoutes($airlabsConnection, 3);


        // FLIGHTS

        if (SELF::doflights) {
            echo "Starting with Flights File $incomingflightsfile" . self::LINEENDING;

            $flightFromFlightFile = $oagflightsObj->fetchFlightsFromFlightsFile(self::uploadsfolder.$incomingflightsfile);

            if ($flightFromFlightFile !== false) {

                extract($oagflightsObj->getAirportAndRoutDataForFlightNumbers($flightFromFlightFile));

                $flightsfile = self::downloadsfolder.'oagflightsresults_' . $datetimestamp . '.csv';
                $oagflightsObj->getFlightDetailsAndOutputCSV($flightsArray, $flightsfile);

                echo "MISSING FLIGHTS (from FLIGHTS)" . self::LINEENDING;
                print_r($missingFlights);
            }
        } // doflights


        // ROUTES
        if (self::doroutes) {
            echo "Starting with Routes File $incomingroutesfile" . self::LINEENDING;

            $uniqueLegs = $oagroutesObj->fetchUniqueLegsFromRoutesFile(self::uploadsfolder.$incomingroutesfile);

            extract($oagroutesObj->findFlightsForLegs($uniqueLegs));
            $routesfile = self::downloadsfolder.'oagroutessresults_' . $datetimestamp . '.csv';
            $oagroutesObj->getFlightDetailsAndOutputCSV($flightsArray, $routesfile);

            echo "MISSING FLIGHTS (from ROUTES)" . self::LINEENDING;
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
            echo "No Fligts file" . self::LINEENDING;
        }

        if (!empty($routesfile)) {
            echo "Routes: $routesfile" . self::LINEENDING;
            $content2 = file_get_contents($routesfile);
        } else {
            echo "No Routes file" . self::LINEENDING;
        }
        $headerrow = JPUtils::stringToCsv('carrier,fltno,depapt,depctry,arrapt,arrctry,deptim,arrtim,arrday,days,stops,efffrom,effto');



        // Concatenate the content
        $combinedContent = $headerrow . $content1 . $content2;

        // Save the concatenated content to a new file
        $combinedFilename = "combined_$datetimestamp.csv";
        file_put_contents(self::downloadsfolder.$combinedFilename, $combinedContent);
        echo "Combined file is $combinedFilename" . self::LINEENDING;


// Create an HTML form with a download button.
$htmlForm = "<form action='download.php' method='post'>";
$htmlForm .= "<input type='hidden' name='fileName' value='$combinedFilename'>";
$htmlForm .= "<input type='submit' value='Download'>";
$htmlForm .= "</form>";

// Echo the HTML form to the screen.
echo $htmlForm;
    }
}
