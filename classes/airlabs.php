<?php

class AirLabs
{
    protected $api_key = '822be1f6-6997-42ad-9898-40326906b37e'; // JP

    const base_url = 'https://airlabs.co/api/v9';
    const LINEENDING = '<br/>';

    private $numcallsmade = 0;

    const CACHEFOLDER = 'cache/';
    const CACHETYPES = ['routes', 'flights', 'airports'];

    private $cache = [];

    public function __construct(bool $ignoreCache = false)
    {
        // check if AIRLABS_API_KEY env variable exists
        $api_env = getenv('AIRLABS_API_KEY');
        echo "AIRLABS_API_KEY = $api_env" . self::LINEENDING;
        if (!empty($api_env) && preg_match('/^[0-9a-z-]{36}$/', $api_env)) {
            $this->api_key = $api_env;
        }

        foreach (self::CACHETYPES as $ct) {
            $this->cache[$ct] = [];
        }

        if (!$ignoreCache) {
            foreach (self::CACHETYPES as $ct) {
                $this->populateCacheFromFile($ct);
            }
        }
    }

    public function extractFlightDetails($flight_iata)
    {
        $pattern = '/^([A-Z][A-Z0-9])(\d{1,4})$/';

        if (preg_match($pattern, trim($flight_iata), $matches)) {
            return [
                'airlineCode' => $matches[1],
                'flightNumber' => (int)$matches[2]  // dump leading zeroes
            ];
        }

        return null;
    }

    public function getNumcallsmade()
    {
        return $this->numcallsmade;
    }

    public function populateCacheFromFile(string $datatype)
    {
        $filePath = self::CACHEFOLDER . $datatype . '.json';

        if (file_exists($filePath)) {
            // Read the JSON content from the file
            $jsonContent = file_get_contents($filePath);

            // Decode the JSON content into a PHP array
            $data = json_decode($jsonContent, true);

            if ($data !== null) {
                // Check if the JSON decoding was successful
                $this->cache[$datatype] = $data;
                echo "Array read from $filePath as JSON.:".json_encode($data).self::LINEENDING;
            } else {
                echo "Failed to decode JSON.";
                $this->cache[$datatype] = [];
            }
        } else {
            echo "File $filePath not found.";
        }
    }

    public function deleteCache(string $datatype)
    {
        $filePath = self::CACHEFOLDER . $datatype . '.json';
        unlink($filePath);
        echo "$filePath Cache file deleted".self::LINEENDING;
    }

    public function persistCache(string $datatype)
    {
        $filePath = self::CACHEFOLDER . $datatype . '.json';

        // Encode the array to a JSON string and write it to the file
        file_put_contents($filePath, json_encode($this->cache[$datatype]));

        echo "Array written to the file $filePath as JSON.".self::LINEENDING;
    }

    private function cacheAirlabData(string $datatype, array $query, $data)
    {
        $this->cache[$datatype][implode('-', $query)] = $data;
    }

    private function checkCache(string $datatype, array $query)
    {
        $cacheIdx = implode('-', $query);
        $cacheHit = $this->cache[$datatype][$cacheIdx] ?? false;

        if ($cacheHit) {
            echo "CacheHit: $cacheIdx > ".json_encode($cacheHit) . self::LINEENDING;
        }
        return $cacheHit;
    }

    public function getAirLabsRoutes(string $originAirport = null, string $destinationAirport = null, string $flight_iata = null)
    {
        $cacheHit = $this->checkCache('routes', [$originAirport, $destinationAirport, $flight_iata]);
        if (!empty($cacheHit)) {
            return $cacheHit;
        }

        $url = self::base_url . '/routes';

        // Make a call to the AirLabsRoutes API
        $ch = curl_init();

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
        } else {
            $params = [
                'api_key' => $this->api_key,
                'dep_iata' => $originAirport,
                'arr_iata' => $destinationAirport
            ];
        }
        $queryParams = http_build_query($params);
        $fullUrl = $url . '?' . $queryParams;

        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $this->numcallsmade++;
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        $res = json_decode($response, true);
        if (!empty($res['error'])) {
            echo "<pre>getAirLabsRoutes: $originAirport, $destinationAirport " . print_r($res['error'], true) . "</pre>";
            return false;
        }

        $this->cacheAirlabData(
            'routes',
            [$originAirport, $destinationAirport, $flight_iata],
            $res['response']
        );
        return $res['response'];
    } //getAirLabsRoutes

    public function getAirLabsFlighDetails($flight_iata)
    {
        $cacheHit = $this->checkCache('flights', [$flight_iata]);
        if (!empty($cacheHit)) {
            return $cacheHit;
        }

        $url = self::base_url . '/flight';

        $ch = curl_init();

        $queryParams = http_build_query([
            'api_key' => $this->api_key,
            'flight_iata' => $flight_iata,
        ]);
        $fullUrl = $url . '?' . $queryParams;

        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $this->numcallsmade++;
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        $res = json_decode($response, true);
        if (!empty($res['error'])) {
            //echo "<pre>getAirLabsFlighDetails: $flight_iata " . print_r($res['error'], true) . "</pre>";
            return false;
        }
        $this->cacheAirlabData(
            'flights',
            [$flight_iata],
            $res['response']
        );
        return $res['response'];
    } //getAirLabsFlighDetails

    public function getAirLabsAirportDetails($airport_iata)
    {
        $cacheHit = $this->checkCache('airports', [$airport_iata]);
        if (!empty($cacheHit)) {
            return $cacheHit;
        }

        $url = self::base_url . '/airports';

        $ch = curl_init();

        $queryParams = http_build_query([
            'api_key' => $this->api_key,
            'iata_code' => $airport_iata,
        ]);
        $fullUrl = $url . '?' . $queryParams;

        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $this->numcallsmade++;
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        $res = json_decode($response, true);
        if (!empty($res['error'])) {
            //echo "<pre>getAirLabsAirportDetails: $airport_iata " . print_r($res['error'], true) . "</pre>";
            return false;
        }

        $this->cacheAirlabData(
            'airports',
            [$airport_iata],
            $res['response']
        );

        return $res['response'];
    } //getAirLabsAirportDetails
}
