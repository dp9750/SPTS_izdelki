<?php
include 'funkcije.php';
session_start();

if(isset($_GET['odjava'])) {
  if($_GET['odjava'] == 'true') {
    odjava();
  }
}

if(isset($_GET['stran'])) {
  if($_GET['stran'] == 'home') {
    include 'template/index.html.php';
  } else if($_GET['stran'] == 'vsiIzdelki') {
    include 'template/vsiIzdelki.html.php';
  } else if($_GET['stran'] == "prijava") {
    include 'template/prijava.html.php';
  } else if($_GET['stran'] == 'kontakt') {
    include 'template/kontakt.html.php';
  } else if($_GET['stran'] == 'nastavitve' && isset($_SESSION['upIme'])) {
    include 'template/nastavitve.html.php';
  } else if($_GET['stran'] == 'dodajIzdelek' && isset($_SESSION['upIme'])) {
    include 'template/dodajIzdelek.html.php';
  } else if($_GET['stran'] == 'uredi' && isset($_SESSION['upIme'])) {
    include 'template/preglej.html.php';
  } else if($_GET['stran'] == 'izdelek') {
    include 'template/izdelek.html.php';
  } else if($_GET['stran'] == 'edit' && isset($_SESSION['upIme']) && isset($_GET['idIzdelka'])) {
    if(preveriIDizdelka()) {
      if(preveriAdmina()) {
        include 'template/urediIzdelek.html.php';
      } else {
          include 'template/error.html.php';
      }
    } else{
      include 'template/error.html.php';
    }
  } else if($_GET['stran'] == 'delete' && isset($_SESSION['upIme'])) {
    include 'template/delete.html.php';
  } else if($_GET['stran'] == 'preglejObvestilo' && isset($_SESSION['upIme']) && isset($_GET['idObvestila'])) {
    if(preveriIDObvestila($_GET['idObvestila'])) {
      include 'template/preglejObvestilo.html.php';
    } else {
      include 'template/error.html.php';
    }
  } else if($_GET['stran'] == 'izbrisiSliko' && isset($_GET['idSlike']) && preveriIDslike()) {
    izbrisiSliko($_GET['idSlike']);
  } else {
    include 'template/error.html.php';
  }
} else {
  include 'template/index.html.php';
}
?>
