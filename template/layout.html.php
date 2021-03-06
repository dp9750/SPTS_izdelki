<!DOCTYPE html>
<html lang="sl">
  <head>
    <title>SPTŠ | Izdelki iz poklicne mature</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.5.0/css/all.css' integrity='sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU' crossorigin='anonymous'>
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <link rel="stylesheet" href="template/css/style.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  </head>
  <body>

    <div id="navbar">
      <a href="index.php" id="logo">SPTŠ izdelki</a>
      <div id="navbar-right">
        <a href="index.php" class="active">Domov</a>
        <a href="index.php?stran=vsiIzdelki">Vsi izdelki</a>
        <a href="index.php?stran=kontakt">Kontakt</a>
        <?php if(!isset($_SESSION['upIme'])) {echo "<a href='javascript:void(0)' data-toggle='modal' data-target='#modalPrijava'>Prijava</a>";} ?>
        <?php if(isset($_SESSION['upIme'])) { echo "<a href='index.php?stran=nastavitve'>" . $_SESSION['upIme'] . "</a>"; } ?>
        <a href="javascript:void(0);" class="icon" onclick="navbarSwitch()"><i class="fa fa-bars"></i></a>
      </div>
    </div>

    <div class="modal fade" id="modalPrijava" tabindex="-1" role="dialog" aria-labelledby="modalPrijavaLbl" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalPrijavaLbl">Prijava administratorja</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Zapri">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post" id="formPrijava">
              <div class="form-group">
                <label for="uname">Uporabniško ime: </label>
                <input type="text" class="form-control" id="uname" placeholder="Vnesi uporabniško ime" maxlength="50" name="uname" required>
              </div>
              <div class="form-group">
                <label for="password">Geslo: </label>
                <input type="password" class="form-control" id="password" placeholder="Vnesi geslo" maxlength="30" name="psw" required>
              </div>
            </form>
            <br>
            <?php prijava(); ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-link" data-dismiss="modal" data-toggle="modal" data-target="#modalPozabljenoGeslo">Pozabljeno geslo? </button>
            <button type="submit" class="btn btn-primary" name="prijava" form="formPrijava">Prijava</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalPozabljenoGeslo" tabindex="-1" role="dialog" aria-labelledby="PozabljenoGeslo" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="PozabljenoGeslo">Pozabljeno geslo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Zapri">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="post" id="formPozabljenoGeslo">
              <div class="form-group">
                <label for="username">Uporabniško ime: </label>
                <input type="text" class="form-control" id="username" aria-describedby="unameHelp" placeholder="Vnesi uporabniško ime" name="username" required>
                <small id="unameHelp" class="form-text text-muted">Vpišite uporabniško ime. Na vaš email vam bomo poslali novo geslo. </small>
              </div>
              <div class="form-group">
                <label for="email">Email: </label>
                <input type="email" class="form-control" id="email" placeholder="Vpišite email" name="email" required>
              </div>
            </form>
            <br>
            <?php pozabljenoGeslo(); ?>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" name="pozabljeno" form="formPozabljenoGeslo">Pošlji novo geslo</button>
          </div>
        </div>
      </div>
    </div>

    <?php if(isset($_SESSION['upIme'])) { ?>
      <div class='admin-bar' id='adminBar'>
        <a href='index.php?odjava=true'><i class="fas fa-sign-out-alt"></i> Odjava</a>
        <a href='index.php'><i class="fas fa-home"></i> Domov</a>
        <a href='index.php?stran=dodajIzdelek'><i class="fas fa-plus"></i> Dodaj</a>
        <a href="index.php?stran=uredi"><i class="fas fa-edit"></i> Uredi</a>
        <a href='index.php?stran=uredi'><i class="fas fa-trash-alt"></i> Odstrani</a>
        <a href='index.php?stran=nastavitve'><i class="fas fa-user-cog"></i> Admin</a>
      </div>
    <?php } ?>

    <div class="content" <?php if(isset($_SESSION['upIme'])) { echo "style='margin-left: 15%;'"; } ?>>
      <?php echo $content; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="template/js/script.js"></script>

  </body>
</html>
