<?php

class JPUtils
{
    public static function stringToCsv($string, $delimiter = ',')
    {
        $data = explode($delimiter, $string);  // Convert your string into an array
        $handle = fopen('php://temp', 'rw');  // Open a temporary stream in read/write mode
        // PHP8 only: fputcsv($handle, $data, ',', '"', '\\', "\r\n");  // Write the data to the stream in CSV format, and force Windows EOL
        fputcsv($handle, $data, ',', '"');
        // Add "\r\n" manually for line endings
        fwrite($handle, "\r\n");
        rewind($handle);  // Rewind the stream to the beginning
        $csvContent = stream_get_contents($handle);  // Read the contents of the stream
        fclose($handle);  // Close the stream
        return $csvContent;
    }

    // Define a function to calculate the number of stops
    public static function calculateNumStops($flight)
    {
        // Count the number of legs in the flight
        $numLegs = count($flight['legs']);

        // If there is more than one leg, then the flight has at least one stop
        return $numLegs > 1 ? 1 : 0;
    }


    public static function getDayNumbers($daysArray)
    {
        // Define a predefined array of days in the desired order with Monday as the start of the week
        $predefinedDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        // Initialize an empty string to hold the result
        $result = '';

        // Iterate through the predefined array of days
        foreach ($predefinedDays as $i => $predefinedDay) {
            // Check if the current day is present in the given array
            $dayExists = in_array($predefinedDay, array_map('strtolower', $daysArray));

            if ($dayExists) {
                // If the day is present, append the day number to the result string
                $result .= ($i + 1);
            } else {
                // If the day is not present, append a space character to the result string
                $result .= ' ';
            }
        }

        return $result;
    } // getDayNumbers


    public static function getLastSundayInMarch(): string
    {
        $nextYear = (int)(date("Y")) + 1;
        $date = new DateTime("last day of March $nextYear");
        while ($date->format('l') !== 'Sunday') {
            $date->modify('-1 day');
        }
        return $date->format('Ymd');;
    }

    public static function calculateArrivalDay($departureTime, $minutesToAdd)
    {
        if (empty($minutesToAdd)) {
            echo "Skipping calculateArrivalDay($departureTime, $minutesToAdd) - assuming 0" . PHP_EOL;
            return 0;
        }
        // Create DateTime object from departure time
        $departure = DateTime::createFromFormat('H:i', $departureTime, new DateTimeZone('UTC'));

        // Clone departure to keep it unchanged
        $arrival = clone $departure;

        // Add minutes to get the arrival time
        $arrival->modify("+{$minutesToAdd} minutes");

        // Calculate the difference in days
        $interval = $departure->diff($arrival);
        $daysDifference = $interval->days;

        return $daysDifference;
    } // calculateArrivalDay


    public static function sendFile($filePath)
    {
        // Check if file exists
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo "File not found.";
            exit;
        }

        // Get file info
        $fileSize = filesize($filePath);
        $fileName = basename($filePath);
        $fileMime = mime_content_type($filePath);

        // Set headers to force download
        header("Content-Type: $fileMime");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Content-Length: $fileSize");

        // Read file content and send to user
        readfile($filePath);
    }
}
