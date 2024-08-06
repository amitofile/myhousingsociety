<?php

//This section will inbclude society details.

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

require_once '../includes/connect.php';
require_once '../includes/prop.php';

try {
    $sql = "SELECT * FROM society s";
    $result = $conn->query($sql);
?>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="col-12"><span class="society-name"><?= $row['name'] ?></span></div>
                <div class="row">
                    <table class="table table-light table-striped">
                        <tbody>
                            <tr>
                                <th><?= $prop['chairman'] ?></th>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td><?= $row['chairman'] ?></td>
                            </tr>
                            <tr>
                                <th><?= $prop['secretary'] ?></th>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td><?= $row['secretary'] ?></td>
                            </tr>
                            <tr>
                                <th><?= $prop['treasurer'] ?></th>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td><?= $row['treasurer'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <label class="label1"><?= $prop['impcontacts'] ?></label>
                    <table class="table">
                        <tbody>
                            <?php
                            foreach (json_decode($row['contacts']) as $key => $value) {
                            ?>
                                <tr>
                                    <td><?= $key ?></td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td><?= $value ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="row">
                <div class="alert alert-danger" role="alert" style="text-align: center;">
                    <?= $prop['noSociety'] ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
<?php
} catch (\Throwable $th) {
?>
    <div class="row">
        <div class="alert alert-danger" role="alert">
            <?= $prop['failSociety'] ?>
        </div>
    </div>
<?php
}

$conn->close();
