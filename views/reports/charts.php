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
    <title>Charts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
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

echo '<pre>';

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
        $ok = true;
        while ($row = $result->fetch_assoc()) {
            foreach ($paid as $head => $value) {
                if ($amount >= $row[$head]) {
                    //$paid[$head] += $row[$head];
                    array_push($paid[$head], $row[$head] . '|' . $amount);
                    $amount -= $row[$head];
                } else {
                    //$paid[$head] += $amount;
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

$count_array = [
    4 => [],
    3 => [],
    2 => [],
    1 => [],
    0 => []
];

foreach ($flats as $flat => $head) {
    //echo print_r($head);
    array_push($count_array[count($head['property'])], $flat);
}

//print_r($count_array);

$delayed = $count_array[3];

//print_r($delayed);

$_delayed  = [];

$sql = "SELECT flat, date FROM payments WHERE flat IN (" . join(',', $delayed) . ")";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!isset($_delayed[$row['flat']]))
            $_delayed[$row['flat']] = [];

        array_push($_delayed[$row['flat']], $row['date']);
    }
}

//print_r($_delayed);

$payperiods = [];

foreach ($_delayed as $flat => $dates) {
    $payperiods[$flat] = ['end' => 0, 'mid' => 0, 'before' => 0];

    foreach ($dates as $date) {
        $d = date('d', strtotime($date));
        if ($d <= 20) {
            $payperiods[$flat]['before']++;
        } elseif ($d > 20 && $d <= 25) {
            $payperiods[$flat]['mid']++;
        } elseif ($d > 25) {
            $payperiods[$flat]['end']++;
        }
    }
}

//print_r($payperiods);

$probability = [
    'willpay' => [],
    'forget' => [],
    'stopped' => []
];

foreach ($payperiods as $flat => $payperiod) {
    $largest = ($payperiod['end'] > $payperiod['mid'])
        ? ($payperiod['end'] > $payperiod['before'] ? $payperiod['end'] : $payperiod['before'])
        : ($payperiod['mid'] > $payperiod['before'] ? $payperiod['mid'] : $payperiod['before']);

    if ($payperiod['end'] == $largest) {
        array_push($probability['willpay'], $flat);
    } elseif ($payperiod['mid'] == $largest) {
        array_push($probability['forget'], $flat);
    } elseif ($payperiod['before'] == $largest) {
        array_push($probability['stopped'], $flat);
    }
}

//print_r($probability);

?>

<body>
    <main>
        <div class="container">

            <div class="row">
                <div class="col-6">
                    <canvas id="myChart"></canvas>
                </div>
                <div class="col-6">
                    <canvas id="myChart1"></canvas>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
                const ctx = document.getElementById('myChart');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Regular Apr-Jul: <?= count($count_array[4]) ?>', 'Delayed Apr-Jun: <?= count($count_array[3]) ?>', 'Stopped Apr-May: <?= count($count_array[2]) ?>', 'Defaulter Apr: <?= count($count_array[1]) ?>', 'Zero 0: <?= count($count_array[0]) ?>'],
                        datasets: [{
                            label: '# Total',
                            data: [<?= count($count_array[4]) ?>, <?= count($count_array[3]) ?>, <?= count($count_array[2]) ?>, <?= count($count_array[1]) ?>, <?= count($count_array[0]) ?>],
                            borderWidth: 1,
                            backgroundColor: ['#27AE60', '#1F618D', '#F1C40F', '#CB4335', '#884EA0'],
                        }]
                    },
                });

                const ctx1 = document.getElementById('myChart1');
                new Chart(ctx1, {
                    type: 'pie',
                    data: {
                        labels: ['Expected: <?= count($probability['willpay']) ?>', 'Forget: <?= count($probability['forget']) ?>', 'Stopped: <?= count($probability['stopped']) ?>'],
                        datasets: [{
                            label: '# Total',
                            data: [<?= count($probability['willpay']) ?>, <?= count($probability['forget']) ?>, <?= count($probability['stopped']) ?>],
                            borderWidth: 1,
                            backgroundColor: ['#AED6F1', '#3498DB', '#154360'],
                        }]
                    },
                });
            </script>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</body>
<?php
$conn->close();

?>

</html>