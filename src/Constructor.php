<?php

/**
 * Class Constructor
 */
class Constructor
{
    private $countElementaryBricks;
    private $maxBricksNumber;
    private $bricksMatrix;

    private $matrix;
    private $matrixH;
    private $matrixW;


    public function __construct($time, $sourceData)
    {
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

        $next = 0;
        foreach ($bricks as $brick):
            // symmetrical bricks
            $next += $brick[2];
            for ($c = 0; $c < $brick[2]; $c++) {
                if ($brick[0] * $brick[1] != 1) {
                    $bricksMatrix[] = [1, $brick[0], $brick[1], $next];
                } else {
                    $countElementaryBricks--;  // Calculating count bricks with size 1x1
                }
            }
        endforeach;
        $maxBricksNumber = count($bricksMatrix);
        unset($bricks, $brick, $c);
        $before = 0;
        for ($c = $maxBricksNumber - 1; $c >= 0; $c--) {
            if ($bricksMatrix[$c][1] * $bricksMatrix[$c][2] != 1) {
                $bricksMatrix[$c][4] = $bricksMatrix[$c][1] * $bricksMatrix[$c][2] + $before;
                $before = $bricksMatrix[$c][4];
            }
        }

        foreach ($matrix as $row)  // Calculating count elementary bricks w/o briks size 1x1
        {
            $countElementaryBricks += array_sum($row);
        }

        // time to execute
        ini_set('max_execution_time', $time);

        $this->countElementaryBricks = $countElementaryBricks;
        $this->maxBricksNumber = $maxBricksNumber;
        $this->bricksMatrix = $bricksMatrix;

        $this->matrix = $matrix;
        $this->matrixH = $matrixH;
        $this->matrixW = $matrixW;
    }

    public function printMatrix()
    {
        echo '<br>';
        foreach ($this->matrix as $row) {  // Print wall
            echo '<br>';
            foreach ($row as $elem) {
                printf('|%2s', $elem);
            }
        }
    }

    public function isPuttable($brickH, $brickW, $positionH, $positionW, &$matrix)
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

    public function putBrick(&$bn, &$brickH, &$brickW, &$brickS)
    {
        $matrixH = $this->matrixH;
        $matrixW = $this->matrixW;
        $bricksMatrix = $this->bricksMatrix;

        for ($h = 0; $h <= $matrixH - $brickH; $h++):   // cutting wall if brick is bigger
            for ($w = 0; $w <= $matrixW - $brickW; $w++):
                if ($this->isPuttable($brickH, $brickW, $h, $w, $this->matrix)) {  // $bn - brick number
                    $backupMatrix = $this->matrix;
                    for ($hh = 0; $hh < $brickH; $hh++)  // Putting brick by own number+2
                    {
                        for ($ww = 0; $ww < $brickW; $ww++) {
                            $this->matrix[$h + $hh][$w + $ww] = $bn + 2;
                        }
                    }
                    $bricksMatrix[$bn][0] = 0; // brick is used
                    $this->countElementaryBricks -= $brickS;
                    if ($this->countElementaryBricks == 0 || $this->wallConstructor($bn + 1)) {
                        return 1;
                    }
                    $bricksMatrix[$bn][0] = 1; // brick is free
                    $this->countElementaryBricks += $brickS;
                    $this->matrix = $backupMatrix;
                }
            endfor;
        endfor;

        return 0;
    }

    public function wallConstructor($bn)
    {
        while ($bn < $this->maxBricksNumber):
            if ($this->bricksMatrix[$bn][0] != 1) { //if brick is free goto next
                $bn++;
            } elseif ($this->bricksMatrix[$bn][4] < $this->countElementaryBricks) {
                return false;
            } else {
                $brickH = $this->bricksMatrix[$bn][1];
                $brickW = $this->bricksMatrix[$bn][2];
                $brickS = $brickH * $brickW;
                if ($brickS <= $this->countElementaryBricks) {
                    if ($this->putBrick($bn, $brickH, $brickW, $brickS) == 1) {
                        return true;
                    } elseif ($brickW != $brickH) {
                        if ($this->putBrick($bn, $brickW, $brickH, $brickS) == 1) {
                            return true;
                        };
                    }
                };
                $bn = $this->bricksMatrix[$bn][3];
            };
        endwhile;
        return false;
    }
}