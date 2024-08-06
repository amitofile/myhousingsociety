<?php

session_start();

if (filter_input(INPUT_GET, "code") !== $_SESSION['report_token']) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

require_once("../../includes/connect.php");

$adv = filter_input(INPUT_GET, "adv");

require_once("../../includes/flats.php");


?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paid-Bill</title>
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
            font-size: small;
        }
    </style>
</head>
<?php

$sql = "SELECT flat, SUM(amount) amount FROM payments GROUP BY flat ORDER BY flat";
$result = $conn->query($sql);

$time = date("Y-m-d h:i:sa");
$csv = [];
$x = 0;
$csv[$x] = ["Report generated at:", $time . " " . ($adv && $adv == 1 ? " considering advance pay" : ""), "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
$x++;
$csv[$x] = ["", "", "", "", "", "", "", "", "", "", "", "", "", ""];
$x++;
$csv[$x] = ["Flat", "Owner", "Propetry Tax", "Water Charges", "Sinking Fund", "Repair Fund", "Service Charges", "Water Tanker", "Non Occupancy", "Parking Charges", "Total", "Arrears"];

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
        'property' => 0,
        'water' => 0,
        'sinking' => 0,
        'repair' => 0,
        'service' => 0,
        'tanker' => 0,
        'nonocc' => 0,
        'parking' => 0,
    ];

    $sql = "SELECT * FROM bills WHERE flat = " . $flat;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $ok = true;
        while ($row = $result->fetch_assoc()) {
            foreach ($paid as $head => $value) {
                if ($amount >= $row[$head]) {
                    $paid[$head] += $row[$head];
                    //array_push($paid[$head], $row[$head] . '|' . $amount);
                    $amount -= $row[$head];
                } else {
                    $paid[$head] += $amount;
                    //array_push($paid[$head], $row[$head] . '|' . $amount);
                    $amount = 0;
                    $ok = false;
                    break;
                }
            }
            if (!$ok)
                break;
        }
        $flats[$flat] = $paid;
    } else {
        echo "0 results";
    }
}

//print_r($flats);

$sql = "SELECT o.flat, o.name, sbills.property, sbills.water, sbills.sinking, sbills.repair, sbills.service, sbills.tanker, sbills.nonocc, sbills.parking FROM flat_details o JOIN (SELECT flat, SUM(property) property, SUM(water) water, SUM(sinking) sinking, SUM(repair) repair, SUM(service) service, SUM(tanker) tanker, SUM(nonocc) nonocc, SUM(parking) parking FROM bills GROUP BY flat) sbills ON o.flat = sbills.flat";
$result = $conn->query($sql);

?>

<body>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12"><a href="./views/reports/download.php?code=<?=$_SESSION['report_token']?>&file=paid_bill">Report generated at: <?= $time ?></a></div>
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
                    if ($result->num_rows > 0) {
                        $total = array(
                            'property' => 0,
                            'water' => 0,
                            'sinking' => 0,
                            'repair' => 0,
                            'service' => 0,
                            'tanker' => 0,
                            'nonocc' => 0,
                            'parking' => 0,
                            'total' => 0
                        );

                        $flats_total = array(
                            'property' => 0,
                            'water' => 0,
                            'sinking' => 0,
                            'repair' => 0,
                            'service' => 0,
                            'tanker' => 0,
                            'nonocc' => 0,
                            'parking' => 0,
                            'total' => 0
                        );
                        $x++;
                        while ($row = $result->fetch_assoc()) {
                            $total['property'] += $row['property'];
                            $flats_total['property'] += $flats[$row['flat']]['property'];
                            $total['water'] += $row['water'];
                            $flats_total['water'] += $flats[$row['flat']]['water'];
                            $total['sinking'] += $row['sinking'];
                            $flats_total['sinking'] += $flats[$row['flat']]['sinking'];
                            $total['repair'] += $row['repair'];
                            $flats_total['repair'] += $flats[$row['flat']]['repair'];
                            $total['service'] += $row['service'];
                            $flats_total['service'] += $flats[$row['flat']]['service'];
                            $total['tanker'] += $row['tanker'];
                            $flats_total['tanker'] += $flats[$row['flat']]['tanker'];
                            $total['nonocc'] += $row['nonocc'];
                            $flats_total['nonocc'] += $flats[$row['flat']]['nonocc'];
                            $total['parking'] += $row['parking'];
                            $flats_total['parking'] += $flats[$row['flat']]['parking'];

                            $_total = $row['property'] + $row['water'] + $row['sinking'] + $row['repair'] + $row['service'] + $row['tanker'] + $row['nonocc'] + $row['parking'];
                            $total['total'] += $_total;

                            $__total = $flats[$row['flat']]['property'] + $flats[$row['flat']]['water'] + $flats[$row['flat']]['sinking'] + $flats[$row['flat']]['repair'] + $flats[$row['flat']]['service'] + $flats[$row['flat']]['tanker'] + $flats[$row['flat']]['nonocc'] + $flats[$row['flat']]['parking'];
                            $flats_total['total'] += $__total;
                    ?>
                            <tr>
                                <?php
                                $csv[$x] = [];
                                ?>
                                <td><?= $row['flat'] ?></td> <?php array_push($csv[$x], $row['flat']); ?>
                                <td><?= $row['name'] ?> &nbsp;<span class="small-value"><?= $adv && $adv == 1 && $_flats[$row['flat']] != 0 ? '(' . $_flats[$row['flat']] . ')' : '' ?></span></td> <?php array_push($csv[$x], $row['name']); ?>
                                <td><span class="big-value"><?= $flats[$row['flat']]['property'] ?></span><span class="small-value">/<?= $row['property'] ?></span></td> <?php array_push($csv[$x], $flats[$row['flat']]['property']); ?>
                                <td><span class="big-value"><?= $flats[$row['flat']]['water'] ?></span><span class="small-value">/<?= $row['water'] ?></span></td> <?php array_push($csv[$x], $flats[$row['flat']]['water']); ?>
                                <td><span class="big-value"><?= $flats[$row['flat']]['sinking'] ?></span><span class="small-value">/<?= $row['sinking'] ?></span></td> <?php array_push($csv[$x], $flats[$row['flat']]['sinking']); ?>
                                <td><span class="big-value"><?= $flats[$row['flat']]['repair'] ?></span><span class="small-value">/<?= $row['repair'] ?></span></td> <?php array_push($csv[$x], $flats[$row['flat']]['repair']); ?>
                                <td><span class="big-value"><?= $flats[$row['flat']]['service'] ?></span><span class="small-value">/<?= $row['service'] ?></span></td> <?php array_push($csv[$x], $flats[$row['flat']]['service']); ?>
                                <td><span class="big-value"><?= $flats[$row['flat']]['tanker'] ?></span><span class="small-value">/<?= $row['tanker'] ?></span></td> <?php array_push($csv[$x], $flats[$row['flat']]['tanker']); ?>
                                <td><span class="big-value"><?= $flats[$row['flat']]['nonocc'] ?></span><span class="small-value">/<?= $row['nonocc'] ?></span></td> <?php array_push($csv[$x], $flats[$row['flat']]['nonocc']); ?>
                                <td><span class="big-value"><?= $flats[$row['flat']]['parking'] ?></span><span class="small-value">/<?= $row['parking'] ?></span></td> <?php array_push($csv[$x], $flats[$row['flat']]['parking']); ?>
                                <th><span class="big-value"><?= $__total ?></span><span class="small-value">/<?= $_total ?></span></th> <?php array_push($csv[$x], $__total); ?>
                                <th><span class="big-value"><?= $_total - $__total ?></span></th> <?php array_push($csv[$x], $_total - $__total); ?>
                            </tr>
                        <?php
                            $x++;
                        }
                        ?>
                        <tr class="table-secondary">
                            <?php
                            $csv[$x] = [];
                            ?>
                            <th></th> <?php array_push($csv[$x], ''); ?>
                            <th>Total</th> <?php array_push($csv[$x], 'Total'); ?>
                            <th><span class="big-value"><?= $flats_total['property'] ?></span><span class="small-value">/<?= $total['property'] ?></span></th> <?php array_push($csv[$x], $flats_total['property']); ?>
                            <th><span class="big-value"><?= $flats_total['water'] ?></span><span class="small-value">/<?= $total['water'] ?></span></th> <?php array_push($csv[$x], $flats_total['water']); ?>
                            <th><span class="big-value"><?= $flats_total['sinking'] ?></span><span class="small-value">/<?= $total['sinking'] ?></span></th> <?php array_push($csv[$x], $flats_total['sinking']); ?>
                            <th><span class="big-value"><?= $flats_total['repair'] ?></span><span class="small-value">/<?= $total['repair'] ?></span></th> <?php array_push($csv[$x], $flats_total['repair']); ?>
                            <th><span class="big-value"><?= $flats_total['service'] ?></span><span class="small-value">/<?= $total['service'] ?></span></th> <?php array_push($csv[$x], $flats_total['service']); ?>
                            <th><span class="big-value"><?= $flats_total['tanker'] ?></span><span class="small-value">/<?= $total['tanker'] ?></span></th> <?php array_push($csv[$x], $flats_total['tanker']); ?>
                            <th><span class="big-value"><?= $flats_total['nonocc'] ?></span><span class="small-value">/<?= $total['nonocc'] ?></span></th> <?php array_push($csv[$x], $flats_total['nonocc']); ?>
                            <th><span class="big-value"><?= $flats_total['parking'] ?></span><span class="small-value">/<?= $total['parking'] ?></span></th> <?php array_push($csv[$x], $flats_total['parking']); ?>
                            <th><span class="big-value"><?= $flats_total['total'] ?></span><span class="small-value">/<?= $total['total'] ?></span></th> <?php array_push($csv[$x], $flats_total['total']); ?>
                            <th><span class="big-value"><?= $total['total'] - $flats_total['total'] ?></span></th> <?php array_push($csv[$x], $total['total'] - $flats_total['total']); ?>
                        </tr>
                    <?php
                    } else {
                        echo "0 results";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</body>
<?php
$conn->close();

$fp = fopen('/var/www/downloaddocs/reports/paid_bill.csv', 'w');
foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
?>

</html>