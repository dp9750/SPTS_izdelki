<?php ob_start(); ?>

  <link rel="stylesheet" href="template/css/dodajanje.css">
  <script>document.title = 'SPTŠ | Dodaj izdelek';</script>

  <div class="content-60">
    <h1>Dodajanje izdelkov</h1>
    <form method="post" enctype="multipart/form-data">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="avtor">Avtor: </label>
          <input type="text" class="form-control" name="avtor" placeholder="Vnesite avtorja" maxlength="50" id="avtor" required>
        </div>
        <div class="form-group col-md-6">
          <label for="naslov">Naslov: </label>
          <input type="text" class="form-control" name="naslov" placeholder="Vnesite naslov izdelka" maxlength="50" id="naslov" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-5">
          <label for="mentor">Mentor: </label>
          <input type="text" class="form-control" name="mentor" placeholder="Vnesite mentorja" maxlength="50" id="mentor" required>
        </div>
        <div class="form-group col-md-4">
          <label for="oddelek">Smer: </label>
          <select name="oddelek" id="oddelek" class="form-control" required>
            <option value="izberiteoddelek">Izberite smer:</option>
            <option value="tehnikracunalnistva">Tehnik računalništva</option>
            <option value="elektrotehnik">Elektrotehnik</option>
            <option value="tehnikoblikovanja">Tehnik oblikovanja</option>
            <option value="strojnitehnik">Strojni tehnik</option>
            <option value="avtomehanik">Avtomehanik</option>
            <option value="mizar">Mizar</option>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label for="datum">Datum: </label>
          <input type="date" name="datum" class="form-control" id="datum" value="<?php echo date("Y-m-d"); ?>">
        </div>
      </div>
      <div class="form-group">
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
      <div class="form-group">
        <label for="kratkiOpis">Kratki opis: </label>
        <textarea name="kratkiOpis" class="form-control" rows="3" cols="100" id="kratkiOpis" maxlength="100" placeholder="Kratki opis izdelka" aria-describedby="textOpis" required></textarea>
        <small id="textOpis" class="form-text text-muted">Max. 100 črk. </small>
      </div>
      <div class="form-group">
        <label for="opis">Opis: </label>
        <textarea name="opis" class="form-control" rows="5" id="opis" placeholder="Opis izdelka" required></textarea>
      </div>
      <div class="form-group">
        <div class="input-group mb-3">
          <div class="custom-file">
            <input type="file" class="custom-file-input" name="naslovnaSlika" id="naslovnaSlika" accept="image/png, image/jpeg, image/jpg" required>
            <label class="custom-file-label" for="naslovnaSlika">Naslovna slika</label>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="input-group mb-3">
          <div class="custom-file">
            <input type="file" class="custom-file-input" name="slike[]" id="slike" accept="image/png, image/jpeg, image/jpg" multiple>
            <label class="custom-file-label" for="slike">Dodaj več slik</label>
          </div>
        </div>
      </div>
      <div class="right">
        <button type="submit" name="dodaj" class="btn btn-primary"><i class="fas fa-plus"></i> Dodaj izdelek</button>
      </div>
      <br><br>
    </form>
    <?php dodajIzdelek(); ?>
  </div>

  <script src="template/js/custom-select.js"></script>

  <?php
    $content = ob_get_clean();
    require 'template/layout.html.php';
  ?>
