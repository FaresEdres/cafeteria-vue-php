<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);



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
