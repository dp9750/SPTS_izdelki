<?php

include 'modal.php';

$vsiAvtorji;
$link = open_database_connection();
$sql = "SELECT DISTINCT avtor FROM izdelki";
$result = mysqli_query($link, $sql);
while($row = mysqli_fetch_assoc($result)) {
  $vsiAvtorji[] = $row['avtor'];
}
close_database_connection($link);

$q = $_REQUEST["q"];

$hint = "";
$ime = "";

if ($q !== "") {
    $q = strtolower($q);
    $len=strlen($q);
    foreach($vsiAvtorji as $ime) {
        if (stristr($q, substr($ime, 0, $len))) {
            if ($hint === "") {
              $hint = "<i class='fas fa-plus-circle'></i> <a href='index.php?stran=vsiIzdelki&avtor=$ime' class='dodajAvtorja'>$ime</a>";
            } else {
              $hint .= "<br><i class='fas fa-plus-circle'></i> <a href='index.php?stran=vsiIzdelki&avtor=$ime' class='dodajAvtorja'>$ime</a>";
            }
        }
    }
}

echo $hint === "" ? "<span class='red'>Avtor ne obstaja. </span>" : $hint;

?>
