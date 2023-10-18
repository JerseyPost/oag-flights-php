<?php
/*
include_once('classes/airlabs.php');
include_once('classes/utils.php');
include_once('classes/oagbase.php');
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class OAGFlights extends OAGBase
{
   

    function fetchFlightsFromFlightsFile(string $filename): array | bool
    {
        echo "In function ".__METHOD__.PHP_EOL;

        // Open the CSV file
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $thirdColumnValues = []; // Array to store values of the third column

            // Loop through each row of the CSV file
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { // Assuming the delimiter is a comma
                // Check if the row has at least three columns
                if (isset($data[2])) {
                    $thirdColumnValues[strtoupper(trim($data[2]))] = $data[2]; // Add the value of the third column to the array, skipping duplicates
                }
            }

            // Close the CSV file
            fclose($handle);

            // Display the values of the third column
            array_shift($thirdColumnValues);
            print_r($thirdColumnValues);
            return $thirdColumnValues;
        } else {
            echo "Error: Unable to open the file.".PHP_EOL;
            return false;
        }
    } // fetchFlightsFromFlightsFile




    function getAirportAndRoutDataForFlightNumbers(array $flightNumbers): array
    {


        echo __FUNCTION__." LIMIT set to $this->limit !!!".PHP_EOL;

        // Create a Set to store unique flights
        $uniqueFlights = [];
        $missingFlights = [];

        //echo "HERE *$limit*".__LINE__;
        $flightNumbers = array_slice($flightNumbers, 0, !empty($this->limit) ? $this->limit : 999999);
        echo "Flights to process: " . count($flightNumbers) . PHP_EOL;
        //die();
        // Iterate through the flight numbers and lookup the arrival and departure airports
        $c = 1;
        foreach ($flightNumbers as $flight_iata) {

            /*
            echo "Calling getAirLabsFlight for  $c/" . count($flightNumbers) . " $flight_iata" . PHP_EOL;
            $flightDetails = $this->airlabsConnection->getAirLabsFlighDetails($flight_iata);

            if (empty($flightDetails)) {
                $missingFlights[] = "No flight details for $flight_iata";
                $c++;
                continue;
            }
            echo "Calling getAirLabsRoutes for leg " . $flightDetails['dep_iata'] . "-" . $flightDetails['arr_iata'] . " $flight_iata" . PHP_EOL;
            */
            //echo "<pre>airports ".print_r($airports,true)."</pre>";

            //var_dump($airports);
            //$originAirport = $flightDetails['dep_iata'];
            //$destinationAirport = $flightDetails['arr_iata'];

            // Call the AirLabsRoutes API to get all flights for this leg
            echo __METHOD__.": Calling getAirLabsRoutes $flight_iata" . PHP_EOL;

            $flightsForLeg = $this->airlabsConnection->getAirLabsRoutes(null, null, $flight_iata);
            if (empty($flightsForLeg)) {
                $missingFlights[] = __METHOD__.": No flight details for $flight_iata";
                $c++;
                continue;
            }
            //echo "<pre>flightsForLeg " . print_r($flightsForLeg, true) . "</pre>";
            //var_dump($flightsForLeg);

            // store this flights data from looking it up using Route API
            foreach ($flightsForLeg as $flight) {
                $uniqueFlights[hash('sha256', serialize($flight))] = $flight;
            }
            $c++;
        } // foreach flight

        //echo "HERE " . __LINE__;

        $flightsArray = array_values($uniqueFlights);

        //echo "///////////////////////////";
        //var_dump($flightsArray);
        //echo "<pre>flightsArray ".print_r($flightsArray,true)."</pre>";
        echo "flightsArray:" . count($flightsArray) . PHP_EOL;
        return ['flightsArray'=>$flightsArray, 'missingFlights'=>$missingFlights];
        //die();
    } // getAirportAndRoutDataForFlightNumbers

} // class OAGFlights