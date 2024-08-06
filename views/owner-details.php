<?php

//This section will try to search for owners based on search keyword.

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

require_once '../includes/connect.php';
require_once '../includes/prop.php';

$keyword = filter_input(INPUT_POST, "keyword");

//Only alphabets accepted in search
if (!preg_match('/^[ a-zA-Z]+$/', $keyword)) {
?>
    <div class="row">
        <div class="alert alert-danger" role="alert"><?= $prop['noOwner'] ?></div>
    </div>
<?php
    return 0;
}

?>
<label class="h5"><?= $prop['nameSearch'] ?></label>
<?php
try {
    $sql = "SELECT * FROM owners o WHERE `name` LIKE '%" . $keyword . "%'";
    $result = $conn->query($sql);
?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <?php
                if ($result->num_rows > 0) {
                ?>
                    <div class="col-10">
                        <span style="font-weight: bold;"><?= $prop['name'] ?></span>
                    </div>
                    <div class="col-2">
                        <span style="font-weight: bold;"><?= $prop['flat'] ?>
                    </div>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                    ?>
                        <div class="col-10">
                            <span><?= $row['name'] ?></span>
                        </div>
                        <div class="col-2">
                            <a class="clickable" onclick="findFlat(<?= $row['flat'] ?>);"><?= $row['flat'] ?></a>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="row">
                        <div class="alert alert-danger" role="alert" style="text-align: center;"><?= $prop['noOwner'] ?></div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
<?php
} catch (\Throwable $th) {
?>
    <div class="row">
        <div class="alert alert-danger" role="alert">
            <?= $prop['failOwner'] ?>
        </div>
    </div>
<?php
}

$conn->close();