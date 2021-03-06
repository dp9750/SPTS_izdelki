<?php ob_start(); ?>

  <link rel="stylesheet" href="template/css/nastavitve.css">

  <div class="content-60">
    <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-spremembaGesla-tab" data-toggle="tab" href="#nav-spremembaGesla" role="tab" aria-controls="nav-spremembaGesla" aria-selected="true">Sprememba gesla</a>
        <?php
        if(isset($_SESSION['upIme'])) {
          if($_SESSION['upIme'] == "admin") {
            ?>
            <a class="nav-item nav-link" id="nav-uporabniki-tab" data-toggle="tab" href="#nav-uporabniki" role="tab" aria-controls="nav-uporabniki" aria-selected="false">Uporabniki</a>
            <a class="nav-item nav-link" id="nav-obvestila-tab" data-toggle="tab" href="#nav-obvestila" role="tab" aria-controls="nav-obvestila" aria-selected="false"><?php badgeObestila(); ?>Obvestila</a>
            <a class="nav-item nav-link" id="nav-napis-tab" data-toggle="tab" href="#nav-napis" role="tab" aria-controls="nav-napis" aria-selected="false">Napis</a>
            <?php
          }
        }
        ?>
      </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="nav-spremembaGesla" role="tabpanel" aria-labelledby="nav-spremembaGesla-tab">
        <form method="post">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="novoGeslo">Novo geslo</label>
              <input type="password" class="form-control" name="novoGeslo" placeholder="Vnesite novo geslo" id="novoGeslo" required>
            </div>
            <div class="form-group col-md-6">
              <label for="novoGesloDrugic">Ponovno vnesite geslo</label>
              <input type="password" class="form-control" name="novoGesloDrugic" placeholder="Ponovno vnesite novo geslo" id="novoGesloDrugic" required>
            </div>
          </div>
          <div class="form-group">
            <label for="staroGeslo">Trenutno geslo</label>
            <input type="password" class="form-control" name="staroGeslo" id="staroGeslo" placeholder="Vnesite trenutno geslo" required>
          </div>
          <div class="right">
            <button type="submit" name="spremeni" class="btn btn-primary"><i class="fas fa-key"></i> Spremeni geslo</button>
          </div>
        </form>
        <br>
        <?php spremembaGesla(); ?>
      </div>
      <div class="tab-pane fade" id="nav-uporabniki" role="tabpanel" aria-labelledby="nav-uporabniki-tab">
        <div class="float-right">
          <button type="button" class="btn btn-info default-size" data-toggle="modal" data-target="#modalDodajUporabnika">Dodaj uporabnika <i class="fas fa-user-plus ml-1"></i></button>
        </div>
        <br><br>
        <div class="accordion" id="accordionUser">
          <?php prikaziUporabnike(); ?>
        </div>
      </div>
      <div class="tab-pane fade" id="nav-obvestila" role="tabpanel" aria-labelledby="nav-obvestila-tab">
        <?php
          preveriObvestila();
          preveriIzpisObvestil();
        ?>
      </div>
      <div class="tab-pane fade" id="nav-napis" role="tabpanel" aria-labelledby="nav-napis-tab">
        <form method="post">
          <div class="form-group">
            <label for="napis">Napis:</label>
            <input type="text" class="form-control" id="napis" aria-describedby="napis" placeholder="Vnesi napis" name="napis" value="<?php prikaziNapis(); ?>">
            <small id="napis" class="form-text text-muted">Napis bo viden na naslovni strani kot manjši naslov. </small>
          </div>
          <div class="text-right">
            <button type="submit" class="btn btn-primary" name="ustvariNapis">Potrdi</button>
          </div>
          <br>
        </form>
        <?php zamenjajNapis(); ?>
      </div>
    </div>

    <div class="modal fade" id="modalIzbrisiVse" tabindex="-1" role="dialog" aria-labelledby="modalIzbrisiVseLbl" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalIzbrisiVseLbl">Izbriši vsa obvestila</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Ste preričani da želite izbrisati vsa obvestila? </p>
          </div>
          <div class="modal-footer">
            <form method="post">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Prekliči</button>
              <button type="submit" name="izbrisiVse" class="btn btn-danger">Izbriši vse</button>
            </form>
            <?php izbrisiVsaObvestila(); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalIzbrisiObvestilo" tabindex="-1" role="dialog" aria-labelledby="modalSpremmebaGeslaLbl" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalSpremmebaGeslaLbl">Izbris obvestila</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Zapri">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Ste prepričani da želite izbrisati obvestilo?</p>
            <form method="post">
            <input type="text" name="ObvestiloID" required class="form-control" hidden><br>
            <?php izbrisiObvestilo(); ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Prekliči</button>
              <button type="submit" class="btn btn-danger" name="izbrisiObvestilo">Izbriši</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="modalDodajUporabnika" tabindex="-1" role="dialog" aria-labelledby="modalDodajUporabnikaLbl" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalDodajUporabnikaLbl">Dodaj uporabnika</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Prekliči">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post">
              <div class="form-group">
                <label for="DodajUsername">Uporabniško ime: </label>
                <input type="text" class="form-control" name="DodajUsername" id="DodajUsername" value="<?php if(isset($_POST['DodajUsername'])) { echo $_POST['DodajUsername']; } ?>" placeholder="Vnesi uporabniško ime" required>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="DodajPassword">Geslo: </label>
                  <input type="password" class="form-control" name="DodajPassword" id="DodajPassword" value="<?php if(isset($_POST['DodajPassword'])) { echo $_POST['DodajPassword']; } ?>" placeholder="Vnesi geslo za uporabnika" required>
                </div>
                <div class="form-group col-md-6">
                  <label for="DodajPasswordPonovi">Ponovi geslo: </label>
                  <input type="password" class="form-control" name="DodajPasswordPonovi" id="DodajPasswordPonovi" value="<?php if(isset($_POST['DodajPasswordPonovi'])) { echo $_POST['DodajPasswordPonovi']; } ?>" placeholder="Vnesi geslo za uporabnika" required>
                </div>
              </div>
              <div class="form-group">
                <label for="DodajEmail">Email: </label>
                <input type="email" class="form-control" name="DodajEmail" id="DodajEmail" value="<?php if(isset($_POST['DodajEmail'])) { echo $_POST['DodajEmail']; } ?>" placeholder="Vnesi uporabnikov email" required>
              </div>
              <br>
              <?php dodajUporabnika(); ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Prekliči</button>
            <button type="submit" name="ustvari" class="btn btn-primary">Dodaj <i class="fas fa-user-plus ml-1"></i></button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalSpremmebaGesla" tabindex="-1" role="dialog" aria-labelledby="modalSpremmebaGeslaLbl" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSpremmebaGeslaLbl">Spremeni geslo uporabniku ''</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Prekliči">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="post" id="spremembaGesla">
            <div class="form-group">
              <label for="recipient-ime" class="col-form-label">Ime uporabnika:</label>
              <input type="text" class="form-control" id="recipient-ime" name="UserName" maxlength="50" placeholder="Uporabniško ime uporabnika" required>
            </div>
            <div class="form-group">
              <label for="novoGeslo">Novo geslo: </label>
              <input type="password" class="form-control" id="novoGeslo" placeholder="Vnesi novo geslo" name="novoGeslo" required>
            </div>
            <div class="form-group">
              <label for="novoGesloDrugic">Ponovno vnesi geslo: </label>
              <input type="password" class="form-control" id="novoGesloDrugic" placeholder="Ponovno vnesi geslo" name="novoGesloDrugic" required>
            </div>
            <br>
          </form>
          <?php spremembaGeslaSubUser(); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Prekliči</button>
          <button type="submit" class="btn btn-primary default-size" name="spremeniGeslo" form="spremembaGesla">Spremeni geslo <i class="fas fa-user-edit ml-1"></i></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.title = 'SPTŠ | Nastavitve';
    $(document).ready(function() {

      $('#modalIzbrisiObvestilo').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var recipient = button.data('whatever')
        var modal = $(this)
        modal.find('.modal-body input').val(recipient)
      })

      $('#modalSpremmebaGesla').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var recipient = button.data('username')
        var modal = $(this)
        modal.find('.modal-title').text('Spremeni geslo uporabniku  ' + "'" + recipient + "'")
        modal.find('.modal-body input#recipient-ime').val(recipient)
      })

    });
  </script>

  <?php
    $content = ob_get_clean();
    require 'template/layout.html.php';
  ?>
