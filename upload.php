<?php
require_once 'bootstrap.php';

// Check if the file was uploaded successfully.
if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != 0) {
    echo "An error occurred while uploading the file.";
    exit;
}

// Check if the file size is within the allowed limit.
if ($_FILES["file"]["size"] > 1000000) {
    echo "The file is too large. The maximum allowed file size is 1MB.";
    exit;
}

// Check if the file type is allowed.
$allowedFileTypes = ["text/csv"];
if (!in_array($_FILES["file"]["type"], $allowedFileTypes)) {
    echo "The file type is not allowed. Only CSV files are allowed.";
    exit;
}

// Save the file to a secure location on the server.
$uploadDirectory = "uploads";
$fileName = $_FILES["file"]["name"];
$result = move_uploaded_file($_FILES["file"]["tmp_name"], $uploadDirectory . "/" . $fileName);

// Display a success message to the user.
if ($result) {
    echo "The file was uploaded successfully to ".$uploadDirectory . "/" . $fileName;
} else {
    echo "The file FAILED to upload to ".$uploadDirectory . "/" . $fileName;
}

// ----------------------------------

// Get an array of all the files in the upload folder.
$files = scandir($uploadDirectory);

echo "<h1>Currently uploaded files</h1>";

// Create an empty HTML table.
$htmlTable = "<table>";

// Add a table header row.
$htmlTable .= "<thead>";
$htmlTable .= "<tr>";
$htmlTable .= "<th>File Name</th>";
$htmlTable .= "<th>Timestamp</th>";
$htmlTable .= "</tr>";
$htmlTable .= "</thead>";

// Add a table body row for each file.
$htmlTable .= "<tbody>";
foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        $htmlTable .= "<tr>";
        $htmlTable .= "<td>" . $file . "</td>";
        $htmlTable .= "<td>" . date("Y-m-d H:i:s", filemtime($uploadDirectory . "/" . $file)) . "</td>";
        $htmlTable .= "</tr>";
    }
}
$htmlTable .= "</tbody>";

// Close the HTML table.
$htmlTable .= "</table>";

// Echo the HTML table to the screen.
echo $htmlTable;



?>

<form action="process.php" method="post">
    <input type="submit" value="Process">
</form>
