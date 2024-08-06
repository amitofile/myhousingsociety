<?php

session_start();

if (filter_input(INPUT_GET, "code") !== $_SESSION['report_token']) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

require_once("../../includes/connect.php");

$adv = filter_input(INPUT_GET, "adv");
$more = filter_input(INPUT_GET, "more");

    require_once("../../includes/flats.php");

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paid-Monthwise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        table {
            font-size: x-small;
        }

        .small-value {
            font-size: xx-small;
        }

        .big-value {
            font-size: medium;
        }
    </style>
</head>

<?php

$sql = "SELECT flat, SUM(amount) amount FROM payments GROUP BY flat ORDER BY flat";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $flats[$row['flat']] = $flats[$row['flat']] + $row['amount'];
    }
} else {
    echo "0 results";
}

//echo '<pre>';

foreach ($flats as $flat => $amount) {
    $paid = [
        'property' => [],
        'water' => [],
        'sinking' => [],
        'repair' => [],
        'service' => [],
        'tanker' => [],
        'nonocc' => [],
        'parking' => [],
    ];
    $sql = "SELECT * FROM bills WHERE flat = " . $flat;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($paid as $head => $value) {
                if ($amount >= $row[$head]) {
                    array_push($paid[$head], $row[$head]);
                    $amount -= $row[$head];
                } else {
                    array_push($paid[$head], $amount);
                    $amount = 0;
                }
            }
        }
    } else {
        echo "0 results";
    }

    $_paid = [];
    $flat_details = (($conn->query("SELECT * FROM flat_details WHERE flat =" . $flat))->fetch_assoc());
    $_paid['owner'] = $flat_details['name'];
    $_paid['month'] = [];

    $sql = "SELECT `month`, (property + water + sinking + `repair` + service + tanker + nonocc + parking) total FROM bills WHERE flat =" . $flat;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $_paid['month'][$row['month']] = [];
            $total = 0;
            foreach ($paid as $key => $head) {
                $total += $head[$i];
            }
            $_paid['month'][$row['month']]['paid'] = $total;
            $_paid['month'][$row['month']]['total'] = $row['total'];
            $i++;
        }
    }

    $_paid['credit'] = $amount;
    $_paid['arrears'] = 0;
    $_paid['pending_chq'] = "NA";
    $_paid['mobile'] = $flat_details['mobile'];
    $_paid['email'] = $flat_details['email'];

    $sql = "SELECT * FROM payments WHERE `status`='PENDING' && flat =" . $flat;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $_paid['pending_chq'] = $row['mode'] . "/" . $row['date'] . "/" . $row['amount'];
        }
    }

    $flats[$flat] = $_paid;
}

//print_r($flats);

foreach ($flats as $key => $value) {
    $total1 = 0;
    $paid1 = 0;
    foreach ($value['month'] as $month => $amount) {
        $total1 += $amount['total'];
        $paid1 += $amount['paid'];
    }
    $flats[$key]['arrears'] = $total1 - $paid1;
}

//print_r($flats);

$time = date("Y-m-d h:i:sa");
$csv = [];
$x = 0;
$csv[$x] = ["Report generated at:", $time . " " . ($adv && $adv == 1 ? " considering advance pay" : ""), "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
$x++;
$csv[$x] = ["", "", "", "", "", "", "", "", "", "", "", "", "", ""];
$x++;
$csv[$x] = ["Flat"];

foreach (array_keys($flats[101]) as $key => $value) {
    if (is_array($flats[101][$value])) {
        foreach ($flats[101][$value] as $month => $z) {
            array_push($csv[$x], $month);
        }
    } else {
        if (($value == 'pending_chq' || $value == 'mobile' || $value == 'email') && !$more)
            continue;
        array_push($csv[$x], ucfirst($value));
    }
}
//print_r($csv);

?>

<body>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12"><a href="./views/reports/download.php?code=<?=$_SESSION['report_token']?>&file=paid_monthwise">Report generated at: <?= $time ?></a></div>
            </div>
            <hr>
            <table class="table">
                <thead>
                    <tr>
                        <?php
                        foreach ($csv[$x] as $i => $head) {
                        ?>
                            <th scope="col"><?= $head ?></th>
                        <?php
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $x++;
                    $total = [];
                    $_total = [];
                    $total['month'] = [];
                    $_total['month'] = [];
                    $total['credit'] = 0;
                    $total['arrears'] = 0;
                    $total['pending_chq'] = 0;

                    foreach ($flats as $key => $value) {
                        $csv[$x] = [];
                    ?>
                        <tr>
                            <td><?= $key ?></td> <?php array_push($csv[$x], $key); ?>
                            <td><?= $value['owner'] ?> <?= $adv && $adv == 1 && $_flats[$key] != 0 ? '(' . $_flats[$key] . ')' : '' ?></span></td><?php array_push($csv[$x], $value['owner']); ?>
                            <?php

                            foreach ($value['month'] as $month => $amount) {
                                if (!isset($total['month'][$month]))
                                    $total['month'][$month] = 0;
                                if (!isset($_total['month'][$month]))
                                    $_total['month'][$month] = 0;

                                $total['month'][$month] += $amount['total'];
                                $_total['month'][$month] += $amount['paid'];
                            ?>
                                <td <?= $amount['paid'] < $amount['total'] ? $amount['paid'] == 0 ? 'class="table-danger"' : 'class="table-warning"' : "" ?>><span class="big-value"><?= $amount['paid'] ?></span><span class="small-value">/<?= $amount['total'] ?></span></td> <?php array_push($csv[$x], $amount['paid']); ?>
                            <?php
                            }
                            ?>
                            <?php
                            $total['credit'] += $value['credit'];
                            $total['arrears'] += $value['arrears'];
                            $total['pending_chq'] += explode('/', $value['pending_chq'])[2] ?? 0;
                            ?>
                            <td><?= $value['credit'] ?></td> <?php array_push($csv[$x], $value['credit']); ?>
                            <td><?= $value['arrears'] ?></td> <?php array_push($csv[$x], $value['arrears']); ?>
                            <?php
                            if ($more) {
                            ?>
                                <td><?= $value['pending_chq'] ?> </td> <?php array_push($csv[$x], $value['pending_chq']); ?>
                                <td><?= $value['mobile'] ?> </td> <?php array_push($csv[$x], $value['mobile']); ?>
                                <td><?= $value['email'] ?> </td> <?php array_push($csv[$x], $value['email']); ?>
                            <?php
                            }
                            ?>
                        </tr>
                    <?php
                        $x++;
                    }
                    ?>
                    <tr class="table-secondary">
                        <?php
                        $csv[$x] = [];
                        ?>
                        <td></td><?php array_push($csv[$x], ""); ?>
                        <td>Total</td><?php array_push($csv[$x], "Total"); ?>
                        <?php
                        foreach ($_total['month'] as $month => $amount) {
                        ?>
                            <td><span class="big-value"><?= $amount ?></span><span class="small-value">/<?= $total['month'][$month] ?></span></td><?php array_push($csv[$x], $amount); ?>
                        <?php
                        }
                        ?>
                        <td><?= $total['credit'] ?></td><?php array_push($csv[$x], $total['credit']); ?>
                        <td><?= $total['arrears'] ?></td> <?php array_push($csv[$x], $total['arrears']); ?>
                        <?php
                        if ($more) {
                        ?>
                            <td><?= $total['pending_chq'] ?></td><?php array_push($csv[$x], $total['pending_chq']); ?>
                            <td></td><?php array_push($csv[$x], ""); ?>
                            <td></td><?php array_push($csv[$x], ""); ?>
                        <?php
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</body>
<?php
$conn->close();

$fp = fopen('/var/www/downloaddocs/reports/paid_monthwise.csv', 'w');
foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
?>

</html>