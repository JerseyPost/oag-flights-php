<?php
$folderPath = 'uploads/'; // Replace with the path to your folder

if (is_dir($folderPath)) {
    $files = scandir($folderPath);
    
    foreach ($files as $file) {
        $filePath = $folderPath . '/' . $file;
        
        // Check if it's a file and not the index.php file
        if (is_file($filePath) && $file !== 'index.php') {
            unlink($filePath); // Delete the file
            echo "Deleted: $file<br>";
        }
    }

    echo "Deletion completed!";
} else {
    echo "Folder not found!";
}
?>


<form action="index.php" method="post">
    <input type="submit" value="Home">
</form>