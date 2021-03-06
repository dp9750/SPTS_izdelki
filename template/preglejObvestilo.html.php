<?php ob_start(); ?>

  <link rel="stylesheet" href="template/css/pregled-obvestila.css">
  <script>document.title = "SPTŠ | Obvestilo <?php echo $_GET['idObvestila']; ?>";</script>

  <div class="content-60">
    <?php
    $link = open_database_connection();
    $id = $_GET['idObvestila'];
    $sql = "SELECT * FROM sporocila WHERE id = '$id'";
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      while($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="card">
          <h5 class="card-header">Zadeva: <?php echo $row['zadeva']; ?></h5>
          <div class="card-body">
            <p class="form-text text-muted float-right"><?php echo date('j. n. Y', strtotime($row['datum'])); ?></p>
            <h5 class="card-title"><?php echo $row['ime']; ?></h5>
            <small class="form-text text-muted mb-3"><?php echo $row['email']; ?></small>
            <p class="card-text sporocilo"><?php echo $row['sporocilo']; ?></p>
            <div class="float-right">
              <button type="button" class="btn btn-primary default-size" id="odgovori">Odgovori <i class="fas fa-comment-alt ml-1"></i></button>
            </div>
            <br>
            <form class="form-odgovori" method="post">
              <div class="form-group">
                <label for="odgovor">Napiši sporočilo: </label>
                <textarea name="odgovor" class="form-control" rows="3" placeholder="Napiši sporočilo.." required id="odgovor"></textarea>
                <small id="odgovorHelp" class="form-text text-muted">Sporočilo bo poslano na naslov <?php echo $row['email']; ?>.</small>
              </div>
              <div class="float-right">
                <button type="button" class="btn btn-secondary default-size" id="zapri">Prekliči</button>
                <button type="submit" class="btn btn-primary default-size font-16" name="poslji">Pošlji <i class="fas fa-share-square ml-1"></i></button>
              </div>
            </form>
            <br><br>
            <?php odgovori($row['email']); ?>
          </div>
        </div>
        <?php
      }
    }
    close_database_connection($link);
    ?>
  </div>

  <script>
    $(document).ready(function() {

      $("#odgovori").click(function(){
        $("#odgovori").hide();
        $(".obvestilo-buttons").hide();
        $(".form-odgovori").show();
      });

      $("#zapri").click(function() {
        $("#odgovori").show();
        $(".obvestilo-buttons").show();
        $(".form-odgovori").hide();
      });

    });
  </script>

<?php
  $content = ob_get_clean();
  require 'template/layout.html.php';
?>
