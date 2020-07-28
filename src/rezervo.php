<?php
require_once("functions.php");


$theStudent = getStudentDetails($config["studentData"]);


if (isset($_POST["lenda_ID"]) && $_POST["data_ID"]) {
    echo reserveExam([
        "lenda_ID" => $_POST["lenda_ID"],
        "data_ID" => $_POST["data_ID"],
        "student_ID" => $theStudent["student_ID"]
    ]);
} else {
    echo json_encode([
        "Unauthorizated"
    ]);
}
