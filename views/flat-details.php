<?php

//This section will try to search for flat number.

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 404 Not Found', TRUE, 404);
    die("<h1>Not Found</h1><p>The requested URL was not found on this server.</p>");
}

session_start();

require_once '../includes/connect.php';
require_once '../includes/functions.php';
require_once '../includes/prop.php';

$flat = filter_input(INPUT_POST, "flat");

if (!is_numeric($flat)) {
?>
    <div class="row">
        <div class="alert alert-danger" role="alert"><?= $prop['noFlat'] ?></div>
    </div>
<?php
    return 0;
}

?>
<label class="h5"><?= $prop['flatNo'] ?> <?= $flat ?></label>
<?php
try {
    $sql = "SELECT * FROM owners o WHERE flat = " . $flat;
    $result = $conn->query($sql);
?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <?php
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                ?>
                        <div class="col-6">
                            <div class="col-sm-12"><i class="bi bi-<?= $i ?>-square"></i></div>
                            <div class="col-sm-12"><span><?= $row['name'] ?></span></div>
                        </div>
                    <?php
                        $i++;
                    }
                } else {
                    ?>
                    <div class="row">
                        <div class="alert alert-danger" role="alert" style="text-align: center;">
                            <?= $prop['noFlat'] ?>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
    if (isset($_SESSION['user']) && $_SESSION['user'] == $flat) {

        require_once '../includes/functions.php';
    ?>
        <label class="h5"><?= $prop['myDetails'] ?> </label>
        <div></div>
        <div class="accordion" id="accordionFlushExample">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        <?= $prop['email'] ?>
                    </button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <?php
                        $email_count = 0;
                        $sql = "SELECT flat, email from users u WHERE flat = " . $flat . " UNION SELECT flat, email FROM emails e WHERE flat = " . $flat;
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ++$email_count;
                                if ($email_count == 1 && isset($row['email'])) {
                                    $_SESSION['EMAIL'] = $row['email'];
                                }
                                //$email_part = explode('@', $row['email']);
                                //$email_part[0] = stringToSecret($email_part[0]);
                                //$email_masked = join("@", $email_part);
                        ?>
                                <div class="row">
                                    <div class="col-10">
                                        <span><?= $row['email'] ?></span><span style="font-size: x-small;">
                                            <?= $email_count == 1 ? " (Primary)" : "" ?></span>
                                    </div>
                                    <div class="col-2">
                                        <span class="bi bi-trash clickable" tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="<?= $prop['contactSociety'] ?>"></span>
                                    </div>
                                </div>

                            <?php
                            }
                        }
                        if ($email_count < 2) {
                            ?>
                            <div class="row" style="background-color: ghostwhite; padding: 10px;">
                                <div class="col-8">
                                    <input type="email" id="email" name="email" class="form-control" placeholder="<?= $prop['inputEmail'] ?>">
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-primary" id="save-email" onclick="saveEmail();"><?= $prop['add'] ?></button>
                                </div>
                            </div>
                            <div class="row" id="email-alert"></div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseZero" aria-expanded="false" aria-controls="flush-collapseZero">
                        <i class="bi bi-telephone"> Mobile</i>
                    </button>
                </h2>
                <div id="flush-collapseZero" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <?php
                        $mobile_count = 0;
                        $sql = "SELECT * FROM mobiles WHERE flat = " . $flat;
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ++$mobile_count;
                        ?>
                                <div class="row">
                                    <div class="col-10">
                                        <span><?= stringToSecret($row['mobile']) ?></span><span style="font-size: x-small;">
                                            <?= $mobile_count == 1 ? " (Primary)" : "" ?></span>
                                    </div>
                                    <div class="col-2">
                                        <span class="bi bi-trash clickable" tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="<?= $prop['contactSociety'] ?>"></span>
                                    </div>
                                </div>
                            <?php
                            }
                        }
                        if ($mobile_count < 2) {
                            ?>
                            <div class="row" style="background-color: ghostwhite; padding: 10px;">
                                <label for="mobile" class="form-label"><?= isset($_SESSION['EMAIL']) ? "" : $prop['emailFirst'] ?></label>
                                <div class="col-8">
                                    <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Type mobile number" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-primary" id="save-mobile" onclick="saveMobile();" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>><?= $prop['add'] ?></button>
                                </div>
                            </div>
                            <div class="row" id="mobile-alert"></div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                        <i class="bi bi-car-front-fill"> Vehicles</i>
                    </button>
                </h2>
                <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <?php
                        $vehicle_count = 0;
                        $sql = "SELECT * FROM vehicles WHERE flat = " . $flat;
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                        ?>
                                <div class="row">
                                    <div class="col-8">
                                        <span><?= $row['vhclno'] ?></span>
                                    </div>
                                    <div class="col-2">
                                        <span><?= $row['vhcltype'] == "4whlr" ? '<i class="bi bi-car-front-fill"></i>' : '<i class="bi bi-bicycle"></i>' ?></span>
                                    </div>
                                    <div class="col-2">
                                        <span class="bi bi-trash clickable" tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="<?= $prop['contactSociety'] ?>"></span>
                                    </div>
                                </div>
                            <?php
                                ++$vehicle_count;
                            }
                        }
                        if ($vehicle_count < 5) {
                            ?>
                            <div class="row" style="background-color: ghostwhite; padding: 10px;">
                                <label for="vhclno" class="form-label"><?= isset($_SESSION['EMAIL']) ? "" : $prop['emailFirst'] ?></label>
                                <div class="col-12">
                                    <input type="text" id="vhclno" name="vhclno" class="form-control" placeholder="Type vehicle number ex. MH04AB1234" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>>
                                </div>
                                <div class="col-8">
                                    <div class="btn-group" role="group" aria-label="Select vehicle type">
                                        <input type="radio" class="btn-check" name="vhcltype" id="btncar" autocomplete="off" value="4whlr" checked <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>>
                                        <label class="btn btn-outline-primary" for="btncar">4-Wheeler</label>
                                        <input type="radio" class="btn-check" name="vhcltype" id="btnbike" autocomplete="off" value="2whlr" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>>
                                        <label class="btn btn-outline-primary" for="btnbike">2-Wheeler</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-primary" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>><i class="bi bi-floppy" id="save-vehicle" onclick="saveVehicle()"> Add</i></button>
                                </div>
                            </div>
                            <div class="row" id="vehicle-alert"></div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                        <i class="bi bi-person-vcard"> Maid Details</i>
                    </button>
                </h2>
                <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <?php
                        $maid_count = 0;
                        $sql = "SELECT * FROM maids WHERE flat = " . $flat;
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                        ?>
                                <div class="row">
                                    <div class="col-10">
                                        <span><?= $row['maidname'] ?></span>
                                    </div>
                                    <div class="col-2">
                                        <span class="bi bi-trash clickable" tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="<?= $prop['contactSociety'] ?>"></span>
                                    </div>
                                </div>
                            <?php
                                ++$maid_count;
                            }
                        }
                        if ($maid_count < 4) {
                            ?>
                            <div class="row" style="background-color: ghostwhite; padding: 10px;">
                                <label for="maidname" class="form-label"><?= isset($_SESSION['EMAIL']) ? "" : $prop['emailFirst'] ?></label>
                                </label>
                                <div class="row">
                                    <div class="col-8">
                                        <input type="text" id="maidname" name="maidname" class="form-control" placeholder="Type your maid's name" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-primary" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>><i class="bi bi-floppy" id="save-maid" onclick="saveMaid()"> Add</i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="maid-alert"></div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                        <i class="bi bi-gitlab"> Pet Licenses</i>
                    </button>
                </h2>
                <div id="flush-collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <?php
                        $pet_count = 0;
                        $sql = "SELECT * FROM pets WHERE flat = " . $flat;
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                        ?>
                                <div class="row">
                                    <div class="col-10">
                                        <span><?= $row['petlicence'] ?></span>
                                    </div>
                                    <div class="col-2">
                                        <span class="bi bi-trash clickable" tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="<?= $prop['contactSociety'] ?>"></span>
                                    </div>
                                </div>
                            <?php
                                ++$pet_count;
                            }
                        }
                        if ($pet_count < 3) {
                            ?>
                            <div class="row" style="background-color: ghostwhite; padding: 10px;">
                                <label for="petlicence" class="form-label"><?= isset($_SESSION['EMAIL']) ? "" : $prop['emailFirst'] ?></label>
                                </label>
                                <div class="row">
                                    <div class="col-8">
                                        <input type="text" id="petlicence" name="petlicence" class="form-control" placeholder="Type license number" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-primary" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>><i class="bi bi-floppy" id="save-pet" onclick="savePet()"> Add</i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="pet-alert"></div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                        <i class="bi bi-people-fill"> Tenant Details</i>
                    </button>
                </h2>
                <div id="flush-collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <?php
                        $tenant_count = 0;
                        $sql = "SELECT * FROM tenants WHERE flat = " . $flat;
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                        ?>
                                <div class="row">
                                    <div class="col-6">
                                        <span style="font-size: small;"><?= $row['tenantname'] ?></span>
                                    </div>
                                    <div class="col-4">
                                        <span style="font-size: small;"><?= stringToSecret($row['tenantnumber']) ?></span>
                                    </div>
                                    <div class="col-2">
                                        <span class="bi bi-trash clickable" tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="<?= $prop['contactSociety'] ?>"></span>
                                    </div>
                                </div>
                            <?php
                                ++$tenant_count;
                            }
                        }
                        if ($tenant_count < 2) {
                            ?>
                            <div class="row" style="background-color: ghostwhite; padding: 10px;">
                                <label for="tenantname" class="form-label"><?= isset($_SESSION['EMAIL']) ? "" : $prop['emailFirst'] ?></label>
                                </label>
                                <div class="row">
                                    <div class="col-12">
                                        <input type="text" id="tenantname" name="tenantname" class="form-control" placeholder="Type tenant name" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>>
                                    </div>
                                    <div class="col-8">
                                        <input type="text" id="tenantnumber" name="tenantnumber" class="form-control" placeholder="Type mobile number" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-primary" <?= isset($_SESSION['EMAIL']) ? "" : "disabled" ?>><i class="bi bi-floppy" id="save-pet" onclick="saveTenant()"> Add</i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="pet-alert"></div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
            if ($_SESSION['role'] == 2) {
                $token = generateRandomString(16);
                $_SESSION['report_token'] = $token;
            ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSix" aria-expanded="false" aria-controls="flush-collapseSix">
                            <i class="bi bi-people-fill"> Reports</i>
                        </button>
                    </h2>
                    <div id="flush-collapseSix" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-3">Total Bill</div>
                                <div class="col-3"><a href="./views/reports/total_bill.php?code=<?= $token ?>" target="_blank">View</a></div>
                            </div>
                            <div class="row">
                                <div class="col-3">Paid Bill</div>
                                <div class="col-3"><a href="./views/reports/paid_bill.php?code=<?= $token ?>" target="_blank">View</a></div>
                            </div>
                            <div class="row">
                                <div class="col-3">Paid Monthwise Bill</div>
                                <div class="col-3"><a href="./views/reports/paid_monthwise.php?code=<?= $token ?>" target="_blank">View</a></div>
                            </div>
                            <div class="row">
                                <div class="col-3">Chart</div>
                                <div class="col-3"><a href="./views/reports/charts.php?code=<?= $token ?>" target="_blank">View</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <script>
            popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

            function saveEmail() {
                if ($('#email').val()) {
                    $.post("./includes/save-email.php", {
                            email: $('#email').val()
                        }, function(data) {
                            $("#modal-body").html(data);
                        })
                        .done(function() {
                            $('#exampleModalLabel').html('<?= $prop['modalEmail'] ?>');
                            $('#modal1').modal('show');
                        })
                        .fail(function() {
                            $("#email-alert").html('<label style="color: red;"><?= $prop['failEmail'] ?></label>');
                            setTimeout(() => {
                                $("#email-alert").html("");
                            }, 5000);
                        });
                } else {
                    $("#email-alert").html('<label style="color: red;"><?= $prop['blankInput'] ?></label>');
                    setTimeout(() => {
                        $("#email-alert").html("");
                    }, 5000);
                    $('#email').focus();
                }
            }

            function saveMobile() {
                if ($('#mobile').val()) {
                    $.post("./includes/save-mobile.php", {
                            mobile: $('#mobile').val()
                        }, function(data) {
                            $("#modal-body").html(data);
                        })
                        .done(function() {
                            $('#exampleModalLabel').html('Update <i class="bi bi-telephone"> Mobile Number</i>');
                            $('#modal1').modal('show');
                        })
                        .fail(function() {
                            $("#mobile-alert").html(
                                '<label style="color: red;"><i class="bi bi-x-octagon"></i> Unable to save mobile this time.</label>'
                            );
                        });
                } else {
                    $("#mobile-alert").html(
                        '<label style="color: red;"><i class="bi bi-exclamation-triangle"></i> We cannot save blank mobile number.</label>'
                    );
                    $('#mobile').focus();
                }
            }

            function saveVehicle() {
                if ($('#vhclno').val()) {
                    $.post("./includes/save-vehicle.php", {
                            vhclno: $('#vhclno').val(),
                            vhcltype: $('input[name = "vhcltype"]:checked').val()
                        }, function(data) {
                            $("#modal-body").html(data);
                        })
                        .done(function() {
                            $('#exampleModalLabel').html('Update <i class="bi bi-car-front-fill"> Vehicle Number</i>');
                            $('#modal1').modal('show');
                        })
                        .fail(function() {
                            $("#vehicle-alert").html(
                                '<label style="color: red;"><i class="bi bi-x-octagon"></i> Unable to save vehicle this time.</label>'
                            );
                        });
                } else {
                    $("#vehicle-alert").html(
                        '<label style="color: red;"><i class="bi bi-exclamation-triangle"></i> We cannot save blank vehicle number.</label>'
                    );
                    $('#vhclno').focus();
                }
            }

            function saveMaid() {
                if ($('#maidname').val()) {
                    $.post("./includes/save-maid.php", {
                            maidname: $('#maidname').val()
                        }, function(data) {
                            $("#modal-body").html(data);
                        })
                        .done(function() {
                            $('#exampleModalLabel').html('Update <i class="bi bi-person-vcard"> Maid Details </i>');
                            $('#modal1').modal('show');
                        })
                        .fail(function() {
                            $("#maid-alert").html(
                                '<label style="color: red;"><i class="bi bi-x-octagon"></i> Unable to save maid details this time.</label>'
                            );
                        });
                } else {
                    $("#maid-alert").html(
                        '<label style="color: red;"><i class="bi bi-exclamation-triangle"></i> We cannot save blank maid name.</label>'
                    );
                    $('#maidname').focus();
                }
            }

            function savePet() {
                if ($('#petlicence').val()) {
                    $.post("./includes/save-pet.php", {
                            petlicence: $('#petlicence').val()
                        }, function(data) {
                            $("#modal-body").html(data);
                        })
                        .done(function() {
                            $('#exampleModalLabel').html('Update <i class="bi bi-gitlab"> Pet Licenses </i>');
                            $('#modal1').modal('show');
                        })
                        .fail(function() {
                            $("#pet-alert").html(
                                '<label style="color: red;"><i class="bi bi-x-octagon"></i> Unable to save pet license details this time.</label>'
                            );
                        });
                } else {
                    $("#pet-alert").html(
                        '<label style="color: red;"><i class="bi bi-exclamation-triangle"></i> We cannot save blank license number.</label>'
                    );
                    $('#petlicence').focus();
                }
            }

            function saveTenant() {
                if ($('#tenantname').val()) {
                    $.post("./includes/save-tenant.php", {
                            tenantname: $('#tenantname').val(),
                            tenantnumber: $('#tenantnumber').val()
                        }, function(data) {
                            $("#modal-body").html(data);
                        })
                        .done(function() {
                            $('#exampleModalLabel').html('Update <i class="bi bi-people-fill"> Tenant Details</i>');
                            $('#modal1').modal('show');
                        })
                        .fail(function() {
                            $("#pet-alert").html(
                                '<label style="color: red;"><i class="bi bi-x-octagon"></i> Unable to save tenant details this time.</label>'
                            );
                        });
                } else {
                    $("#pet-alert").html(
                        '<label style="color: red;"><i class="bi bi-exclamation-triangle"></i> We cannot save blank Tenant name.</label>'
                    );
                    $('#tenantname').focus();
                }
            }
        </script>

    <?php
    }
} catch (\Throwable $th) {
    ?>
    <div class="row">
        <div class="alert alert-danger" role="alert">
            <?= $prop['failFlat'] ?>
        </div>
    </div>
<?php
}

?>

<?php
$conn->close();
