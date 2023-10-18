<!DOCTYPE html>
<html>
<head>
<title>File Upload Form</title>
</head>
<body>
    
<?php
$uploadDirectory = 'uploads/';

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

<h1>File Upload Form</h1>

<form action="upload.php" method="post" enctype="multipart/form-data">
<input type="file" name="file">
<input type="submit" value="Upload File">
</form>

<h1>Process Files</h1>
<form action="process.php" method="post">
    <input type="submit" value="Process Latest Flights and Routes files in upload folder">
</form>

</body>
</html>