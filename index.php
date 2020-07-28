<?php

require_once("./src/functions.php");
$theStudent = getStudentDetails($config["studentData"]);
$theActiveSeasons = getActivedSeasons(isset($config["sezonData"]) ? $config["sezonData"] : null);
$theUnPassedSubjects = getUnPassedSubjects([$theStudent["student_ID"], $theActiveSeasons]);


?>





<!DOCTYPE html>
<html>

<head>
    <title>Page Title</title>
    <link rel="stylesheet" type="text/css" href="assets/style.css">
</head>

<body>


    <div class="userDetails">
        <b style="font-size:25;">Pershendetje: <?php echo $theStudent["Emri"] . " " . $theStudent["Mbiemri"] ?>!</b>
        <br>
        <p>Me poshte do te gjeni listen e lendeve jo kaluese !</p>
        <?php if ($theActiveSeasons) { ?>
        <p>
            <h2>
                Afati per Rregjistrimin e Lendeve mbaron ne daten <b style="color:red"><?php echo $config["sezonData"]["expiredDate"]; ?></b>
            </h2>
        </p>
        <?php } ?>
    </div>

    <div class="table">
        <?php if ($theActiveSeasons) { ?>
            <form name="foo" method="POST" id="foo"></form>
            <table class="tabela">
                <thead>
                    <th style="max-width:10px;text-align:center;">
                        Nr
                        <!-- <input type="checkbox" id="vehicle1" name="vehicle1" value="Bike"> -->
                    </th>
                    <th>Lenda</th>
                    <th>Nota</th>
                    <th>AfatiRegjistrimit</th>
                    <th>DataProvimit</th>
                    <th>Rezervo</th>
                </thead>
                <tbody>
                    <?php if ($theUnPassedSubjects) {
                        foreach ($theUnPassedSubjects as $key => $item) {
                            $examDateData =  getSingleExamDate($item["lenda_ID"], $item['sezoni_ID']);
                    ?>
                            <tr>
                                <td style="max-width:10px;text-align:center;">
                                    <?php echo ++$key ?>
                                    <!-- <input type="checkbox" id="vehicle1" name="vehicle1" value="Bike"> -->
                                </td>

                                <td>
                                    <?php echo getSingleSubject($item["lenda_ID"], "title"); ?>
                                </td>

                                <td style="text-align:center">
                                    <?php echo $item["Nota"]; ?>
                                </td>
                                <td style="text-align:center">
                                    <?php echo $examDateData["AfatiRegjistrimit"]; ?>
                                </td>
                                <td style="text-align:center">
                                    <?php echo $examDateData["DataProvimit"]; ?>
                                </td>
                                <td style="text-align:center" id="reservationdiv-<?php echo $item['lenda_ID']; ?>">

                                    <?php if (checkIfreservationExist($examDateData['data_ID'], $item['lenda_ID'],  $item['sezoni_ID'], $theStudent["student_ID"])) { ?>
                                        <b>Rezervuar ne <?php echo checkIfreservationExist($examDateData['data_ID'], $item['lenda_ID'],  $item['sezoni_ID'], $theStudent["student_ID"])["DataRegjistrimitOnline"] ?> </b>
                                    <?php } else { ?>
                                        <button onclick="rezervo(<?php echo $item['lenda_ID']; ?>,<?php echo $examDateData['data_ID']; ?>)">Rezervo</button>
                                    <?php }; ?>
                                </td>
                            </tr>
                        <?php  }
                    } else { ?>
                        <tr>
                            <td colspan="6" style="text-align:center">
                                <b>Nuk keni provime te mbartur per kete sezon</b>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>




        <?php } else { ?>
            <hi style="text-align: center; font-weight:bold;font-size:30">Nuk Ka Sezon Aktiv</hi>
        <?php } ?>
    </div>






















    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script type="text/javascript" src="https://lms.uet.edu.al/theme/jquery.php/core/jquery-3.2.1.min.js"></script>

    <script>
        function rezervo(lenda_ID, data_ID) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Rezervo'
            }).then((result) => {
                if (result.value) {
                    if (lenda_ID != "" && data_ID != "") {
                        $.ajax({
                            url: 'src/rezervo.php',
                            type: 'post',
                            data: {
                                lenda_ID,
                                data_ID
                            },
                            success: function(response) {
                                // return null
                                var data = JSON.parse(response)
                                console.log(data)
                                if (data.status === 1) {
                                    Swal.fire(
                                        'Rezervuar!',
                                        'Lenda u rezervua me sukses.',
                                        'success'
                                    )
                                    $('#reservationdiv-' + data.ii).html("<b>Rezervuar ne "+data.time+'</b>');
                                }
                            }
                        });
                    }






                }
            })
        }
    </script>

</body>

</html>