<?php ob_start(); ?>

  <script>document.title = 'SPTŠ | Izbris izdelka';</script>
  <link rel="stylesheet" href="template/css/delete.css">

  <div class="content-60">
    <?php
    $idIzdelka = $_GET['idIzdelka'];
    $link = open_database_connection();
    $sql = "SELECT * FROM izdelki WHERE id='$idIzdelka';";
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      $stVrstic = mysqli_num_rows($result);
      if($stVrstic > 0) {
        while($row = mysqli_fetch_assoc($result)) {
    ?>
    <h1>Izbris izdelka</h1>
    <p>Ali ste prepričani da želite izbrisati ta izdelek? Izdelek in vsi njegovi podatki bodo izbrisani iz baze. Postopek je neobnovljiv. </p>
    <hr>
    <div class="slika-left">
      <img src="template/images/uploads/<?php echo $row['slika']; ?>" alt="<?php echo $row['naslov']; ?>">
    </div>
    <div class="info-right">
      <h3><?php echo $row['naslov']; ?><span><?php echo $row['datum']; ?></span></h3>
      <hr>
      <p><b>Avtor: </b><?php echo $row['avtor']; ?></p>
      <p><b>Oddelek: </b><?php echo $row['oddelek']; ?></p>
      <p><b>Mentor: </b><?php echo $row['mentor']; ?></p>
      <p><b>Opis: </b><?php echo $row['opis'] ?></p>
    </div>
    <div class="info-buttons">
      <button type="button" class="btn-primary btn-secondary" onclick="window.history.back();">Prekliči</button>
      <a href="index.php?stran=delete&idIzdelka=<?php echo $row['id']; ?>&izbrisi=true" class="btn-primary">Izbriši</a>
    </div>
    <?php
        }
      }
    }
    izbrisi($idIzdelka);
    ?>
  </div>

<?php
  $content = ob_get_clean();
  require 'template/layout.html.php';
?>
