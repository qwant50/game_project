<?php
/**
 * Created by PhpStorm.
 * User: Qwant
 * Date: 06-Nov-15
 * Time: 19:33
 * Version 4.0 at 14-Nov-15
 */
require_once 'shutdown.php';
require_once 'src/Constructor.php';


if (isset($_POST['sourceData']) && isset($_POST['time'])) {
    $constructor = new Constructor($_POST['time'], $_POST['sourceData']);
} else {
    return false;
};

$start = microtime(true);

if ($constructor->wallConstructor(0)) {
    $constructor->printMatrix();
    echo '</br>Yes</br>';
} else {
    echo '</br>No</br>';
}

$time = microtime(true) - $start;
printf('Elapsed time %.4F sec.', $time);
echo '</br>';
$maxMemory = memory_get_peak_usage();
printf('Max memory usage: %8d ', $maxMemory);
