<?php
/**
 * Created by PhpStorm.
 * User: Qwant
 * Date: 04-Jan-16
 * Time: 21:36
 */

register_shutdown_function('time_shutdown');

function time_shutdown()
{
    $error = error_get_last();
    if ($error['type'] === E_ERROR) {
        // fatal error has occured
        if (strrpos($error['message'], "Maximum execution time of") !== false) {
            echo "<br>No, " . $error['message'];
        }

    }

}
