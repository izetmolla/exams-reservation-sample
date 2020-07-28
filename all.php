<?php


// ndrysho te dhenat e MYSQL
define("DB_SERVER", "localhost");
define("DB_USERNAME", "newuser");
define("DB_PASSWORD", "");
define("DB_NAME", "uetlms");




$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($conn === false){
  die("ERROR: Could not connect. " . mysqli_connect_error());
}






// Ndrysho emailin e studentit 
$studentEmail = "Studenti15@uet.edu.al";
$config = [
  "studentData" => ["Email", $studentEmail],
  //Vendos daten e mbarimit t e Sezonit per te hequr tabelen e rezervimeve  ose beje koment per ta caktivizuar
  "sezonData" => ["sezonet", "query" => "EmertimiSezonit='Sept' AND Viti='2020'", "expiredDate" => "08/31/2020"],
];




$tables = [
    "studentsDB" => "studentet",
    "sezonDB" => ["sezonet", "sezoni_ID"],
    "studentInfoDB" => "StGrades",
    "subjectsDB" => ["lista_e_lendeve", "lenda_ID"],
    "examsDatesDB" => ["datat_e_provimeve", "lenda_ID", "sezoni_ID", "data_ID"],
    "reservationsDB" => ["rezervimet_online", "rezervim_ID", "data_ID", "student_ID", "lenda_ID", "sezoni_ID"]
  ];
  


$theStudent = getStudentDetails($config["studentData"]);
if (isset($_POST["lenda_ID"]) && $_POST["data_ID"]) {
    echo reserveExam([
        "lenda_ID" => $_POST["lenda_ID"],
        "data_ID" => $_POST["data_ID"],
        "student_ID" => $theStudent["student_ID"]
    ]);
    return null;
}
  
  
  // selektojm te dhenat e Studentit
  function getStudentDetails($props)
  {
    global $conn, $tables;
    $sql = "SELECT * FROM " . $tables["studentsDB"] . " WHERE " . $props[0] . "='" . $props[1] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        return $row;
      }
    } else {
      return [];
    }
  }
  
  // selektojme sezonin aktiv
  function getActivedSeasons($props)
  {
    if (!$props) {
      return [];
    }
    global $conn, $tables;
    $sql = "SELECT * FROM " . $tables["sezonDB"][0] . " WHERE " . $props["query"];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        return $row;
      }
    } else {
      return [];
    }
  }
  
  // selektojme lendet e pakaluara 
  function getUnPassedSubjects($props)
  {
  
    global $conn, $tables;
    if (!$props[1]) {
      return [];
    }
  
    $sql = "SELECT * FROM " . $tables["studentInfoDB"] . " WHERE student_ID=" . $props[0] . " AND sezoni_ID=" . $props[1][$tables["sezonDB"][1]] . " AND Nota < 5";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $results[] = $row;
      }
    } else {
      $results = [];
    }
    return $results;
  }
  
  // selektojme lendet e pakaluara 
  function getSingleSubject($id, $rr = null)
  {
    global $conn, $tables;
    $sql = "SELECT * FROM " . $tables["subjectsDB"][0] . " WHERE " . $tables["subjectsDB"][1] . "=" . $id . "";
    // $sql = "SELECT * FROM " . $tables["subjectsDB"][0] . " WHERE " . $tables["subjectsDB"][0] . "='" . $id . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        if ($rr) {
          return $row[$rr];
        }
        return $row;
      }
    } else {
      return [];
    }
  }
  
  
  // selektojme daten lendet e pakaluara 
  function getSingleExamDate($lenda, $season)
  {
    global $conn, $tables;
    $sql = "SELECT * FROM " . $tables["examsDatesDB"][0] . " WHERE " . $tables["examsDatesDB"][1] . "=" . $lenda . " AND " . $tables["examsDatesDB"][2] . "=" . $season;
    // $sql = "SELECT * FROM " . $tables["subjectsDB"][0] . " WHERE " . $tables["subjectsDB"][0] . "='" . $id . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
  
        return $row;
      }
    } else {
      return [];
    }
  }
  
  
  
  
  
  // selektojme daten lendet e pakaluara BY ID
  function getSingleExamDatebyId($id)
  {
    global $conn, $tables;
    $sql = "SELECT * FROM " . $tables["examsDatesDB"][0] . " WHERE " . $tables["examsDatesDB"][3] . "=" . $id;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        return $row;
      }
    } else {
      return [];
    }
  }
  
  
  
  
  
  
  function checkIfreservationExist($dataId, $lendaId, $sezonId, $studentId)
  {
    global $conn, $tables;
    $sql = "SELECT * FROM rezervimet_online WHERE data_ID=" . $dataId . " AND student_ID=" . $studentId . "  AND lenda_ID=" . $lendaId . "  AND sezoni_ID=" . $sezonId . "";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        return $row;
      }
    } else {
      return [];
    }
  }
  
  
  function insertReservation($props)
  {
    global  $conn;
  
  
  
  
  
  
  
    $sql = "INSERT INTO rezervimet_online (data_ID, student_ID, lenda_ID, sezoni_ID, more_content, DataProvimit, DataRegjistrimitOnline) 
    VALUES
     (" . $props["data_ID"] . ", " . $props["student_ID"] . "," . $props["lenda_ID"] . "," . $props["sezoni_ID"] . ",null,'" . $props["DataProvimit"] . "','" . $props["DataRegjistrimitOnline"] . "')";
  
    if ($conn->query($sql) === TRUE) {
      return json_encode([
        "status" => 1,
        "message" => "Rezervimi u krye me sukses",
        "ii" => $props["lenda_ID"],
        "time"=> getTime(time())
      ]);
    } else {
      return json_encode([
        "status" => 2,
        "message" => "Something gone Wrong",
        "ii" => $props["lenda_ID"]
      ]);
    }
  }
  
  
  
  
  
  
  
  function reserveExam($props)
  {
    $subjectData = getSingleExamDatebyId($props["data_ID"]);
    $check = checkIfreservationExist($props["data_ID"], $subjectData["lenda_ID"], $subjectData["sezoni_ID"], $props["student_ID"]);
  
    if ($check) {
      return json_encode([
        "status" => 0,
        "message" => "Rezervimi egziston"
      ]);
    } else {
      return insertReservation([
        "data_ID" => $props["data_ID"],
        "student_ID" => $props["student_ID"],
        "lenda_ID" => $subjectData["lenda_ID"],
        "sezoni_ID" => $subjectData["sezoni_ID"],
        "DataProvimit" => $subjectData["DataProvimit"],
        "DataRegjistrimitOnline" => date("m/d/y") . " " . date("h:i"),
      ]);
    }
  }
  
  function getTime($time){
   return date("m-d-y h:i", $time);
  }



$theStudent = getStudentDetails($config["studentData"]);
$theActiveSeasons = getActivedSeasons(isset($config["sezonData"]) ? $config["sezonData"] : null);
$theUnPassedSubjects = getUnPassedSubjects([$theStudent["student_ID"], $theActiveSeasons]);


?>




<style>
table {
    border-collapse: collapse;
}

td,
th {
    border: 1px solid #999;
    padding: 0.5rem;
    text-align: left;
}



th {
    background-color: #009688;
    color: white;
}

table {
    max-width: 2480px;
    width: 100%;
}

table,
tr,
th,
td {
    border: 1px solid lightblue;
    font-size: 90%;
    align: left;
}

tr {
    align: "right"
}

p {
    color: black;
}

.paragraph1 {
    font-size: 12px;
}

.paragraph2 {
    font-size: 8px;
}

tr:hover {
    color: blue;
    background-color: #f5f5f5
}

tr:nth-child(even) {
    background-color: #f2f2f2
}
</style>


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
                            url: 'all.php',
                            type: 'post',
                            data: {
                                lenda_ID,
                                data_ID
                            },
                            success: function(response) {
                                console.log(response)
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