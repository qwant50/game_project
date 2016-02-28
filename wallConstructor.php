<?php
/**
 * Created by PhpStorm.
 * User: Qwant
 * Date: 06-Nov-15
 * Time: 19:33
 * Version 3.0 at 14-Nov-15
 */
require_once 'shutdown.php';

function printMatrix(&$matrix)
{
    echo '<br>';
    foreach ($matrix as $row) {  // Print wall
        echo '<br>';
        foreach ($row as $elem) {
            printf('|%2s', $elem);
        }
    }
}

function isPuttable($brickH, $brickW, $positionH, $positionW, &$matrix)
{
    for ($h = 0; $h < $brickH; $h++)  // Is the place free?
    {
        for ($w = 0; $w < $brickW; $w++) {
            if ($matrix[$positionH + $h][$positionW + $w] != 1) {
                return false;
            }
        }
    }
    return true;
}

function putBrick(&$bn, &$brickH, &$brickW, &$brickS)
{
    global $countElementaryBricks, $matrix, $matrixH, $matrixW;
    for ($h = 0; $h <= $matrixH - $brickH; $h++):   // cutting wall if brick is bigger
        for ($w = 0; $w <= $matrixW - $brickW; $w++):
            if ($brickS <= $countElementaryBricks) {
                if (isPuttable($brickH, $brickW, $h, $w, $matrix)) {  // $bn - brick number
                    $backupMatrix = $matrix;
                    for ($hh = 0; $hh < $brickH; $hh++)  // Putting brick by own number+2
                    {
                        for ($ww = 0; $ww < $brickW; $ww++) {
                            $matrix[$h + $hh][$w + $ww] = $bn + 2;
                        }
                    }

                    $bricksMatrix[$bn][0] = 0; // brick is used
                    $countElementaryBricks -= $brickS;
                    if ($countElementaryBricks == 0  || wallConstructor($bn + 1)) {
                        return true;
                    }
                    $bricksMatrix[$bn][0] = 1; // brick is free
                    $countElementaryBricks += $brickS;
                    $matrix = $backupMatrix;
                }
            };
        endfor;
    endfor;
}

/**
 * @param $firstBrick
 * @return bool
 */
function wallConstructor($firstBrick)
{
    global $maxBricksNumber, $bricksMatrix;
    $prev = true;
    for ($bn = $firstBrick; $bn < $maxBricksNumber; $bn++):
        if ($bricksMatrix[$bn][0] == 1) { //if brick didnt use
            if ($prev || ($bricksMatrix[$bn - 1][1] != $bricksMatrix[$bn][1]) || ($bricksMatrix[$bn - 1][2] != $bricksMatrix[$bn][2])) {

                $brickH = $bricksMatrix[$bn][1];
                $brickW = $bricksMatrix[$bn][2];
                $brickS = $brickH * $brickW;
                if (putBrick($bn, $brickH, $brickW, $brickS)) {
                    return true;
                };

                if ($brickW != $brickH) {
                    if (putBrick($bn, $brickW, $brickH, $brickS)) {
                        return true;
                    };
                }
                $prev = false;
            }
        }
    endfor;
    return false;
}

if (isset($_POST['sourceData']) && isset($_POST['time'])) {

    $time = (int)$_POST['time'];
    $sourceData = $_POST['sourceData'];
    if (!is_string($sourceData)) {
        throw new Exception('sourceData isn\'t a string');
    }
    if (!is_int($time)) {
        throw new Exception('time  isn\'t an integer');
    }

    $str = trim($sourceData);
    //preparing source data
    $data = preg_split('/[\t\r]/', $str);
    $data = array_map('trim', $data);
    list($matrixW, $matrixH) = explode(" ", $data[0]);
    $matrixH = (int)$matrixH;
    $matrixW = (int)$matrixW;
    for ($h = 0; $h < $matrixH; $h++) {
        for ($w = 0; $w < $matrixW; $w++) {
            $matrix[$h][] = (int)$data[$h + 1][$w];
        }
    }
    $bricksSorts = (int)$data[$matrixH + 1];

    for ($b = 0; $b < $bricksSorts; $b++) {
        $bricks[] = array_map('intval', explode(" ", $data[$matrixH + 2 + $b]));
    };

    // Presort bricks (bigger the first)
    usort($bricks, function ($p, $q) {
        return ($p[0] * $p[1] == $q[0] * $q[1]) ? 0 : ($p[0] * $p[1] < $q[0] * $q[1]) ? 1 : -1;
    });

    $countElementaryBricks = 0;
    // building full bricks matrix
   // $next = 0;
    foreach ($bricks as $brick):
        // symmetrical bricks
       // $next += $brick[2];
        for ($c = 0; $c < $brick[2]; $c++) {
            if ($brick[0] * $brick[1] != 1) {
                $bricksMatrix[] = [1, $brick[0], $brick[1]];
            } else {
                $countElementaryBricks--;  // Calculating count bricks with size 1x1
            }
        }
    endforeach;
    unset($bricks, $brick);

    $maxBricksNumber = count($bricksMatrix);

    foreach ($matrix as $row)  // Calculating count elementary bricks w/o briks size 1x1
    {
        $countElementaryBricks += array_sum($row);
    }

    // time to execute
    ini_set('max_execution_time', $time);


    $start = microtime(true);

    if (wallConstructor(0)) {
        printMatrix($matrix);
        echo '</br>Yes</br>';
    } else {
        echo '</br>No</br>';
    }
    $time = microtime(true) - $start;
    printf('Elapsed time %.4F sec.', $time);
    echo '</br>';
    $maxMemory = memory_get_peak_usage();
    printf('Max memory usage: %8d ', $maxMemory);
}