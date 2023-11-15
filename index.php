<!DOCTYPE html>
<html>

<head>
    <title>File Upload Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css">
</head>

<body class="container">

    <?php

    $uploadDirectory = 'uploads/';

    // Get an array of all the files in the upload folder.
    $files = scandir($uploadDirectory);

    echo "<h1 class='mt-4'>Currently uploaded files</h1>";

    // Create an empty HTML table with Bootstrap table classes.
    $htmlTable = "<table class='table table-striped table-bordered table-hover mt-2'>";

    // Add a table header row with Bootstrap table classes.
    $htmlTable .= "<thead class='thead-dark'>";
    $htmlTable .= "<tr>";
    $htmlTable .= "<th>File Name</th>";
    $htmlTable .= "<th>Timestamp</th>";
    $htmlTable .= "</tr>";
    $htmlTable .= "</thead>";

    // Add a table body row for each file with Bootstrap table classes.
    $htmlTable .= "<tbody>";
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && $file != "index.php" && stripos($file, 'deleteme') === false) {
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

    <form action="purgefiles.php" method="GET">
    <button type="submit" class="btn btn-primary" onclick="confirm('Are you sure?')">Purge Files</button>
    </form>
  
    <div class="row mt-4">
        <div class="col-md-6">
            <h1>File Upload Form</h1>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="file" class="form-label">Choose a file to upload:</label>
                    <input type="file" class="form-control" name="file" id="file">
                </div>
                <button type="submit" class="btn btn-primary">Upload File</button>
            </form>
        </div>

        <div class="col-md-6">
            <h1>Delete Cache</h1>
            <form action="deletecache.php" method="post">
                <button type="submit" class="btn btn-danger">Delete Flights, Routes and Airports Cache Files</button>
            </form>

            <h1 class="mt-4">Process Files</h1>
            <form action="process.php" method="post">
                <div class="mb-3">
                    <label for="flightslimit" class="form-label">Flights Limit</label>
                    <input name="flightslimit" type="number" class="form-control" value="1" id="flightslimit">
                </div>
                <div class="mb-3">
                    <label for="routeslimit" class="form-label">Routes Limit</label>
                    <input name="routeslimit" type="number" class="form-control" value="3" id="routeslimit">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="ignoreCache" value="1" id="ignoreCache">
                    <label class="form-check-label" for="ignoreCache">Ignore Cache</label>
                </div>
                <button type="submit" class="btn btn-primary">Process Latest Flights and Routes files in upload folder</button>
            </form>
        </div>
    </div>

</body>

</html>
