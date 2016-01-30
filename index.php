<?php
ini_set('max_execution_time', 3000); // default value
//error_reporting(E_ALL);  //development
error_reporting(0);  //production
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css" type="text/css">
</head>

<body>
<div class="container">
    <p></p>

    <div class="row">
        <div class="col-xs-12">
            <button class="btn btn-info" onclick="loadData(1)">Load data set 1</button>
            <button class="btn btn-info" onclick="loadData(2)">Load data set 2</button>
            <button class="btn btn-info" onclick="loadData(3)">Load data set 3</button>
            <button class="btn btn-info" onclick="loadData(4)">Load data set 4</button>
            <button class="btn btn-info" onclick="loadData(5)">Load data set 5</button>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-xs-12">
            <form action="index.php?calc=true" method="post">
                <div class="form-group">
                    <label for="time">Time to execute in seconds (default : 3000 sec.) :</label>
                    <input type="text" class="form-control" name="time" value="3000">
                </div>
                <hr>
                <div class="form-group">
                    <label>Input source data:</label>
                    <textarea rows="15" class="form-control" name="sourceData"></textarea>
                </div>

                <button type="submit" class="btn btn-success">Verification</button>
            </form>
            <!-- end form -->
            <?php
            if (isset($_GET['calc']) && $_GET['calc'] == true) require_once 'wallConstructor.php';
            ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</div>
</body>

</html>

<!-- 6 3 101101 111111 111111 4 1 1 4 2 1 6 1 3 1 3 4 5   -->
