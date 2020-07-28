<?php

// Ndrysho emailin e studentit 
$studentEmail = "Studenti15@uet.edu.al";
$config = [
  "studentData" => ["Email", $studentEmail],
  //Vendos daten e mbarimit t e Sezonit per te hequr tabelen e rezervimeve  ose beje koment per ta caktivizuar
  "sezonData" => ["sezonet", "query" => "EmertimiSezonit='Sept' AND Viti='2020'", "expiredDate" => "08/31/2020"],
];








// ndrysho te dhenat e MYSQL
define("DB_SERVER", "localhost");
define("DB_USERNAME", "newuser");
define("DB_PASSWORD", "milani10");
define("DB_NAME", "uetlms");

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($conn === false){
  die("ERROR: Could not connect. " . mysqli_connect_error());
}



