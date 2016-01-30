<?php
/**
 * Created by PhpStorm.
 * User: Qwant
 * Date: 06-Nov-15
 * Time: 19:33
 * Version 3.0 at 14-Nov-15
 */
require_once 'shutdown.php';

function printMatrix($matrix, $matrixH, $matrixW)
{
    echo '</br>';
    for ($h = 0; $h < $matrixH; $h++) {  // Print wall
        echo '</br>';
        for ($w = 0; $w < $matrixW; $w++) {
            printf('|%2s', $matrix[$h][$w]);
        }
    }
}

function putBrick($brickH, $brickW, $positionH, $positionW, &$matrix)
{
    for ($h = 0; $h < $brickH; $h++)  // Is place free?
    {
        for ($w = 0; $w < $brickW; $w++) {
            if ($matrix[$positionH + $h][$positionW + $w] != 1) {
                return false;
            }
        }
    }
    return true;
}

/**
 * @param $firstBrick
 * @return bool
 */
function wallConstructor($firstBrick)
{
    global $countElementaryBricks, $maxBricksNumber, $bricksType, $bricksMatrix, $matrix, $matrixH, $matrixW;
    $prev = true;
    for ($bn = $firstBrick; $bn < $maxBricksNumber; $bn++):
        if ($bricksMatrix[$bn][0] == 1 && $bricksType[$bricksMatrix[$bn][1]] > 0) { //if brick didnt use & we can use it
            if ($prev || ($bn > 0 && ($bricksMatrix[$bn - 1][2] != $bricksMatrix[$bn][2] || $bricksMatrix[$bn - 1][3] != $bricksMatrix[$bn][3]))) { //if previous did false and same
                $brickH = $bricksMatrix[$bn][2];
                $brickW = $bricksMatrix[$bn][3];
                $brickS = $brickH * $brickW;
                for ($h = 0; $h <= $matrixH - $brickH; $h++):   // cutting wall if brick is bigger
                    for ($w = 0; $w <= $matrixW - $brickW; $w++):
                        if ($brickS <= $countElementaryBricks) {
                            if (putBrick($brickH, $brickW, $h, $w, $matrix)) {  // $bn - brick number
                                $backupMatrix = $matrix;
                                for ($hh = 0; $hh < $brickH; $hh++)  // Putting brick by own number+2
                                {
                                    for ($ww = 0; $ww < $brickW; $ww++) {
                                        $matrix[$h + $hh][$w + $ww] = $bn + 2;
                                    }
                                }
                                $bricksType[$bricksMatrix[$bn][1]]--;  // dec free bricks
                                $bricksMatrix[$bn][0] = 0; // brick is used
                                $countElementaryBricks -= $brickS;
                                if ($countElementaryBricks == 0) {
                                    printMatrix($matrix, $matrixH, $matrixW);
                                    return true;
                                }
                                if (wallConstructor($bn + 1)) {
                                    return true;
                                }
                                $bricksType[$bricksMatrix[$bn][1]]++;  // inc free bricks
                                $bricksMatrix[$bn][0] = 1; // brick is free
                                $countElementaryBricks += $brickS;
                                $matrix = $backupMatrix;
                            }
                        }
                    endfor;
                endfor;
            }
        }
        $prev = false;
    endfor;
    return false;
}

if (isset($_POST['sourceData'])) {


    $str = trim($_POST['sourceData']);
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
    foreach ($bricks as $b => $brick):
        // symmetrical bricks
        $bricksType[] = $brick[2];
        for ($c = 0; $c < $brick[2]; $c++) {
            if ($brick[0] * $brick[1] != 1) {
                $bricksMatrix[] = [1, $b, $brick[0], $brick[1]];
            } else {
                $countElementaryBricks--;  // Calculating count bricks with size 1x1
            }
        }
        // unsymmetrical (rotated) bricks
        if ($brick[0] != $brick[1]) {
            for ($c = 0; $c < $brick[2]; $c++) {
                $bricksMatrix[] = [1, $b, $brick[1], $brick[0]];
            }
        }
    endforeach;
    unset($bricks, $brick);

    $maxBricksNumber = count($bricksMatrix);
    for ($h = 0; $h < $matrixH; $h++)  // Calculating count elementary bricks w/o briks size 1x1
    {
        for ($w = 0; $w < $matrixW; $w++) {
            if ($matrix[$h][$w] == 1) {
                $countElementaryBricks++;
            }
        }
    }

    // time to execute
    if (isset($_POST['time'])) {
        ini_set('max_execution_time', $_POST['time']);
    }

    $start = microtime(true);

    echo wallConstructor(0) ? '</br>Yes</br>' : '</br>No</br>';

    $time = microtime(true) - $start;
    printf('Elapsed time %.4F sec.', $time);
    echo '</br>';
    $maxMemory = memory_get_peak_usage();
    printf('Max memory usage: %8d ', $maxMemory);
}