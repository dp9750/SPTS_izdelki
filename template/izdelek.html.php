<?php ob_start(); ?>

  <script>document.title = 'SPTŠ | Izdelek';</script>

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
          <h1>Izdelek: <?php echo $row['naslov']; ?></h1>

          <div class="card mb-4 shadow">
            <div class="row no-gutters">
              <div class="col-md-5">
                <img src="template/images/uploads/<?php echo $row['slika']; ?>" class="card-img pointer" alt="<?php echo $row['naslov']; ?>" data-toggle="modal" data-target="#modalGalerija"> <!--onclick="openModal();currentSlide(1)  -->
              </div>
              <div class="col-md-7">
                <div class="card-body text-justify">
                  <h5 class="card-title d-inline-block"><?php echo $row['naslov']; ?></h5><small class="text-muted ml-3 float-right"><?php echo date('d. n. Y', strtotime($row['datum'])); ?></small>
                  <p class="card-text"><?php echo $row['predstavitveniOpis']; ?></p>
                  <p class="card-text"><b>Avtor: </b><?php echo $row['avtor'] ?></p>
                  <p class="card-text"><b>Mentor: </b><?php echo $row['mentor']; ?></p>
                  <p class="card-text"><b>Oddelek:</b> <?php echo $row['oddelek']; ?></p>
                  <p class="card-text"><b>Šolsko leto izdelave: </b><?php echo $row['solsko_leto']; ?></p>
                </div>
              </div>
            </div>
          </div>

          <div class="sporocilo mt-4 mb-5 p-3 text-justify shadow">
            <h5>Več o izdelku</h5>
            <p><?php echo $row['opis']; ?></p>
          </div>

          <hr>

          <div class="mt-5 card-deck">
            <?php prikaziVecSlik(); ?>
          </div>
          <?php
        }
      } else {
        ?>
        <script>document.getElementsByClassName('content-60')[0].style.textAlign = "center";</script>
        <div id="errorbox">
          <h1>Ta stran ne obstaja!</h1>
          <h3>Znova vnesite veljaven URL naslov</h3>
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <?php
      }
    }
    close_database_connection($link);
    ?>
  </div>

  <div class="modal fade bd-example-modal-lg" id="modalGalerija" tabindex="-1" role="dialog" aria-labelledby="modalGalerijaLbl" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body p-0" id="modalGalerijaLbl">
          <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
              <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
              <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
              <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
              <?php
              $idIzdelka = $_GET['idIzdelka'];
              $link = open_database_connection();
              $sql = "SELECT naslov, slika FROM izdelki WHERE id='$idIzdelka';";
              if(!mysqli_query($link, $sql)) {
                echo "Error description: " . mysqli_error($link);
              } else {
                $result = mysqli_query($link, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                  ?>
                  <div class="carousel-item active">
                    <img class="d-block w-100" src="template/images/uploads/<?php echo $row['slika']; ?>" alt="<?php echo $row['naslov']; ?>">
                  </div>
                  <?php
                }
              }
              if(imaVecSlik()) {
                $sql = "SELECT potSlike FROM slike WHERE idIzdelka='$idIzdelka'";
                if(!mysqli_query($link, $sql)) {
                  echo "Error description: " . mysqli_error($link);
                } else {
                  $result = mysqli_query($link, $sql);
                  $i = 2;
                  while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="carousel-item">
                      <img class="d-block w-100" src="template/images/uploads/<?php echo $row['potSlike']; ?>" alt="<?php echo $row['potSlike']; ?>">
                    </div>
                    <?php
                  }
                }
              }
              close_database_connection($link);
              ?>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Prejšnja</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Naslednja</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php
  $content = ob_get_clean();
  require 'template/layout.html.php';
?>
