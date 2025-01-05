<?php
// Loop through all header variables and print them
foreach (getallheaders() as $name => $value) {
    echo "$name: $value<br>";
}
?>
