<?php
require_once("config.php");

$tables = [
  "studentsDB" => "studentet",
  "sezonDB" => ["sezonet", "sezoni_ID"],
  "studentInfoDB" => "StGrades",
  "subjectsDB" => ["lista_e_lendeve", "lenda_ID"],
  "examsDatesDB" => ["datat_e_provimeve", "lenda_ID", "sezoni_ID", "data_ID"],
  "reservationsDB" => ["rezervimet_online", "rezervim_ID", "data_ID", "student_ID", "lenda_ID", "sezoni_ID"]
];





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
      "ii" => $props["lenda_ID"]
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






  // return json_encode([
  //   "data_ID" => $props["data_ID"],
  //   "student_ID" => $props["student_ID"],
  //   "lenda_ID" => $subjectData["lenda_ID"],
  //   "sezoni_ID" => $subjectData["sezoni_ID"],
  //   "DataProvimit" => $subjectData["DataProvimit"],
  //   "DataRegjistrimitOnline" => date("m/d/y") . " " . date("h:i"),
  // ]);
}
