<?php
/*
include_once('classes/airlabs.php');
include_once('classes/utils.php');
include_once('classes/oagbase.php');
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class OAGRoutes extends OAGBase
{

    function fetchUniqueLegsFromRoutesFile($filename)
    {
        echo "Reading $filename" . $this->LINEENDING;

        $legsArray = [];

        // Open the CSV file
        if (($handle = fopen($filename, "r")) !== FALSE) {

            // Get the header and determine the position of the Leg columns
            $header = fgetcsv($handle);

            $leg1Index = array_search('Leg_1', $header);
            $leg2Index = array_search('Leg_2', $header);
            $leg3Index = array_search('Leg_3', $header);
            $leg4Index = array_search('Leg_4', $header);
            $MailCategory = array_search('Mail_Category', $header);

            // Read each row of the CSV
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (strtolower(trim($data[$MailCategory])) == 'a') {
                    if (!empty(trim($data[$leg1Index]))) $legsArray[] = preg_replace('/\s+/', '', $data[$leg1Index]);
                    if (!empty(trim($data[$leg2Index]))) $legsArray[] = preg_replace('/\s+/', '', $data[$leg2Index]);
                    if (!empty(trim($data[$leg3Index]))) $legsArray[] = preg_replace('/\s+/', '', $data[$leg3Index]);
                    if (!empty(trim($data[$leg4Index]))) $legsArray[] = preg_replace('/\s+/', '', $data[$leg4Index]);
                }
            }

            fclose($handle);
        }

        // Filter out duplicates
        $uniqueLegs = array_unique($legsArray);

        return $uniqueLegs;
    }







    function findFlightsForLegs(array $uniqueLegs): array
    {
        echo __FUNCTION__ . " LIMIT set to $this->limit !!!";

        // Create a Set to store unique flights
        $uniqueFlights = [];
        $missingFlights = [];

        //echo "HERE *$limit*".__LINE__;
        $legs = array_slice($uniqueLegs, 0, !empty($this->limit) ? $this->limit : 999999);
        echo "HERE " . __LINE__ . " " . count($legs) . $this->LINEENDING;
        //die();
        // Iterate through the legs and get all flights for each leg
        foreach ($legs as $i => $leg) {
            // Split the leg into origin and destination airports
            echo __METHOD__ . ": Calling getAirLabsRoutes for leg ".($i+1)."/" . count($legs) . " $leg" . $this->LINEENDING;
            $airports = explode('-', $leg);
            //echo "<pre>airports ".print_r($airports,true)."</pre>";

            //var_dump($airports);
            $originAirport = trim($airports[0]);
            $destinationAirport = trim($airports[1]);

            // Call the AirLabsRoutes API to get all flights for this leg
            $flightsForLeg = $this->airlabsConnection->getAirLabsRoutes($originAirport, $destinationAirport);

            if (empty($flightsForLeg)) {
                $missingFlights[] = __METHOD__ . ": No flight details for Leg $originAirport-$destinationAirport";
                continue;
            }
            //echo "<pre>flightsForLeg ".print_r($flightsForLeg,true)."</pre>";
            //var_dump($flightsForLeg);

            // Add all the flights for this leg to the Set
            foreach ($flightsForLeg as $flight) {
                $uniqueFlights[hash('sha256', serialize($flight))] = $flight;
            }
        } // foreach leg

        //echo "HERE " . __LINE__;

        // Convert the Set back to an array
        $flightsArray = array_values($uniqueFlights);

        //echo "///////////////////////////";
        //var_dump($flightsArray);
        //echo "<pre>flightsArray ".print_r($flightsArray,true)."</pre>";
        echo "flightsArray:" . count($flightsArray) . $this->LINEENDING;
        //die();

        return ['flightsArray' => $flightsArray, 'missingFlights' => $missingFlights];
    } // findFlightsForLegs

    //echo "HERE ".__LINE__;


} // class OAGRoutes