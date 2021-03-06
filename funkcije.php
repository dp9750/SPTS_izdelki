<?php
include 'modal.php';

function prijava(){
  if(isset($_POST['prijava'])) {
    $username = strip_tags(trim($_POST['uname']));
    $password = strip_tags(trim($_POST['psw']));
    if($username == "" || $password == "") {
      echo "<script>$(document).ready(function(){ $('#modalPrijava').modal(); }); </script>";
      alert("alert-danger", "Niste izpolnili vseh podatkov. ");
    } else {
      $link = open_database_connection();
      $pass = sha1($password);
      $sql = "SELECT username, password FROM admin WHERE username='$username' AND password = '$pass'";
      if(!mysqli_query($link, $sql)) {
				echo "Error description: " . mysqli_error($link);
			} else {
        $result=mysqli_query($link, $sql);
				$st_vrstic=mysqli_num_rows($result);
        if($st_vrstic == 0) {
          echo "<script>$(document).ready(function(){ $('#modalPrijava').modal(); }); </script>";
          alert("alert-danger", "Uporabnik s tem uporabniškim imenom in geslom ne obstaja. ");
        } else {
          $_SESSION['upIme'] = $username;
          $date = date('Y-m-d');
          $sql = "UPDATE admin SET lastLogin='$date' WHERE username='$username' AND password='$pass'";
          if(!mysqli_query($link, $sql)) {
    				echo "Error description: " . mysqli_error($link);
    			} else {
            echo "<script>$(document).ready(function(){ $('#modalPrijava').modal(); }); </script>";
            alert("alert-success", "Prijava uspešna. ");
            header("Refresh:1");
          }
        }
      }
      close_database_connection($link);
    }
  }
}

function odjava() {
  unset($_SESSION['upIme']);
  header("Location: index.php");
}

function alert($type, $vsebina) {
  ?><div class="alert <?php echo $type; ?>" role="alert"><?php echo $vsebina; ?></div><?php
}

// Index.html.php --------------------
function StIzdelkov() {
  $link = open_database_connection();
  $sql = "SELECT COUNT(*) AS VSI FROM izdelki";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    while($row = mysqli_fetch_assoc($result)) {
      echo $row['VSI'];
    }
  }
  close_database_connection($link);
}

function StOddelkov() {
  $link = open_database_connection();
  $sql = "SELECT DISTINCT oddelek FROM izdelki";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    echo mysqli_num_rows($result);
  }
  close_database_connection($link);
}

function StSlik() {
  $link = open_database_connection();
  $sql = "SELECT COUNT(*) AS VSI FROM izdelki";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    while($row = mysqli_fetch_assoc($result)) {
      $st = $row['VSI'];
      $sql2 = "SELECT COUNT(*) FROM slike";
      if(!mysqli_query($link, $sql2)) {
        echo "Error description: " . mysqli_error($link);
      } else {
        $result2 = mysqli_query($link, $sql2);
        echo ($st + mysqli_num_rows($result2));
      }
    }
  }
  close_database_connection($link);
}

function StAvtorjev() {
  $link = open_database_connection();
  $sql = "SELECT DISTINCT avtor FROM izdelki";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    echo mysqli_num_rows($result);
  }
  close_database_connection($link);
}

// Dodajanje izdelkov -----------------
$phpFileUploadErrors = array(
  0 => 'There is no error, the file uploaded with success',
  1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
  2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
  3 => 'The uploaded file was only partially uploaded',
  4 => 'No file was uploaded',
  6 => 'Missing a temporaly folder',
  7 => 'Failed to write file to disk',
  8 => 'A PHP extension stopped the file upload. ',
);

function dodajIzdelek() {
  if(isset($_POST['dodaj'])) {
    $avtor = strip_tags(trim($_POST['avtor']));
    $naslov = strip_tags(trim($_POST['naslov']));
    $kratkiOpis = strip_tags(trim($_POST['kratkiOpis']));
    $opis = strip_tags(trim($_POST['opis']));
    $oddelek = $_POST['oddelek'];
    $mentor = strip_tags(trim($_POST['mentor']));
    $solskoLeto = $_POST['solskoLeto'];

    $datum = date("Y-m-d");
    if($_POST['datum'] != $datum) {
      $datum = $_POST['datum'];
    }

    $file = $_FILES['naslovnaSlika'];
    $fileName = $_FILES['naslovnaSlika']['name'];
    $fileTmpName = $_FILES['naslovnaSlika']['tmp_name'];
    $fileSize = $_FILES['naslovnaSlika']['size'];
    $fileError = $_FILES['naslovnaSlika']['error'];
    $fileType = $_FILES['naslovnaSlika']['type'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');

    $success = false;
    $username = $_SESSION['upIme'];
    $idUporabnika = 1;

    $zadnjiID = 0;
    $potiSlik = array();

    if($avtor == "" || $naslov == "" || $kratkiOpis == "" ||$opis == "" || $oddelek == "izberiteoddelek" || $mentor == "" || $solskoLeto == "izberiteleto" || !isset($_FILES['naslovnaSlika'])) {
      alert("alert-danger", "Niste izpolnili vseh podatkov. ");
    } else {
      if(strlen($kratkiOpis) > 100 || strlen($avtor) > 50 || strlen($naslov) > 50 || strlen($mentor) > 50) {
        alert("alert-danger", "Predolga dolžina besedila. ");
      } else {
        if(!in_array($fileActualExt, $allowed)) {
          alert("alert-danger", "Ta vrsta datoteke ni dovoljena. ");
        } else {
          if($fileError != 0) {
            alert("alert-danger", "Pri nalganju datoteke je prišlo do težave. ");
          } else {
            if($fileSize > 1000000) {
              alert("alert-danger", "Velikost datoteke je prevelika. ");
            } else {
              $fileNameNew = uniqid('', true) . "." . $fileActualExt;
              $fileDestination = "template/images/uploads/" . $fileNameNew;
              move_uploaded_file($fileTmpName, $fileDestination);
              $link = open_database_connection();
              $tmpImeOddelka = imeOddelka($oddelek);
              $sql = "SELECT id FROM admin WHERE username='$username'";
              if(!mysqli_query($link, $sql)) {
                echo "Error description: " . mysqli_error($link);
              } else {
                $result = mysqli_query($link, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                  $idUporabnika = $row['id'];
                }
              }
              if(isset($_FILES['slike'])) {
                $file_array = reArrayFiles($_FILES['slike']);
                if(count($file_array) > 3) {
                  alert("alert-danger", "Največje dovoljeno število dodatnih slik je 3. ");
                  $success = false;
                } else {
                  for($i = 0; $i < count($file_array); $i++) {
                    if($file_array[$i]['error']) {
                      $success = false;
                      alert("alert-danger", $file_array[$i]['name'] . " - " . $phpFileUploadErrors[$file_array[$i]['error']]);
                    } else {
                      $extensions = array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG');
                      $file_ext = explode('.', $file_array[$i]['name']);
                      $file_ext = end($file_ext);
                      if(!in_array($file_ext, $extensions)) {
                        $success = false;
                        alert("alert-danger", $file_array[$i]['name'] . " - Nedovoljena vrsta datoteke. ");
                      } else {
                        $file_name_new = uniqid('', true) . "." . $file_ext;
                        $file_dest = "template/images/uploads/" . $file_name_new;
                        move_uploaded_file($file_array[$i]['tmp_name'], $file_dest);
                        array_push($potiSlik, $file_name_new);
                        $success = true;
                      }
                    }
                  }
                  if($success == true) {
                    $sql = "INSERT INTO izdelki(avtor, naslov, predstavitveniOpis, opis, oddelek, mentor, datum, slika, idUporabnika, solsko_leto) VALUES ('$avtor', '$naslov', '$kratkiOpis', '$opis', '$tmpImeOddelka', '$mentor', '$datum', '$fileNameNew', '$idUporabnika', '$solskoLeto');";
                    if(!mysqli_query($link, $sql)) {
                      echo "Error description: " . mysqli_error($link);
                      $success = false;
                    } else {
                      $sql = "SELECT MAX(id) AS zadnjiID FROM izdelki LIMIT 1";
                      if(!mysqli_query($link, $sql)) {
                        $success = false;
                        echo "Error description: " . mysqli_error($link);
                      } else {
                        $result = mysqli_query($link, $sql);
                        while($row = mysqli_fetch_assoc($result)) {
                          $zadnjiID = $row['zadnjiID'];
                          $success = true;
                        }
                      }
                    }
                  }
                  if($success == true) {
                    for ($i = 0; $i < count($potiSlik); $i++) {
                      $img = $potiSlik[$i];
                      $sql = "INSERT INTO slike (idIzdelka, potSlike) VALUES ('$zadnjiID', '$img')";
                      if(!mysqli_query($link, $sql)) {
                        $success = false;
                        echo "Error description: " . mysqli_error($link);
                      } else {
                        $success = true;
                      }
                    }
                    if($success == true) {
                      alert("alert-success", "Izdelek uspešno dodan. ");
                    } else {
                      alert("alert-danger", "Napaka pri dodajanju izdelka. ");
                    }
                  }
                }
              } else {
                $sql = "INSERT INTO izdelki(avtor, naslov, predstavitveniOpis, opis, oddelek, mentor, datum, slika, idUporabnika, solsko_leto) VALUES ('$avtor', '$naslov', '$kratkiOpis', '$opis', '$tmpImeOddelka', '$mentor', '$datum', '$fileNameNew', '$idUporabnika', '$solskoLeto');";
                if(!mysqli_query($link, $sql)) {
                  echo "Error description: " . mysqli_error($link);
                  $success = false;
                  alert("alert-danger", "Napaka pri dodajanju izdelka. ");
                } else {
                  $success = true;
                  alert("alert-success", "Izdelek uspešno dodan.");
                }
              }
              close_database_connection($link);
            }
          }
        }
      }
    }
  }
}

function pre_r($array) {
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}

function reArrayFiles($file_post) {
  $file_ary = array();
  $file_count = count($file_post['name']);
  $file_keys = array_keys($file_post);
  for($i = 0; $i < $file_count; $i++) {
    foreach ($file_keys as $key) {
      $file_ary[$i][$key] = $file_post[$key][$i];
    }
  }
  return $file_ary;
}

function StSlikIzdelka() {
  $idIzdelka = $_GET['idIzdelka'];
  $stSlik = 1;
  $link = open_database_connection();
  $sql = "SELECT potSlike FROM slike WHERE idIzdelka='$idIzdelka';";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    $stVrstic = mysqli_num_rows($result);
    $stSlik += $stVrstic;
  }
  close_database_connection($link);
  return $stSlik;
}

// Sortiranje izdelkov -------------------
function prikaziCheckboxe() {
  $link = open_database_connection();
  $sql = "SELECT DISTINCT(oddelek) FROM izdelki";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result=mysqli_query($link, $sql);
    $stVrstic = mysqli_num_rows($result);
    while($row = mysqli_fetch_assoc($result)) {
      ?>
      <label class="label-item"><?php echo $row['oddelek'] ?>
        <input type="checkbox" name="oddelki[]" value="<?php echo $row['oddelek'] ?>"<?php oznaciCeJeOznacen($row['oddelek']); ?>>
        <span class="checkmark"></span>
      </label>
      <?php
    }
  }
  oznaciOznaciVse($stVrstic);
  close_database_connection($link);
  if(!isset($_GET['oddelek'])) {
    ?>
    <script>
    for(var i = 0; i < document.getElementsByName('oddelki[]').length; i++) {
      document.getElementsByName('oddelki[]')[i].checked = true;
    }
    </script>
    <?php
  } else {
    ?>
    <script>
      $(".moznosti-vsebina:eq(0)").show();
      $(".moznosti-buttom:eq(0)").addClass("moznosti-active");
    </script>
    <?php
  }
}

function prikaziPoOddelkih() {
  if(isset($_POST['dodajFilterOddelki'])) {
    if(!empty($_POST['oddelki'])) {
      $url = "index.php?stran=vsiIzdelki";
      foreach($_POST['oddelki'] as $selected) {
        $url .= "&oddelek=" . imeOddelkaReverse($selected);
      }
      header("Location: " . $url);
    }
  }
}

function prikaziSelectOptions() {
  $link = open_database_connection();
  $sql = "SELECT DISTINCT datum FROM izdelki";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    $stVrstic = mysqli_num_rows($result);
    if($stVrstic > 0) {
      $vsiDatumi = array();
      while($row = mysqli_fetch_assoc($result)) {
        $leto = substr($row['datum'], 0, 4);
        array_push($vsiDatumi, $leto);
      }
      $unique = array_unique($vsiDatumi);
      foreach ($unique as $leto) {
        if(isset($_GET['solskoleto'])) {
          if($leto == $_GET['solskoleto']) {
            echo "<option value=" . $leto . " selected>" . $leto . "</option>";
          } else {
            echo "<option value=" . $leto . ">" . $leto . "</option>";
          }
        } else {
          echo "<option value=" . $leto . ">" . $leto . "</option>";
        }
      }
    } else {
      echo "<option value='niizdelkov'>Ni izdelkov</option>";
    }
  }
  close_database_connection($link);
}

function prikaziPoLetu() {
  if(isset($_POST['filterLeto'])) {
    $leto = $_POST['solskoleto'];
    $url = "index.php?stran=vsiIzdelki&solskoleto=$leto";
    header("Location: " . $url);
  }
}

function imeOddelka($oddelek) {
  if($oddelek == "tehnikracunalnistva") {
    return "Tehnik računalništva";
  } else if($oddelek == "elektrotehnik") {
    return "Elektrotehnik";
  } else if($oddelek == "tehnikoblikovanja") {
    return "Tehnik oblikovanja";
  } else if($oddelek == "strojnitehnik") {
    return "Strojni tehnik";
  } else if($oddelek == "avtomehanik") {
    return "Avtomehanik";
  } else if($oddelek == "mizar") {
    return "Mizar";
  }
}

function imeOddelkaReverse($oddelek) {
  if($oddelek == "Tehnik računalništva") {
    return "tehnikracunalnistva";
  } else if($oddelek == "Elektrotehnik") {
    return "elektrotehnik";
  } else if($oddelek == "Tehnik oblikovanja") {
    return "tehnikoblikovanja";
  } else if($oddelek == "Strojni tehnik") {
    return "strojnitehnik";
  } else if($oddelek == "Avtomehanik") {
    return "avtomehanik";
  } else if($oddelek == "Mizar") {
    return "mizar";
  }
}

function izbrisi() {
  if(isset($_POST['izbrisi'])) {
    $idIzdelka = strip_tags(trim($_POST['recipient-id']));
    $link = open_database_connection();
    $sql = "SELECT * FROM izdelki WHERE id='$idIzdelka'";
    if(!mysqli_query($link, $sql)) {
      izbrisiStay();
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      $slika = mysqli_fetch_assoc($result);
      $pic = $slika['slika'];

      if (!unlink("template/images/uploads/$pic")) {
        izbrisiStay();
        echo ("Error deleting $file");
      } else {
        $sql = "SELECT * FROM slike WHERE idIzdelka='$idIzdelka'";
        if(!mysqli_query($link, $sql)) {
          izbrisiStay();
          echo "Error description: " . mysqli_error($link);
        } else {
          $result = mysqli_query($link, $sql);
          $stVrstic = mysqli_num_rows($result);
          if($stVrstic > 0) {
            while($row = mysqli_fetch_assoc($result)) {
              $pic = $row['potSlike'];
              if (!unlink("template/images/uploads/$pic")) {
                izbrisiStay();
                echo ("Error deleting $file");
              } else {
                $sql = "DELETE FROM slike WHERE idIzdelka='$idIzdelka'";
                if(!mysqli_query($link, $sql)) {
                  izbrisiStay();
                  echo "Error description: " . mysqli_error($link);
                }
              }
            }
          }
          $sql = "DELETE FROM izdelki WHERE id='$idIzdelka';";
          if(!mysqli_query($link, $sql)) {
            izbrisiStay();
            echo "Error description: " . mysqli_error($link);
          } else {
            izbrisiStay();
            alert("alert-success", "Izdelek uspešno izbrisan. ");
            header("Refresh:2");
          }
        }
      }
    }
    close_database_connection($link);
  }
}

function izbrisiStay() {
  ?>
  <script>
    $(document).ready(function(){ $('#modalIzbrisiIzdelek').modal() });
  </script>
  <?php
}

function oznaciCeJeOznacen($parameter) {
  if(isset($_GET['oddelek'])) {
    $query  = explode('&', $_SERVER['QUERY_STRING']);
    $params = array();
    $text = "";
    foreach( $query as $item )
    {
      if (strpos($item, 'oddelek') !== false) {
        array_push($params, $item);
        $text .= $item;
      }
    }
    for($i = 0; $i < substr_count($text, 'oddelek'); $i++) {
      $tmp = imeOddelka(str_replace("oddelek=","",$params[$i]));
      if($tmp == $parameter) {
        echo " checked";
      }
    }
  }
}

function oznaciOznaciVse($vsi) {
  if(isset($_GET['oddelek'])) {
    $query  = explode('&', $_SERVER['QUERY_STRING']);
    $params = array();
    $text = "";
    foreach( $query as $item )
    {
      if (strpos($item, 'oddelek') !== false) {
        array_push($params, $item);
        $text .= $item;
      }
    }
    if(substr_count($text, 'oddelek') == $vsi) {
      ?>
      <script>
        document.getElementsByName('oddelek')[0].checked = true;
      </script>
      <?php
    }
  }
}

function izpisIzdekov() {
  $link = open_database_connection();
  $sql = "SELECT DISTINCT * FROM izdelki ORDER BY RAND() LIMIT 3";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    while($row = mysqli_fetch_assoc($result)) {
      ?>
      <div class="card ml-5 mr-5 shadow-lg">
        <img class="card-img-top" src="template/images/uploads/<?php echo $row['slika']; ?>" alt="<?php echo $row['naslov']; ?>">
        <div class="card-body">
          <h5 class="card-title"><?php echo $row['naslov']; ?></h5>
          <p class="card-text"><?php echo $row['predstavitveniOpis']; ?></p>
        </div>
        <div class="card-footer p-0">
          <a href="index.php?stran=izdelek&idIzdelka=<?php echo $row['id']; ?>" class="btn btn-primary w-100 rounded-0">Preberi več</a>
        </div>
      </div>
      <?php
    }
  }
  close_database_connection($link);
}

function izpisiVseIzdelke() {
  $link = open_database_connection();
  $sql = "SELECT * FROM izdelki ORDER BY datum DESC";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    while($row = mysqli_fetch_assoc($result)) {
      izpisiIzdelek($row);
    }
  }
  close_database_connection($link);
}

function filterIzdelki() {
  if(isset($_GET['oddelek'])) {
    $query  = explode('&', $_SERVER['QUERY_STRING']);
    $params = array();
    $text = "";
    foreach( $query as $item )
    {
      if (strpos($item, 'oddelek') !== false) {
        array_push($params, $item);
        $text .= $item;
      }
    }
    $sql = "SELECT * FROM izdelki WHERE ";
    for($i = 0; $i < substr_count($text, 'oddelek'); $i++) {
      if($i == (substr_count($text, 'oddelek') - 1)){
        $tmp = imeOddelka(str_replace("oddelek=","",$params[$i]));
        $sql .= "oddelek=" . "'$tmp'" . " ORDER BY datum DESC;";
      } else{
        $tmp = imeOddelka(str_replace("oddelek=","",$params[$i]));
        $sql .= "oddelek=" . "'$tmp'" . " OR ";
      }
    }
    $link = open_database_connection();
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      $stVrstic = mysqli_num_rows($result);
      if($stVrstic > 0) {
        while($row = mysqli_fetch_assoc($result)) {
          izpisiIzdelek($row);
        }
      } else {
        niIzdelkov();
      }
    }
    close_database_connection($link);
  } else if(isset($_GET['avtor'])) {
    $avtor = $_GET['avtor'];
    $link = open_database_connection();
    $sql = "SELECT * FROM izdelki WHERE avtor='$avtor'";
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      $stVrstic = mysqli_num_rows($result);
      if($stVrstic > 0) {
        while($row = mysqli_fetch_assoc($result)) {
          izpisiIzdelek($row);
        }
      } else {
        niIzdelkov();
      }
    }
    close_database_connection($link);
  } else if(isset($_GET['naslov'])) {
    $naslov = $_GET['naslov'];
    $link = open_database_connection();
    $sql = "SELECT * FROM izdelki WHERE naslov='$naslov'";
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      $stVrstic = mysqli_num_rows($result);
      if($stVrstic > 0) {
        while($row = mysqli_fetch_assoc($result)) {
          izpisiIzdelek($row);
        }
      } else {
        niIzdelkov();
      }
    }
    close_database_connection($link);
  } else if(isset($_GET['solskoleto'])) {
    $solskoleto = $_GET['solskoleto'];
    if($solskoleto == 'izberidatum') {
      izpisiVseIzdelke();
    } else {
      $link = open_database_connection();
      $sql = "SELECT * FROM izdelki WHERE datum LIKE '$solskoleto%'";
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
      } else {
        $result = mysqli_query($link, $sql);
        $stVrstic = mysqli_num_rows($result);
        if($stVrstic > 0) {
          while($row = mysqli_fetch_assoc($result)) {
            izpisiIzdelek($row);
          }
        } else {
          niIzdelkov();
        }
      }
      close_database_connection($link);
    }
  } else {
    izpisiVseIzdelke();
  }
}

function niIzdelkov() {
  ?>
  <li class="clearfix">
    <h2>Trenutno žal ni nobenih izdelkov. </h2>
  </li>
  <?php
}

function izpisiIzdelek($row) {
  ?>
  <li class="clearfix">
    <div class="left">
      <img src="template/images/uploads/<?php echo $row['slika']; ?> " alt="<?php echo $row['naslov']; ?>" class="thumb">
      <h4><?php echo $row['naslov']; ?></h4>
      <span class="meta"><?php echo $row['predstavitveniOpis']; ?></span>
    </div>

    <div class="right">
      <span class="darkview">
        <a class="btn-primary" href="index.php?stran=izdelek&idIzdelka=<?php echo $row['id']; ?>">Preberi več</a>
      </span>
    </div>
  </li>
  <?php
}

function vrniSLODatum($date) {
  if($date == 'January') {
    return 'Januar';
  } else if($date == 'February') {
    return 'Februar';
  } else if($date == 'March') {
    return 'Marec';
  } else if($date == 'April') {
    return 'April';
  } else if($date == 'May') {
    return 'Maj';
  } else if($date == 'June') {
    return 'Junij';
  } else if($date == 'July') {
    return 'Julij';
  } else if($date == 'August') {
    return 'Avgust';
  } else if($date == 'September') {
    return 'September';
  } else if($date == 'October') {
    return 'Oktober';
  } else if($date == 'November') {
    return 'November';
  } else if($date == 'December') {
    return 'December';
  }
}

function solskoLeto($datum) {
  $mesec = date('m', strtotime($datum));
  $leto = date('Y', strtotime($datum));
  if($mesec < 7) {
    echo "<p class='card-text'><b>Šolsko leto:</b> " . ($leto - 1) . "/" . $leto . "</p>";
  } else if($mesec > 8) {
    echo "<p class='card-text'><b>Šolsko leto:</b> " . $leto . "/" . ($leto + 1) . "</p>";
  } else {
    echo "<p class='card-text'>Objavljeno izven šolskega časa. </p>";
  }
}

// Sprememba gesla ------------------
function spremembaGesla() {
  if(isset($_POST['spremeni'])) {
    $trenutnoGeslo = strip_tags(trim($_POST['staroGeslo']));
    $novoGeslo = strip_tags(trim($_POST['novoGeslo']));
    $novoGesloDrugic = strip_tags(trim($_POST['novoGesloDrugic']));

    if($trenutnoGeslo == "" || $novoGeslo == "" || $novoGesloDrugic == "") {
      alert("alert-danger", "Niste izpolnili vseh podatkov. ");
    } else {
      if($novoGeslo != $novoGesloDrugic) {
        alert("alert-danger", "Gesli nista enaki. ");
      } else {
        if(strlen($novoGeslo) < 8) {
          alert("alert-danger", "Novo geslo je prekratko. Geslo mora biti dolžine vsaj 8 znakov. ");
        } else {
          if(!preg_match("/[A-Z]/", $novoGeslo) || !preg_match("/[a-z]/", $novoGeslo)){
              alert("alert-danger", "Geslo mora vključevati velike in male črke. ");
          } else {
            if(1 != preg_match('~[0-9]~', $novoGeslo)){
                alert("alert-danger", "Geslo mora vključevati številke. ");
            } else {
              $link = open_database_connection();
              $username = $_SESSION['upIme'];
              $shaPass = sha1($novoGeslo);
              $shaTrenutno = sha1($trenutnoGeslo);
              $sql = "SELECT password FROM admin WHERE username='$username' AND password='$shaTrenutno'";
              if(!mysqli_query($link, $sql)) {
                echo "Error description: " . mysqli_error($link);
            } else {
                $result = mysqli_query($link, $sql);
                $stVrstic = mysqli_num_rows($result);
                if($stVrstic == 0) {
                  alert("alert-danger", "Uporabnik s tem uporabniškim imenom in geslom ne obstaja. ");
                } else {
                  $sql = "UPDATE admin SET password='$shaPass' WHERE username='$username' AND password='$shaTrenutno'";
                  if(!mysqli_query($link, $sql)) {
                    echo "Error description: " . mysqli_error($link);
                    } else {
                    alert("alert-success", "Geslo je bilo spremenjeno. ");
                  }
                }
              }
              close_database_connection($link);
            }
          }
        }
      }
    }
  }
}

// Pozabljeno geslo ---------------
function pozabljenoGeslo() {
  if(isset($_POST['pozabljeno'])) {
    $username = strip_tags(trim($_POST['username']));
    $email = strip_tags(trim($_POST['email']));
    if($username==""||$email=="") {
      echo "<script>$(document).ready(function(){ $('#modalPozabljenoGeslo').modal(); }); </script>";
      alert("alert-danger", "Niste izpolnili vseh podatkov. ");
    } else {
      $link = open_database_connection();
      $sql = "SELECT id FROM admin WHERE username='$username' AND email='$email';";
      if(!mysqli_query($link, $sql)) {
        echo "<script>$(document).ready(function(){ $('#modalPozabljenoGeslo').modal(); }); </script>";
				echo "Error description: " . mysqli_error($link);
			} else {
        $result = mysqli_query($link, $sql);
        $stVrstic = mysqli_num_rows($result);
        if($stVrstic == 0) {
          echo "<script>$(document).ready(function(){ $('#modalPozabljenoGeslo').modal(); }); </script>";
          alert("alert-danger", "Uporabnik s tem uporabniškim imenom in emailom ne obstaja. ");
        } else {
          $geslo = uniqid();
          $kodiranoGeslo = sha1($geslo);
          $zadeva = "Pozabljeno geslo";
          $sporocilo = "Zahtevali ste novo geslo za stran SPTŠ | Izdelki. \n \n Vaše novo geslo je " . $geslo;
          mail($email, $zadeva, $sporocilo);
          $sql = "UPDATE admin SET password='$kodiranoGeslo' WHERE username='$username' AND email='$email';";
          if(!mysqli_query($link, $sql)) {
            echo "<script>$(document).ready(function(){ $('#modalPozabljenoGeslo').modal(); }); </script>";
    				echo "Error description: " . mysqli_error($link);
    			} else {
            echo "<script>$(document).ready(function(){ $('#modalPozabljenoGeslo').modal(); }); </script>";
            alert("alert-success", "Na email " . $email . " smo vam poslali vaše novo geslo. ");
          }
        }
      }
      close_database_connection($link);
    }
  }
}

// Kontakt -------------------------
function kontaktiraj() {
  if(isset($_POST['sporoci'])) {
    $ime = strip_tags(trim($_POST['name']));
    $email = strip_tags(trim($_POST['kontaktEmail']));
    $oseba = strip_tags(trim($_POST['oseba']));
    $zadeva = strip_tags(trim($_POST['zadeva']));
    $sporocilo = strip_tags(trim($_POST['sporocilo']));
    if($ime == "" || $email == "" || $zadeva == "" || $sporocilo == "") {
      alert("alert-danger", "Niste izpolnili vseh podatkov. ");
      if($ime == "") {
        ?>
        <style>#name { border: 2px solid red; }</style>
        <?php
      }
      if($email == "") {
        ?>
        <style>#kontaktEmail { border: 2px solid red; }</style>
        <?php
      }
      if($zadeva == "") {
        ?>
        <style>#zadeva { border: 2px solid red; }</style>
        <?php
      }
      if($sporocilo == "") {
        ?>
        <style>#sporocilo { border: 2px solid red; }</style>
        <?php
      }
    } else {
      $datum = date("Y.m.d H:i:s");
      $link = open_database_connection();
      $sql = "INSERT INTO sporocila (ime, email, oseba, zadeva, sporocilo, datum) VALUES ('$ime', '$email', '$oseba', '$zadeva', '$sporocilo', '$datum')";
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
      } else {
          alert("alert-success", "Sporočilo uspešno poslano. ");
      }
      close_database_connection($link);
    }
  }
}

/* -------------------- OBVESTILA ------------ */
function badgeObestila() {
  $link = open_database_connection();
  $sql = "SELECT * FROM sporocila";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    $stObvestil = mysqli_num_rows($result);
    if($stObvestil > 0) {
      ?>
      <span class="badge badge-pill badge-primary"><?php echo $stObvestil; ?></span>
      <?php
    }
  }
  close_database_connection($link);
}

//če obstaja sporočilo z ID-jem..
function preveriIDObvestila($id) {
  $link = open_database_connection();
  $sql = "SELECT id FROM sporocila WHERE id = '$id'";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    $stVrstic = mysqli_num_rows($result);
    if($stVrstic == 0) {
      return false;
    } else {
      return true;
    }
  }
  close_database_connection($link);
}

//prikaz
function prikaziVsaObvestila() {
  $link = open_database_connection();
  $sql = "SELECT COUNT(*) FROM sporocila";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    $stObvestil = mysqli_num_rows($result);
    if($stObvestil > 0) {
      $sql = "SELECT * FROM sporocila ORDER BY datum DESC";
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
      } else {
        $result = mysqli_query($link, $sql);
        $stVrstic = mysqli_num_rows($result);
        if($stVrstic == 0) {
          ?>
            <hr>
            <br>
            <h3>Trenutno nimate obvestil!</h3>
            <br>
            <hr>
          <?php
        } else {
          while($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="card border-secondary shadow">
              <h5 class="card-header">Zadeva: <?php echo $row['zadeva']; ?></h5>
              <div class="card-body">
                <h5 class="card-title">Pošiljatelj: <?php echo $row['ime']; ?></h5>
                <small class="form-text text-muted"><?php echo $row['datum']; ?></small>
                <hr>
                <div class="btn-group float-right" role="group" aria-label="Card moznosti">
                  <a href="index.php?stran=preglejObvestilo&idObvestila=<?php echo $row['id']; ?>" class="btn btn-primary default-size font-16"><i class="fas fa-plus"></i></a>
                  <button type="button" class="btn btn-danger default-size" data-toggle="modal" data-target="#modalIzbrisiObvestilo" data-whatever="<?php echo $row['id']; ?>"><i class="fas fa-trash-alt"></i></button>
                </div>
              </div>
            </div>
            <br>
            <?php
          }
        }
      }
    } else {
      echo "Nimate obvestil!";
    }
  }
  close_database_connection($link);
}

function prikaziObvestilaDijakov() {
  $link = open_database_connection();
  $sql = "SELECT COUNT(*) FROM sporocila";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    $stObvestil = mysqli_num_rows($result);
    if($stObvestil > 0) {
      $sql = "SELECT * FROM sporocila WHERE oseba='dijak' ORDER BY datum DESC";
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
      } else {
        $result = mysqli_query($link, $sql);
        $stVrstic = mysqli_num_rows($result);
        if($stVrstic == 0) {
          ?>
            <hr>
            <br>
            <h3>Trenutno nimate obvestil!</h3>
            <br>
            <hr>
          <?php
        } else {
          while($row = mysqli_fetch_assoc($result)) {
            ?><div class="card border-secondary shadow">
              <h5 class="card-header">Zadeva: <?php echo $row['zadeva']; ?></h5>
              <div class="card-body">
                <h5 class="card-title">Pošiljatelj: <?php echo $row['ime']; ?></h5>
                <small class="form-text text-muted"><?php echo $row['datum']; ?></small>
                <hr>
                <div class="btn-group float-right" role="group" aria-label="Card moznosti">
                  <a href="index.php?stran=preglejObvestilo&idObvestila=<?php echo $row['id']; ?>" class="btn btn-primary default-size font-16"><i class="fas fa-plus"></i></a>
                  <button type="button" class="btn btn-danger default-size" data-toggle="modal" data-target="#modalIzbrisiObvestilo" data-whatever="<?php echo $row['id']; ?>"><i class="fas fa-trash-alt"></i></button>
                </div>
              </div>
            </div>
            <br>
            <?php
          }
        }
      }
    } else {
      echo "Nimate obvestil!";
    }
  }
  close_database_connection($link);
}

function prikaziObvestilaOstalih() {
  $link = open_database_connection();
  $sql = "SELECT COUNT(*) FROM sporocila";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    $stObvestil = mysqli_num_rows($result);
    if($stObvestil > 0) {
      $sql = "SELECT * FROM sporocila WHERE oseba='ostali' ORDER BY datum DESC";
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
      } else {
        $result = mysqli_query($link, $sql);
        $stVrstic = mysqli_num_rows($result);
        if($stVrstic == 0) {
          ?>
            <hr>
            <br>
            <h3>Trenutno nimate obvestil!</h3>
            <br>
            <hr>
          <?php
        } else {
          while($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="card border-secondary shadow">
              <h5 class="card-header">Zadeva: <?php echo $row['zadeva']; ?></h5>
              <div class="card-body">
                <h5 class="card-title">Pošiljatelj: <?php echo $row['ime']; ?></h5>
                <small class="form-text text-muted"><?php echo $row['datum']; ?></small>
                <hr>
                <div class="btn-group float-right" role="group" aria-label="Card moznosti">
                  <a href="index.php?stran=preglejObvestilo&idObvestila=<?php echo $row['id']; ?>" class="btn btn-primary default-size font-16"><i class="fas fa-plus"></i></a>
                  <button type="button" class="btn btn-danger default-size" data-toggle="modal" data-target="#modalIzbrisiObvestilo" data-whatever="<?php echo $row['id']; ?>"><i class="fas fa-trash-alt"></i></button>
                </div>
              </div>
            </div>
            <br>
            <?php
          }
        }
      }
    } else {
      echo "Nimate obvestil!";
    }
  }
  close_database_connection($link);
}

//če je obvestil več kot 0 izpiši gumbe za izbris, sortiranje..
function preveriObvestila() {
  $link = open_database_connection();
  $sql = "SELECT * FROM sporocila";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    $stVrstic = mysqli_num_rows($result);
    if($stVrstic > 0) {
      ?>
      <form method="post">
        <div class="form-row">
          <div class="form-group col-md-4">
            <div class="input-group mb-1">
              <div class="input-group-prepend">
                <button class="btn btn-secondary" type="submit" name="filtriraj">Filtriraj</button>
              </div>
              <select class="custom-select" id="filterObvestila" name="filterObvestila">
                <option value="vsi" <?php if(!isset($_POST['filtriraj']) || $_POST['filterObvestila'] == 'vsi' ){ echo "selected"; } ?>>Prikaži vsa</option>
                <option value="dijaki" <?php if(isset($_POST['filtriraj'])) { if($_POST['filterObvestila'] == 'dijaki') { echo "selected"; } } ?>>Dijaki</option>
                <option value="ostali" <?php if(isset($_POST['filtriraj'])) { if($_POST['filterObvestila'] == 'ostali') { echo "selected"; } } ?>>Ostali</option>
              </select>
            </div>
          </div>
        </div>
      </form>
      <button type="button" class="btn btn-outline-danger mb-3" data-toggle="modal" data-target="#modalIzbrisiVse">Izbriši vse</button>
      <hr>
      <?php
    }
  }
  close_database_connection($link);
}

function preveriIzpisObvestil() {
  if(isset($_POST['filtriraj'])) {
    ?><script>$(document).ready(function(){$('#nav-tab a[href="#nav-obvestila"]').tab('show')});</script><?php
    $izbira = $_POST['filterObvestila'];
    if($izbira == 'dijaki') {
      prikaziObvestilaDijakov();
    } else if($izbira == 'ostali') {
      prikaziObvestilaOstalih();
    } else {
      prikaziVsaObvestila();
    }
  } else {
    prikaziVsaObvestila();
  }
}

function izbrisiObvestilo() {
  if(isset($_POST['izbrisiObvestilo'])) {
    $idObvestila = $_POST['ObvestiloID'];
    if($idObvestila == "") {
      alert("alert-danger", "Napaka pri brisanju obvestila. ");
    } else {
      $link = open_database_connection();
      $sql = "DELETE FROM sporocila WHERE id='$idObvestila'";
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
      } else {
        header("Location: index.php?stran=nastavitve");
      }
      close_database_connection($link);
    }
  }
}

function izbrisiVsaObvestila() {
  if(isset($_POST['izbrisiVse'])) {
    $link = open_database_connection();
    $sql = "DELETE FROM sporocila";
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      header("Location: index.php?stran=nastavitve");
    }
    close_database_connection($link);
  }
}

function odgovori($email) {
  if(isset($_POST['poslji'])) {
    ?>
    <style>
      #odgovori {display: none;}
      .form-odgovori {display: block;}
    </style>
    <?php
    $odgovor = strip_tags(trim($_POST['odgovor']));
    if($odgovor == "") {
      alert("alert-danger", "Sporočilo je prazno. ");
    } else {
			mail($email, "Odgovor na strani SPTŠ | Izdelki s poklicne mature", $odgovor);
      alert("alert-success", "Sporočilo uspešno poslano. ");
    }
  }
}

/* ---------------UREDI IZDELEK ----------------*/
function preveriIDizdelka() {
  $id = $_GET['idIzdelka'];
  $link = open_database_connection();
  $sql = "SELECT * FROM izdelki WHERE id='$id'";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $stVrstic = mysqli_num_rows(mysqli_query($link, $sql));
    if($stVrstic > 0) {
      return true;
    } else {
      return false;
    }
  }
  close_database_connection($link);
}

function preveriIDslike() {
  $id = $_GET['idSlike'];
  $link = open_database_connection();
  $sql = "SELECT * FROM slike WHERE id='$id'";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $stVrstic = mysqli_num_rows(mysqli_query($link, $sql));
    if($stVrstic > 0) {
      return true;
    } else {
      return false;
    }
  }
  close_database_connection($link);
}

function preveriAdmina() {
  $link = open_database_connection();
  $idIzdelka = $_GET['idIzdelka'];
  $username = $_SESSION["upIme"];
  $userID;
  if($username == "admin")
    return true;
  else {
    $sql = "SELECT id FROM admin WHERE username='$username'";
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      while($row = mysqli_fetch_assoc($result)) {
        $userID = $row['id'];
      }
    }
    $sql = "SELECT idUporabnika FROM izdelki WHERE id='$idIzdelka'";
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      while($row = mysqli_fetch_assoc($result)) {
        if($userID == $row['idUporabnika']) {
          return true;
        } else {
          return false;
        }
      }
    }
  }
  close_database_connection($link);
}

function urediIzdelek() {
  if(isset($_POST['preuredi'])) {
    $id=$_GET['idIzdelka'];
    $avtor = strip_tags(trim($_POST['avtor']));
    $naslov = strip_tags(trim($_POST['naslov']));
    $kratkiOpis = strip_tags(trim($_POST['kratkiOpis']));
    $opis = strip_tags(trim($_POST['opis']));
    $oddelek = $_POST['oddelek'];
    $mentor = strip_tags(trim($_POST['mentor']));
    $solskoleto = $_POST['solskoLeto'];

    $file = $_FILES['naslovnaSlika'];
    $fileName = $_FILES['naslovnaSlika']['name'];
    $fileTmpName = $_FILES['naslovnaSlika']['tmp_name'];
    $fileSize = $_FILES['naslovnaSlika']['size'];
    $fileError = $_FILES['naslovnaSlika']['error'];
    $fileType = $_FILES['naslovnaSlika']['type'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('jpg', 'jpeg', 'png');

    $tmpImeOddelka;

    if($avtor == ""||$naslov==""||$kratkiOpis==""||$opis==""||$oddelek=="izberiteoddelek"||$mentor==""||$solskoleto=="izberiteleto") {
      alert("alert-danger", "Niste izpolnili vseh podatkov. ");
      if($avtor == "") {
        ?>
        <style>#avtor { border: 2px solid red; }</style>
        <?php
      }
      if($naslov == "") {
        ?>
        <style>#naslov { border: 2px solid red; }</style>
        <?php
      }
      if($opis == "") {
        ?>
        <style>#opis { border: 2px solid red; }</style>
        <?php
      }
      if($oddelek == "izberiteoddelek") {
        ?>
        <style>select{ border: 2px solid red; }</style>
        <?php
      }
      if($mentor == "") {
        ?>
        <style>#mentor { border: 2px solid red; }</style>
        <?php
      }
      if($kratkiOpis=="") {
        ?>
        <style>#kratkiOpis { border: 2px solid red; }</style>
        <?php
      }
      if($solskoleto=="izberiteleto") {
        ?>
        <style>#solskoLeto { border: 2px solid red; }</style>
        <?php
      }
    } else {
      if(strlen($kratkiOpis) > 100 || strlen($avtor) > 50 || strlen($naslov) > 50 || strlen($mentor) > 50) {
        alert("alert-danger", "Besedilo je predolgo. ");
      } else {
        $link = open_database_connection();
        if($_FILES['naslovnaSlika']['name'] != "") {
          if(!in_array($fileActualExt, $allowed)) {
            print_r($_FILES['naslovnaSlika']);
            alert("alert-danger", "Ta vrsta datoteke ni dovoljena. ");
          } else {
            if($fileError != 0) {
              alert("alert-danger", "Pri nalganju datoteke je prišlo do težave. ");
            } else {
              if($fileSize > 1000000) {
                alert("alert-danger", "Velikost datoteke je prevelika. ");
              } else {
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                $fileDestination = "template/images/uploads/" . $fileNameNew;
                move_uploaded_file($fileTmpName, $fileDestination);
                $sql = "SELECT slika FROM izdelki WHERE id='$id'";
                if(!mysqli_query($link, $sql)) {
                  echo "Error description: " . mysqli_error($link);
                } else {
                  $result = mysqli_query($link, $sql);
                  while($row = mysqli_fetch_assoc($result)) {
                    $pic = $row['slika'];
                    if (!unlink("template/images/uploads/$pic")) {
                      echo ("Error deleting file: $fileName");
                    }
                  }
                }
                $tmpImeOddelka = imeOddelka($oddelek);
                $sql = "UPDATE izdelki SET avtor='$avtor', naslov='$naslov', predstavitveniOpis='$kratkiOpis', opis='$opis', mentor='$mentor', oddelek='$tmpImeOddelka', solsko_leto='$solskoleto', slika='$fileNameNew' WHERE id='$id'";
                if(!mysqli_query($link, $sql)) {
                  echo "Error description: " . mysqli_error($link);
                } else {
                  alert("alert-success", "Izdelek uspešno posodobljen. ");
                }
              }
            }
          }
        } else {
          $tmpImeOddelka = imeOddelka($oddelek);
          $sql = "UPDATE izdelki SET avtor='$avtor', naslov='$naslov', predstavitveniOpis='$kratkiOpis', opis='$opis', mentor='$mentor', oddelek='$tmpImeOddelka', solsko_leto='$solskoleto' WHERE id='$id'";
          if(!mysqli_query($link, $sql)) {
            echo "Error description: " . mysqli_error($link);
          } else {
            alert("alert-success", "Izdelek uspešno posodobljen. ");
          }
        }
        close_database_connection($link);
      }
    }
  }
}

function izbrisiSliko($idSlike) {
  $id = $_GET['idIzdelka'];
  $link = open_database_connection();
  $sql = "SELECT * FROM slike WHERE id='$idSlike'";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    while($row = mysqli_fetch_assoc($result)) {
      $pic = $row['potSlike'];
      if (!unlink("template/images/uploads/$pic")) {
        echo ("Error deleting $file");
      }
    }
  }
  $sql = "DELETE FROM slike WHERE id='$idSlike'";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  }
  header("Location: index.php?stran=edit&idIzdelka=$id");
  alert("alert-success","Slika je bila uspešno izbrisana. ");
  close_database_connection($link);
}


// Ali ima izdelek več slik kot samo naslovno sliko
function imaVecSlik() {
  $id = $_GET['idIzdelka'];
  $link = open_database_connection();
  $sql = "SELECT id FROM slike WHERE idIzdelka='$id'";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    if(mysqli_num_rows($result) > 0)
      return true;
    return false;
  }
  close_database_connection($link);
}

function prikaziVecSlik() {
  if(imaVecSlik()) {
    $id = $_GET['idIzdelka'];
    $link = open_database_connection();
    $sql = "SELECT * FROM slike WHERE idIzdelka='$id'";
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      while($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="card shadow">
          <img class="card-img-top pointer" src="template/images/uploads/<?php echo $row['potSlike']; ?>" alt="<?php echo $row['potSlike']; ?>" data-toggle="modal" data-target="#modalGalerija">
        </div>
        <?php
      }
    }
    close_database_connection($link);
  }
}

// DODAJ UPORABNIKA
function dodajUporabnika() {
  if(isset($_POST['ustvari'])) {
    $username = strip_tags(trim($_POST['DodajUsername']));
    $geslo = strip_tags(trim($_POST['DodajPassword']));
    $gesloPonovi = strip_tags(trim($_POST['DodajPasswordPonovi']));
    $email = strip_tags(trim($_POST['DodajEmail']));
    UserAddStay();
    if($username == "" || $geslo == "" || $gesloPonovi == "" || $email == "") {
      alert("alert-danger","Nisi izpolnil vseh podatkov. ");
      dodajUporabnikaStay();
      if($username == "") {
        ?><style>#DodajUsername { border: 2px solid red; }</style><?php
        dodajUporabnikaStay();
      }
      if($geslo == "") {
        ?><style>#DodajPassword { border: 2px solid red; }</style><?php
        dodajUporabnikaStay();
      }
      if($gesloPonovi == "") {
        ?><style>#DodajPasswordPonovi { border: 2px solid red; }</style><?php
        dodajUporabnikaStay();
      }
      if($email == "") {
        ?><style>#DodajEmail { border: 2px solid red; }</style><?php
        dodajUporabnikaStay();
      }
    } else {
      if($geslo != $gesloPonovi) {
        alert("alert-danger", "Gesli nista enaki. ");
        dodajUporabnikaStay();
      } else {
        if(strlen($geslo) < 8) {
          alert("alert-danger", "Geslo je prekratko. Geslo mora biti dolžine vsaj 8 znakov. ");
          dodajUporabnikaStay();
        } else {
          if(!preg_match("/[A-Z]/", $geslo) || !preg_match("/[a-z]/", $geslo)) {
            alert("alert-danger", "Geslo mora vključevati velike in male črke. ");
            dodajUporabnikaStay();
          } else {
            if(!preg_match('~[0-9]~', $geslo)) {
              alert("alert-danger", "Geslo mora vključevati številke. ");
              dodajUporabnikaStay();
            } else {
              if($username == "admin") {
                alert("alert-danger", "Nedovoljeno uporabniško ime. ");
                dodajUporabnikaStay();
              } else {
                $link = open_database_connection();
                $sql = "SELECT username FROM admin WHERE username='$username'";
                if(!mysqli_query($link, $sql)) {
                  echo "Error description: " . mysqli_error($link);
                } else {
                  $result = mysqli_query($link, $sql);
                  if(mysqli_num_rows($result) > 0) {
                    alert("alert-danger", "Uporabnik s tem uporabniškim imenom že obstaja. ");
                    dodajUporabnikaStay();
                  } else {
                    $shaGeslo = sha1($geslo);
                    $sql = "INSERT INTO admin(username, password, email) VALUES ('$username', '$shaGeslo', '$email')";
                    if(!mysqli_query($link, $sql)) {
                      echo "Error description: " . mysqli_error($link);
                    } else {
                      alert("alert-success", "Uporabnik uspešno dodan. ");
                      dodajUporabnikaStay();
                    }
                  }
                }
                close_database_connection($link);
              }
            }
          }
        }
      }
    }
  }
}

function dodajUporabnikaStay() {
  ?>
  <script>
    $(document).ready(function(){
      $('#nav-tab a[href="#nav-uporabniki"]').tab('show');
      $('#modalDodajUporabnika').modal();
    });
  </script>
  <?php
}

function UserAddStay() {
  ?>
  <script>
    $( ".tablinks:eq(0)" ).removeAttr( "id" );
    $( ".tablinks:eq(1)" ).attr( "id", "defaultOpen" );
    document.getElementById("defaultOpen").click();
  </script>
  <?php
}

function prikaziUporabnike() {
  $link = open_database_connection();
  $sql = "SELECT * FROM admin WHERE id!=1";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    $stVrstic = mysqli_num_rows($result);
    if($stVrstic == 0) {
      echo "<h3>Ni drugih uporabnikov. </h3>";
    } else {
      $i = 1;
      echo "<ul class='list-group'>";
      while($row = mysqli_fetch_assoc($result)) {
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <a href="javascript:void(0)" class="btn-btn-link" data-toggle="modal" data-target="#modalSpremmebaGesla" data-username="<?php echo $row['username']; ?>"><?php echo $row['username']; ?></a>
          <span class="badge badge-primary badge-pill"><?php echo $row['id']; ?></span>
        </li>
        <?php
      }
      echo "</ul>";
    }
  }
  close_database_connection($link);
}

function spremembaGeslaSubUser() {
  if(isset($_POST['spremeniGeslo'])) {

    $username = strip_tags(trim($_POST['UserName']));
    $novoGeslo = strip_tags(trim($_POST['novoGeslo']));
    $novoGesloDrugic = strip_tags(trim($_POST['novoGesloDrugic']));

    if($username == "" || $novoGeslo == "" || $novoGesloDrugic == "") {
      alert("alert-danger", "Niste izpolnili vseh podatkov. ");
      userChangeStay($username);
    } else {
      if($novoGeslo != $novoGesloDrugic) {
        alert("alert-danger", "Gesli nista enaki. ");
        userChangeStay($username);
      } else {
        if(strlen($novoGeslo) < 8) {
          alert("alert-danger", "Novo geslo je prekratko. Geslo mora biti dolžine vsaj 8 znakov. ");
          userChangeStay($username);
        } else {
          if(!preg_match("/[A-Z]/", $novoGeslo) || !preg_match("/[a-z]/", $novoGeslo)){
              alert("alert-danger", "Geslo mora vključevati velike in male črke. ");
              userChangeStay($username);
          } else {
            if(1 != preg_match('~[0-9]~', $novoGeslo)){
                alert("alert-danger", "Geslo mora vključevati številke. ");
                userChangeStay($username);
            } else {
              $link = open_database_connection();
              $shaPass = sha1($novoGeslo);
              $sql = "SELECT username FROM admin WHERE username='$username'";
              if(!mysqli_query($link, $sql)) {
                echo "Error description: " . mysqli_error($link);
                userChangeStay($username);
              } else {
                $result = mysqli_query($link, $sql);
                $stVrstic = mysqli_num_rows($result);
                if($stVrstic == 0) {
                  alert("alert-danger", "Uporabnik s tem uporabniškim imenom ne obstaja. ");
                  userChangeStay($username);
                } else {
                  $sql = "UPDATE admin SET password='$shaPass' WHERE username='$username'";
                  if(!mysqli_query($link, $sql)) {
                    echo "Error description: " . mysqli_error($link);
                    userChangeStay($username);
                    } else {
                    alert("alert-success", "Geslo je bilo spremenjeno. ");
                    userChangeStay($username);
                  }
                }
              }
              close_database_connection($link);
            }
          }
        }
      }
    }
  }
}

function userChangeStay($username) {
  ?>
  <script>
    $(document).ready(function(){
      $('#nav-tab a[href="#nav-uporabniki"]').tab('show');
      $('#modalSpremmebaGesla').modal();
      $('#modalSpremmebaGesla .modal-title').text('New message to ' + '<?php echo $username; ?>')
      $('#recipient-ime').val('<?php echo $username; ?>')
    });
  </script>
  <?php
}

function prikaziNapis() {
  $link = open_database_connection();
  $sql = "SELECT naslov FROM predstavitven_naslov ORDER BY id DESC LIMIT 1";
  if(!mysqli_query($link, $sql)) {
    echo "Error description: " . mysqli_error($link);
  } else {
    $result = mysqli_query($link, $sql);
    if(mysqli_num_rows($result) > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        echo $row['naslov'];
      }
    }
  }
  close_database_connection($link);
}

function zamenjajNapis() {
  if(isset($_POST['ustvariNapis'])) {
    $napis = strip_tags(trim($_POST['napis']));
    $link = open_database_connection();
     $datum = date('Y-m-d');
      $sql = "UPDATE predstavitven_naslov SET naslov='$napis', datum='$datum'";
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
        ?>
        <script>
          $(document).ready(function(){
            $('#nav-tab a[href="#nav-napis"]').tab('show');
          });
        </script>
        <?php
      } else {
        ?>
        <script>
          $(document).ready(function(){
            $('#nav-tab a[href="#nav-napis"]').tab('show');
          });
        </script>
        <?php
        alert("alert-success", "Zamenjali ste napis. ");
      }
    close_database_connection($link);
  }
}

?>
