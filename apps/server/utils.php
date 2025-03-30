<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';




function displayError($error_message){
    echo '<div class="alert alert-danger" role="alert">';
    echo "<h2 class='mb-0'>❌ { $error_message} </h2> </div>";
}

function appendDataToFile($fileName, $data) {
    $file = fopen($fileName, "a");
    if ($file) {
        fwrite($file, $data);
        fclose($file);
        return true;
    }
    return false;
}

?>