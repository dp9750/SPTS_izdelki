<?php ob_start(); ?>

  <script>document.title = "SPTŠ | Urejanje izdelka | <?php echo $_GET['idIzdelka'] ?>";</script>
  <link rel="stylesheet" href="template/css/urediIzdelek.css">

  <div class="content-60">
    <h2 class="text-center">Uredi izdelek</h2><br>
    <form method="post" enctype="multipart/form-data" class="mt-1 mb-1">
      <?php
      $id = $_GET['idIzdelka'];
      $link = open_database_connection();
      $sql = "SELECT * FROM izdelki WHERE id='$id'";
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
      } else {
        $result = mysqli_query($link, $sql);
        while($row = mysqli_fetch_assoc($result)) {
          ?>
            <div class="form-row mb-3">
              <div class="form-group col-md-6">
                <label for="avtor">Avtor</label>
                <input type="text" class="form-control" name="avtor" value="<?php echo $row['avtor']; ?>" placeholder="Vnesite avtorja" id="avtor" required maxlength="50">
              </div>

              <div class="form-group col-md-6">
                <label for="naslov">Naslov izdelka</label>
                <input type="text" class="form-control" name="naslov" value="<?php echo $row['naslov']; ?>" placeholder="Vnesite naslov" id="naslov" required maxlength="50">
              </div>
            </div>

            <div class="form-group mb-3">
              <label for="kratkiOpis">Kratki opis: </label>
              <textarea name="kratkiOpis" class="form-control" rows="3" cols="100" id="kratkiOpis" maxlength="100" placeholder="Kratki opis izdelka" required><?php echo $row['predstavitveniOpis']; ?></textarea>
              <small class="form-text text-muted">Max. 100 črk. </small>
            </div>

            <div class="form-group mb-3">
              <label for="opis">Opis izdelka</label>
              <textarea name="opis" class="form-control" rows="5" placeholder="Opis.." id="opis" required><?php echo $row['opis']; ?></textarea>
            </div>

            <div class="form-row mb-3">
              <div class="form-group col-md-5">
                <label for="oddelek">Oddelek</label>
                <select id="oddelek" name="oddelek" class="form-control" required>
                  <option value="izberiteoddelek">Izberite oddelek:</option>
                  <option value="tehnikracunalnistva">Tehnik računalništva</option>
                  <option value="elektrotehnik">Elektrotehnik</option>
                  <option value="tehnikoblikovanja">Tehnik oblikovanja</option>
                  <option value="strojnitehnik">Strojni tehnik</option>
                  <option value="avtomehanik">Avtomehanik</option>
                  <option value="mizar">Mizar</option>
                </select>
              </div>

              <script>
                $("#oddelek").val("<?php echo imeOddelkaReverse($row['oddelek']); ?>");
              </script>

              <div class="form-group col-md-7">
                <label for="mentor">Mentor: </label>
                <input type="text" name="mentor" class="form-control" placeholder="Vnesite mentorja" maxlength="50" id="mentor" required value="<?php echo $row['mentor']; ?>">
              </div>
            </div>

            <div class="form-group mb-3">
              <label for="solskoLeto">Šolsko leto izdelave: </label>
              <select name="solskoLeto" id="solskoLeto" class="form-control" required>
                <option value="izberiteleto">Izberi šolsko leto:</option>
                <?php
                  $trenutnoLeto = date('Y');
                  $limit = $trenutnoLeto - 10;
                  $najnovejseLeto = $trenutnoLeto;
                  foreach (range($najnovejseLeto, $limit) as $i) {
                    print '<option value="'.($i-1).'/'.$i.'"'.($i === $trenutnoLeto ? ' selected="selected"' : '').'>'.($i-1).'/'.$i.'</option>';
                  }
                ?>
              </select>
            </div>

            <script>
              $("#solskoLeto").val("<?php echo $row['solsko_leto']; ?>");
            </script>

            <div class="card mb-3">
              <div class="row no-gutters">
                <div class="col-md-4">
                  <img src="template/images/uploads/<?php echo $row['slika']; ?>" class="card-img" alt="<?php echo $row['naslov']; ?>">
                </div>
                <div class="col-md-8">
                  <div class="card-body">
                    <h6 class="card-title">Naslovna slika</h6>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" name="naslovnaSlika" id="naslovnaSlika" accept="image/png, image/jpeg, image/jpg">
                      <label class="custom-file-label" for="naslovnaSlika">Zamenjaj naslovno sliko</label>
                      <small class="form-text text-muted">Dovoljeni formati: jpg, jpeg in png. </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="text-right mb-3">
              <button type="submit" name="preuredi" class="btn btn-primary"><i class="fas fa-pen mr-1"></i> Spremeni</button>
            </div>
          <?php
        }
      }
      close_database_connection($link);
      ?>
    </form>
    <?php urediIzdelek(); ?>

    <hr class="mt-5">



    <?php
    $link = open_database_connection();
    $idIzdelka = $_GET['idIzdelka'];
    $sql = "SELECT * FROM slike WHERE idIzdelka='$idIzdelka'";
    if(!mysqli_query($link, $sql)) {
      echo "Error description: " . mysqli_error($link);
    } else {
      $result = mysqli_query($link, $sql);
      $stVrstic = mysqli_num_rows($result);
      if($stVrstic > 0) {
        while($row = mysqli_fetch_assoc($result)) {
          ?>
          <div class="card mb-3 shadow">
            <div class="card-body">
              <div class="card border-0">
                <div class="row no-gutters">
                  <div class="col-md-2">
                    <img src="template/images/uploads/<?php echo $row['potSlike']; ?>" class="card-img" alt="<?php echo $row['naslov']; ?>">
                  </div>
                  <div class="col-md-10">
                    <div class="card-body text-justify">
                      <?php $idSlike = $row['id']; ?>
                      <a href="index.php?stran=edit&idIzdelka=<?php echo $idIzdelka; ?>&izbrisiSliko=<?php echo $idSlike; ?>" class="btn btn-outline-danger mr-1"><i class="fas fa-trash-alt"></i> Izbriši sliko</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php
        }
      }
    }
    close_database_connection($link);
    ?>
    <br>
    <?php
    if(isset($_GET['izbrisiSliko'])) {
      $idSlike = $_GET['izbrisiSliko'];
      izbrisiSliko($idSlike);
    }
    ?>
  </div>

<?php
  $content = ob_get_clean();
  require 'template/layout.html.php';
?>
