<?php ob_start(); ?>

  <script>document.title = 'SPTŠ | Kontakt';</script>
  <link rel="stylesheet" href="template/css/kontakt.css">

  <div class="content-60">
    <h1>Pišite nam</h1>
    <form method="post">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="lblIme">Ime in priimek: </label>
          <input type="text" class="form-control" id="lblIme" placeholder="Vnesite vaše ime" name="name" maxlength="30" required value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>">
        </div>
        <div class="form-group col-md-6">
          <label for="lblEmail">Email: </label>
          <input type="email" class="form-control" id="lblEmail" placeholder="Vnesite vaš email" name="kontaktEmail" required value="<?php if(isset($_POST['kontaktEmail'])) echo $_POST['kontaktEmail']; ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="lblZadeva">Zadeva: </label>
        <input type="text" class="form-control" id="lblZadeva" placeholder="Vnesite zadevo" name="zadeva" required value="<?php if(isset($_POST['zadeva'])) echo $_POST['zadeva']; ?>">
      </div>
      <div class="form-group">
        <label for="oseba">Oseba: </label>
        <select id="oseba" class="form-control" name="oseba" required>
          <option value="">Kdo ste?</option>
          <option value="dijak">Dijak šole</option>
          <option value="ostali">Ostali</option>
        </select>
      </div>
      <div class="form-group">
        <label for="sporocilo">Sporočilo: </label>
        <textarea class="form-control" name="sporocilo" rows="6" placeholder="Sporočilo.." id="sporocilo" maxlength="300" required><?php if(isset($_POST['sporocilo'])) echo $_POST['sporocilo']; ?></textarea>
      </div>
      <div class="right">
        <button type="submit" name="sporoci" class="btn btn-primary">Pošlji sporočilo</button>
      </div>
      <br>
    </form>
    <?php kontaktiraj(); ?>
  </div>

  <?php
    $content = ob_get_clean();
    require 'template/layout.html.php';
  ?>
