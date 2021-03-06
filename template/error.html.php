<?php ob_start(); ?>

  <div id="errorbox">
    <h1>Ta stran ne obstaja!</h1>
    <h3>Znova vnesite veljaven URL naslov</h3>
    <i class="fas fa-exclamation-triangle"></i>
  </div>

<?php
  $content = ob_get_clean();
  require 'template/layout.html.php';
?>
