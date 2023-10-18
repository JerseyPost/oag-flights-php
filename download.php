<?php

// Get the name of the file to download from the hidden field.
$fileName = htmlspecialchars($_POST["fileName"]);

// Check if the file exists.
if (file_exists('downloads/'.$fileName)) {

    // Set the headers to download the file.
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . $fileName);

    // Read the file and output it to the browser.
    readfile('downloads/'.$fileName);

} else {

    // Display an error message if the file does not exist.
    echo "The file downloads/$fileName does not exist.";

}

?>