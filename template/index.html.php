<?php ob_start(); ?>
<link rel="stylesheet" href="template/css/index.css">

<div class="jumbotron shadow">
  <h1 class="display-4">SPTŠ izdelki iz poklicne mature</h1>
  <p class="lead">Na tej šolski strani so prikazani maturitetni izdelkih naših dijakov.</p>
  <hr class="my-4">
  <p><?php prikaziNapis(); ?></p>
  <p class="lead">
    <a class="btn btn-primary btn-lg" href="index.php?stran=vsiIzdelki" role="button">Prikaži vse</a>
  </p>
</div>


<div class="content-60">
  <div class="card-deck">
    <?php izpisIzdekov(); ?>
  </div>
</div>

<div class="card-deck shadow-lg m-0 item-deck">
  <div class="card">
    <div class="card-body text-center item-deck-body">
      <p class="card-title title-icon"><i class="fas fa-clipboard-list"></i></p>
      <h3 class="card-title"><?php StIzdelkov(); ?></h3>
      <h5 class="card-title">Izdelkov</h5>
    </div>
  </div>
  <div class="card">
    <div class="card-body text-center item-deck-body">
      <p class="card-title title-icon"><i class="fas fa-user-tag"></i></p>
      <h3 class="card-title"><?php StOddelkov(); ?></h3>
      <h5 class="card-title">Oddelkov</h5>
    </div>
  </div>
  <div class="card">
    <div class="card-body text-center item-deck-body">
      <p class="card-title title-icon"><i class="fas fa-user-graduate"></i></p>
      <h3 class="card-title"><?php StSlik(); ?></h3>
      <h5 class="card-title">Slik</h5>
    </div>
  </div>
  <div class="card">
    <div class="card-body text-center item-deck-body">
      <p class="card-title title-icon"><i class="fas fa-images"></i></p>
      <h3 class="card-title"><?php StAvtorjev(); ?></h3>
      <h5 class="card-title">Avtorjev</h5>
    </div>
  </div>
</div>

<footer>
    <p><i class="far fa-copyright mr-1"></i>Denis Prelog 2019</p>
    <p>Mentor: Dominik Letnar</p>
</footer>

<?php
  $content = ob_get_clean();
  require 'template/layout.html.php';
?>
