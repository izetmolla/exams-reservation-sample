<?php

// Ndrysho emailin e studentit 
$studentEmail = "Studenti15@uet.edu.al";
$config = [
  "studentData" => ["Email", $studentEmail],
  //Vendos daten e mbarimit t e Sezonit per te hequr tabelen e rezervimeve  ose beje koment per ta caktivizuar
  // "sezonData" => ["sezonet", "query" => "EmertimiSezonit='Sept' AND Viti='2020'", "expiredDate" => "08/31/2020"],
];








// ndrysho te dhenat e MYSQL
$servername = "localhost";
$username = "newuser";
$password = "milani10";
$dbname = "uetlms";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}



