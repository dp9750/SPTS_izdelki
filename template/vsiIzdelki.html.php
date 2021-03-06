<?php ob_start(); ?>

<link rel="stylesheet" href="template/css/vsi-izdelki.css">

<div class="moznosti" <?php if(isset($_SESSION['upIme']))  { ?> style="display: none;" <?php }?>>
  <button class="moznosti-buttom <?php if(!isset($_SESSION['upIme']) && !isset($_GET['oddelek']) && !isset($_GET['solskoleto']) && !isset($_GET['avtor']) && !isset($_GET['naslov'])) { echo " moznosti-active"; } ?>">Smer</button>
  <div class="moznosti-vsebina" <?php if(!isset($_SESSION['upIme']) && !isset($_GET['oddelek']) && !isset($_GET['solskoleto']) && !isset($_GET['avtor']) && !isset($_GET['naslov'])) { echo "style='display: block'"; } ?>>
    <form method="post">
      <label class="label-item">Označi vse
        <input type="checkbox" name="oddelek" value="Izberi vse" onclick="izberiVse();" <?php if(!isset($_GET['oddelek'])) { echo " checked"; } ?>>
        <span class="checkmark"></span>
      </label>
      <?php prikaziCheckboxe(); ?>
      <br><button type="submit" name="dodajFilterOddelki" class='btn-primary btn-block'>Potrdi</button>
    </form>
    <?php prikaziPoOddelkih(); ?>
  </div>
  <button class="moznosti-buttom <?php if(isset($_GET['solskoleto']))  { echo "moznosti-active"; }?>">Leto</button>
  <div class="moznosti-vsebina" <?php if(isset($_GET['solskoleto']))  { ?> style="display: block;" <?php }?>>
    <form method="post">
      <select class="" name="solskoleto">
        <option value="izberidatum">Izberi datum</option>
        <?php prikaziSelectOptions(); ?>
      </select>
      <br>
      <button type="submit" name="filterLeto" class='btn-primary btn-block'>Potrdi</button>
    </form>
    <?php prikaziPoLetu(); ?>
  </div>
  <button class="moznosti-buttom <?php if(isset($_GET['avtor']))  { echo "moznosti-active"; }?>"><i class="fas fa-user-tie"></i> Išči po avtorju</button>
  <div class="moznosti-vsebina ime-avtorja" <?php if(isset($_GET['avtor']))  { ?> style="display: block;" <?php }?>>
    <form>
      <label for="imeAvtorja">Ime avtorja: </label>
      <input type="text" onkeyup="showPredlogHTML(this.id, 'panelPredlogiAvtor', 'txtHint'); showHint(this.value)" id="imeAvtorja" placeholder="Išči.." maxlength="40" value="<?php if(isset($_GET['avtor'])) { echo $_GET['avtor']; } ?>">
      <div id="panelPredlogiAvtor"></div>
    </form>
  </div>
  <button class="moznosti-buttom <?php if(isset($_GET['naslov']))  { echo "moznosti-active"; }?>"><i class="fas fa-search"></i> Išči po naslovu</button>
  <div class="moznosti-vsebina naslov" <?php if(isset($_GET['naslov']))  { ?> style="display: block;" <?php }?>>
    <form>
      <label for="naslov">Naslov: </label>
      <input type="text" onkeyup="showPredlogHTML(this.id, 'panelPredlogiNaslov', 'txtHintNaslov'); showHintNaslov(this.value);" id="naslov" placeholder="Išči.." maxlength="40" value="<?php if(isset($_GET['naslov'])) { echo $_GET['naslov']; } ?>">
      <div id="panelPredlogiNaslov"></div>
    </form>
  </div>
</div>

<div id="wrap">
  <header>
    <span class="list-style-buttons">
      <?php
      if(!isset($_SESSION['upIme'])) {
        ?>
        <button id="gridview" class="btn-primary switcher"><i class='fas fa-th'></i></button>
        <button id="listview" class="btn-primary switcher btn-active"><i class='fas fa-tasks'></i></button>
        <?php
      }
      ?>
    </span>
    <h2>Vsi izdelki</h2>
  </header>
  <ul id="products" class="list clearfix">
    <?php filterIzdelki(); ?>
  </ul>
  <footer>
    &nbsp;
  </footer>
</div>

<script src="template/js/vsi-izdelki.js"></script>

<?php
  $content = ob_get_clean();
  require 'template/layout.html.php';
?>
