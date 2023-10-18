<?php

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
$uploadDirectory = "upload";
$fileName = $_FILES["file"]["name"];
move_uploaded_file($_FILES["file"]["tmp_name"], $uploadDirectory . "/" . $fileName);

// Display a success message to the user.
echo "The file was uploaded successfully.";

?>