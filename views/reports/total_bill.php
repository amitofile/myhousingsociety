<?php

session_start();

if (filter_input(INPUT_GET, "code") !== $_SESSION['report_token']) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

require_once("../../includes/connect.php");

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Total-Bill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        table {
            font-size: small;
        }

        .number {
            text-align: right;
        }
    </style>
</head>
<?php

$sql = "SELECT o.flat, o.name, sbills.property, sbills.water, sbills.sinking, sbills.repair, sbills.service, sbills.tanker, sbills.nonocc, sbills.parking FROM flat_details o JOIN (SELECT flat, SUM(property) property, SUM(water) water, SUM(sinking) sinking, SUM(repair) repair, SUM(service) service, SUM(tanker) tanker, SUM(nonocc) nonocc, SUM(parking) parking FROM bills GROUP BY flat) sbills ON o.flat = sbills.flat";
$result = $conn->query($sql);

$time = date("Y-m-d h:i:sa");
$csv = [];
$x = 0;
$csv[$x] = ["Report generated at:", $time , "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
$x++;
$csv[$x] = ["", "", "", "", "", "", "", "", "", "", "", "", "", ""];
$x++;
$csv[$x] = ["Flat", "Owner", "Propetry Tax", "Water Charges", "Sinking Fund", "Repair Fund", "Service Charges", "Water Tanker", "Non Occupancy", "Parking Charges", "Total"];

?>

<body>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12"><a href="./views/reports/download.php?code=<?=$_SESSION['report_token']?>&file=total_bill">Report generated at: <?= $time ?></a></div>
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
                        $x++;
                        while ($row = $result->fetch_assoc()) {
                            $total['property'] += $row['property'];
                            $total['water'] += $row['water'];
                            $total['sinking'] += $row['sinking'];
                            $total['repair'] += $row['repair'];
                            $total['service'] += $row['service'];
                            $total['tanker'] += $row['tanker'];
                            $total['nonocc'] += $row['nonocc'];
                            $total['parking'] += $row['parking'];
                            $_total = $row['property'] + $row['water'] + $row['sinking'] + $row['repair'] + $row['service'] + $row['tanker'] + $row['nonocc'] + $row['parking'];
                            $total['total'] += $_total;
                    ?>
                            <tr>
                                <?php
                                $csv[$x] = [];
                                ?>
                                <td><?= $row['flat'] ?></td> <?php array_push($csv[$x], $row['flat']); ?>
                                <td><?= $row['name'] ?></td> <?php array_push($csv[$x], $row['name']); ?>
                                <td class="number"><?= $row['property'] ?></td><?php array_push($csv[$x], $row['property']); ?>
                                <td class="number"><?= $row['water'] ?></td><?php array_push($csv[$x], $row['water']); ?>
                                <td class="number"><?= $row['sinking'] ?></td><?php array_push($csv[$x], $row['sinking']); ?>
                                <td class="number"><?= $row['repair'] ?></td><?php array_push($csv[$x], $row['repair']); ?>
                                <td class="number"><?= $row['service'] ?></td><?php array_push($csv[$x], $row['service']); ?>
                                <td class="number"><?= $row['tanker'] ?></td><?php array_push($csv[$x], $row['tanker']); ?>
                                <td class="number"><?= $row['nonocc'] ?></td><?php array_push($csv[$x], $row['nonocc']); ?>
                                <td class="number"><?= $row['parking'] ?></td><?php array_push($csv[$x], $row['parking']); ?>
                                <th class="number"><?= $_total ?></th> <?php array_push($csv[$x], $_total); ?>
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
                            <th class="number"><?= $total['property'] ?></th> <?php array_push($csv[$x], $total['property']); ?>
                            <th class="number"><?= $total['water'] ?></th> <?php array_push($csv[$x], $total['water']); ?>
                            <th class="number"><?= $total['sinking'] ?></th> <?php array_push($csv[$x], $total['sinking']); ?>
                            <th class="number"><?= $total['repair'] ?></th> <?php array_push($csv[$x], $total['repair']); ?>
                            <th class="number"><?= $total['service'] ?></th> <?php array_push($csv[$x], $total['service']); ?>
                            <th class="number"><?= $total['tanker'] ?></th> <?php array_push($csv[$x], $total['tanker']); ?>
                            <th class="number"><?= $total['nonocc'] ?></th> <?php array_push($csv[$x], $total['nonocc']); ?>
                            <th class="number"><?= $total['parking'] ?></th> <?php array_push($csv[$x], $total['parking']); ?>
                            <th class="number"><?= $total['total'] ?></th> <?php array_push($csv[$x], $total['total']); ?>
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

$fp = fopen('/var/www/downloaddocs/reports/total_bill.csv', 'w');
foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
?>

</html>