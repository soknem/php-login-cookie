<?php
if (!extension_loaded('pdo_sqlite')) {
    echo "pdo_sqlite not loaded!";
} else {
    echo "pdo_sqlite is loaded!";
}
?>
