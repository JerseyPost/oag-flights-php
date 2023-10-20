<!DOCTYPE html>
<html>

<head>
    <title>File Upload Form</title>
</head>

<body>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

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
        if ($file != "." && $file != ".." && $file != "index.php" && stripos($file, 'deleteme')===false) {
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

    <form action="purgefiles.php" method="post">
        <input type="submit" value="Purge all files">
    </form>

    <h1>File Upload Form</h1>

    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" value="Upload File">
    </form>

    <h1>Process Files</h1>
    <form action="process.php" method="post">
        <label for="">Flights Limit</label>
        <input name="flightslimit" type="number" value="1">
        <label for="">Routes Limit</label>
        <input name="routeslimit" type="number" value="3">
        <br/>
        <label for="html">Ignore Cache:</label>
        <input type="checkbox" name="ignoreCache" value="1" id="ignoreCache">
        <input type="submit" value="Process Latest Flights and Routes files in upload folder">
    </form>

</body>

</html>