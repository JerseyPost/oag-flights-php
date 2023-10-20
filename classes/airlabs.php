<?php


class AirLabs
{ 
    //const api_key = 'fff37b56-bfae-4d80-ba8a-6ba97c40d314'; // foood
    //const api_key = 'dda49d10-932f-4c43-912a-39be23c951f7'; // cricket
    //const api_key = 'ff2a122d-f424-429c-a365-4e5f0337b5d5'; // 1359
    //const api_key = 'ebc56746-15ed-47f9-97c2-16c797692b03'; // 1120

    private $api_key = 'b9314148-a8e3-4abf-a9f4-e0036e2f8682'; // 1204

    const base_url = 'https://airlabs.co/api/v9';
    const LINEENDING = '<br/>';
    
    private $numcallsmade = 0;

    private $airports = [];


    function __construct()
    {
        // check if AIRLABS_API_KEY env variable exists
        $api_env = getenv('AIRLABS_API_KEY');
        echo "AIRLABS_API_KEY = $api_env".self::LINEENDING;
        if (!empty($api_env) && preg_match('/^[0-9a-z-]{36}$/', $api_env)) {
            $this->api_key = $api_env;
        } else {
            //throw new Exception("Not set or invalid AIRLABS_API_KEY environment variable");
        }
        // Load local airports file into memory so we can use a local cache of them
        // TBC
    }

    function extractFlightDetails($flight_iata) {
        $pattern = '/^([A-Z][A-Z0-9])(\d{1,4})$/';
    
        if (preg_match($pattern, trim($flight_iata), $matches)) {
            return [
                'airlineCode' => $matches[1],
                'flightNumber' => (int)$matches[2]  // dump leading zeroes
            ];
        }
    
        return null;
    }

    function getNumcallsmade() {
        return $this->numcallsmade;
    }

    function getAirLabsRoutes(string $originAirport=null, string $destinationAirport=null, string $flight_iata=null): array|bool
    {

        //echo "In ".__FUNCTION__;
        $url = self::base_url . '/routes';

        // Make a call to the AirLabsRoutes API
        // Create a cURL handle
        $ch = curl_init();

        // Prepare the URL with query parameters
        if (!empty($originAirport) && !empty($destinationAirport) && !empty($flight_iata)) {
            extract($this->extractFlightDetails($flight_iata));

            $params = [
                'api_key' => $this->api_key,
                'dep_iata' => $originAirport,
                'arr_iata' => $destinationAirport,
                'airline_iata' => $airlineCode,
                'flight_number' => $flightNumber
            ];

        } elseif (empty($originAirport) && empty($destinationAirport) && !empty($flight_iata)) {

            extract($this->extractFlightDetails($flight_iata));

            $params = [
                'api_key' => $this->api_key,
                'airline_iata' => $airlineCode,
                'flight_number' => $flightNumber
            ];


        } else  {
            $params = [
                'api_key' => $this->api_key,
                'dep_iata' => $originAirport,
                'arr_iata' => $destinationAirport
            ];
        }
        $queryParams = http_build_query($params);
        //echo "queryParams=$queryParams".PHP_EOL;
        $fullUrl = $url . '?' . $queryParams;
        //die($fullUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        https: //www.w3schools.com/php/php_switch.asp
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

        // Execute the cURL request
        $this->numcallsmade++;
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        // Close the cURL handle
        curl_close($ch);

        // Return the response from the API
        $res = json_decode($response, true);
        if (!empty($res['error'])) {
            echo "<pre>getAirLabsRoutes: $originAirport, $destinationAirport " . print_r($res['error'], true) . "</pre>";
            return false;
        }
        return $res['response'];
    } //getAirLabsRoutes

    function getAirLabsFlighDetails($flight_iata): array|bool
    {

        //echo "In ".__FUNCTION__."($flight_iata)";
        $url = self::base_url . '/flight';

        // Make a call to the AirLabsRoutes API
        // Create a cURL handle
        $ch = curl_init();

        // Prepare the URL with query parameters
        $queryParams = http_build_query([
            'api_key' => $this->api_key,
            'flight_iata' => $flight_iata,
        ]);
        $fullUrl = $url . '?' . $queryParams;
        //die($fullUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

        // Execute the cURL request
        $this->numcallsmade++;
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        // Close the cURL handle
        curl_close($ch);

        // Return the response from the API
        $res = json_decode($response, true);
        if (!empty($res['error'])) {
            //echo "<pre>getAirLabsFlighDetails: $flight_iata " . print_r($res['error'], true) . "</pre>";
            return false;
        }
        return $res['response'];
    } //getAirLabsFlighDetails



    function getAirLabsAirportDetails($airport_iata): array|bool
    {

        if (!empty($this->airports[$airport_iata])) {
            echo __METHOD__.": Using cached data for $airport_iata".self::LINEENDING;
            //print_r($this->airports[$airport_iata]);
            return $this->airports[$airport_iata];
        }

        //echo "In ".__FUNCTION__."($flight_iata)";
        $url = self::base_url . '/airports';

        // Make a call to the AirLabsRoutes API
        // Create a cURL handle
        $ch = curl_init();

        // Prepare the URL with query parameters
        $queryParams = http_build_query([
            'api_key' => $this->api_key,
            'iata_code' => $airport_iata,
        ]);
        $fullUrl = $url . '?' . $queryParams;
        //die($fullUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

        // Execute the cURL request
        $this->numcallsmade++;
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        // Close the cURL handle
        curl_close($ch);

        // Return the response from the API
        $res = json_decode($response, true);
        if (!empty($res['error'])) {
            //echo "<pre>getAirLabsAirportDetails: $airport_iata " . print_r($res['error'], true) . "</pre>";
            return false;
        }
        // cache result so we can save on API calls
        $this->airports[$airport_iata] = $res['response'];

        return $res['response'];
    } //getAirLabsAirportDetails

} // class Airlabs
