<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class OAGFlights extends OAGBase
{
    function fetchFlightsFromFlightsFile(string $filename)
    {
        echo "In function " . __METHOD__ . self::LINEENDING;
        echo "Reading $filename" . self::LINEENDING;

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
            echo "Error: Unable to open the file." . self::LINEENDING;
            return false;
        }
    }

    function getAirportAndRouteDataForFlightNumbers(array $flightNumbers)
    {
        echo __FUNCTION__ . " LIMIT set to $this->limit !!!" . self::LINEENDING;

        // Create a Set to store unique flights
        $uniqueFlights = [];
        $missingFlights = [];

        $flightNumbers = array_slice($flightNumbers, 0, !empty($this->limit) ? $this->limit : 999999);
        echo "Flights to process: " . count($flightNumbers) . self::LINEENDING;

        $c = 1;
        foreach ($flightNumbers as $flight_iata) {
            // ... (unchanged code)
        }

        $flightsArray = array_values($uniqueFlights);

        echo "flightsArray:" . count($flightsArray) . self::LINEENDING;
        return ['flightsArray' => $flightsArray, 'missingFlights' => $missingFlights];
    }
}
