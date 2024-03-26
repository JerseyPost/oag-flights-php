<?php

class OAGBase {
    
    protected $airlabsConnection;
    protected $lastSundayInOctober;
    protected $lastSundayInMarch;

    protected $routeStartDate; // to replace $lastSundayInOctober
    protected $routeEndDate; // to replace $lastSundayInMarch

    const LINEENDING = '<br/>';

    protected $limit;

    function __construct($airlabsConnection = null, $limit=9999999, $routeStartDate=null, $routeEndDate=null)
    {
        if (!$airlabsConnection) {
            $this->airlabsConnection = new AirLabs();
        } else {
            $this->airlabsConnection = $airlabsConnection;
        }

        
        $this->limit = $limit; 
    
        // RCH 20240326
        $this->lastSundayInOctober =  date('Ymd', strtotime('last Sunday of October ' . date('Y')));
        $this->lastSundayInMarch = JPUtils::getLastSundayInMarch();
        
        $this->routeStartDate = empty($routeStartDate) ? $this->lastSundayInMarch : $routeStartDate;
        $this->routeEndDate = empty($routeEndDate) ? $this->lastSundayInOctober : $routeEndDate;
    }

    function getFlightDetailsAndOutputCSV(array $flightsArray, string $outputfilename)
    {
        echo "-----------------------------------".self::LINEENDING;
        echo "In ".__METHOD__.self::LINEENDING;

        $missingFlights = [];

        $lastSundayInOctober =  date('Ymd', strtotime('last Sunday of October ' . date('Y')));
        $lastSundayInMarch = JPUtils::getLastSundayInMarch();



        // Open the file for writing
        $file = fopen($outputfilename, 'w');  // 'w' is for write, 'a' is for append

        try {
            // Check if the file is opened successfully
            if ($file) {
                // Use this to reduce the number of API hits while testing
                //$flightsArray = array_slice($flightsArray, 0, 3); // DEBUG!!

                $c=1;
                $total = count($flightsArray);

                // Iterate through the flights, calculate some data and get more details using the AirLabs API
                foreach ($flightsArray as $fi => $flight) {
                    // Calculate the arrival day
                    $arrivalDay = JPUtils::calculateArrivalDay($flight['dep_time_utc'], $flight['duration']);
                    echo __METHOD__.": ($c/$total) Getting details for " . $flight['flight_iata'] . self::LINEENDING;
                    $c++;

                    //echo "HERE3 ".$arrivalDay;

                    // Calculate the number of stops
                    //$numStops = calculateNumStops($flight);
                    $numStops = 0;
                    // echo "HERE " . __LINE__;

                    $dayStr = JPUtils::getDayNumbers($flight['days']);

                    // Preserve Scheduled dep and arr times (without dates) as these get over written when we merge later
                    $scheddeptime = $flight['dep_time_utc'];
                    $schedarrtime = $flight['arr_time_utc'];

                    // Now we have a flight number (flight_iata) we can look up more flight details
                    // We only need this to know the dep and arr countries
                    $dep_airportDetails = $this->airlabsConnection->getAirLabsAirportDetails($flight['dep_iata']);
                    $arr_airportDetails = $this->airlabsConnection->getAirLabsAirportDetails($flight['arr_iata']);
                    //print_r($dep_airportDetails);
                    //print_r($arr_airportDetails);
                    $flightDetails['dep_country'] = $dep_airportDetails[0]['country_code'];
                    $flightDetails['arr_country'] = $arr_airportDetails[0]['country_code'];
                    
                    //$flightDetails = $this->airlabsConnection->getAirLabsFlighDetails($flight['flight_iata']);
                    echo "Calls to AirLabs so far: " . $this->airlabsConnection->getNumcallsmade() . self::LINEENDING;

                    //echo "<pre>flightDetails " . print_r($flightDetails, true) . "</pre>";
                    if (empty($flightDetails)) {
                        $missingFlights[] = __METHOD__.": No flight details for ".$flight['flight_iata'];
                        print_r($flight);
                        break;
                        continue;
                    }
                    //echo "HERE $fi {$flight['flight_iata']} ".__LINE__."<br>";

                    // Merge the flight data with the calculated arrival day and number of stops
                    $flightData = array_merge($flight, [
                        'arrival_day' => $arrivalDay,
                        'num_stops' => $numStops,
                        'daysstr' => $dayStr,
                        'scheddeptime' => $scheddeptime,
                        'schedarrtime' => $schedarrtime
                    ]);

                    if (!empty($flightDetails)) {
                        // Add flight data from Flights API to exising flight data
                        $flightData = array_merge($flightData, $flightDetails);
                    } else {
                        //echo "<pre>No flight data for " . $flight['flight_iata'] . "</pre>";
                    }

                    if(empty($scheddeptime)) {
                        echo "schedarrtime MISSING for ".$flight['flight_iata'];
                    }

                    // Map the fields you need into an array
                    $row = [
                        $flightData['airline_iata'],
                        $flightData['flight_number'],
                        $flightData['dep_iata'],
                        $flightData['dep_country'],
                        $flightData['arr_iata'],
                        $flightData['arr_country'],
                        str_replace(':', '', $flightData['scheddeptime'] ?? ''),
                        str_replace(':', '', $flightData['schedarrtime'] ?? ''),
                        $flightData['arrival_day'],
                        $flightData['daysstr'],
                        $flightData['num_stops'],
                        $this->routeStartDate,
                        $this->routeEndDate
                    ];

                    // Write data to the file
                    $csvdata = JPUtils::stringToCsv(implode(',', $row));
                    fwrite($file, $csvdata);

                    echo $csvdata;

                    echo "Calls to AirLabs so far: " . $this->airlabsConnection->getNumcallsmade() . self::LINEENDING;
                } // for each flightsArray

                // Close the file
                fclose($file);
            } else {
                echo "Unable to open the file.";
            }



            echo "DONE".self::LINEENDING;
        } catch (Exception $e) {
            fclose($file);
            echo "DONE - With Exception ". $e->getMessage().self::LINEENDING;
        }

        print_r($missingFlights);
    } // getFlightDetailsAndOutputCSV

}