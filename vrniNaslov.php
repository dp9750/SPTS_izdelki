<?php

include 'modal.php';

$vsiNaslovi;
$link = open_database_connection();
$sql = "SELECT naslov FROM izdelki ORDER BY datum DESC";
$result = mysqli_query($link, $sql);
while($row = mysqli_fetch_assoc($result)) {
  $vsiNaslovi[] = $row['naslov'];
}
close_database_connection($link);

$q = $_REQUEST["q"];

$hint = "";
$naslov = "";

if ($q !== "") {
    $q = strtolower($q);
    $len=strlen($q);
    foreach($vsiNaslovi as $naslov) {
        if (stristr($q, substr($naslov, 0, $len))) {
            if ($hint === "") {
              $hint = "<i class='fas fa-plus-circle'></i> <a href='index.php?stran=vsiIzdelki&naslov=$naslov' class='dodajNaslov'>$naslov</a>";
            } else {
              $hint .= "<br><i class='fas fa-plus-circle'></i> <a href='index.php?stran=vsiIzdelki&naslov=$naslov' class='dodajNaslov'>$naslov</a>";
            }
        }
    }
}

echo $hint === "" ? "<span class='red'>Izdelek s tem naslovom ne obstaja. </span>" : $hint;

?>
