<?php ob_start(); ?>

<script>document.title = 'SPTŠ | Urejanje';</script>

    <div class="content-60 p-0 bg-transparent">
    <?php
      $link = open_database_connection();
      $username = $_SESSION['upIme'];
      $idUporabnika = 1;
      $sql = "SELECT id FROM admin WHERE username='$username'";
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
      } else {
        $result = mysqli_query($link, $sql);
        while($row = mysqli_fetch_assoc($result)) {
          $idUporabnika = $row['id'];
        }
      }
      if($username == "admin") {
        $sql = "SELECT * FROM izdelki ORDER BY datum DESC";
      } else {
        $sql = "SELECT * FROM izdelki WHERE idUporabnika = '$idUporabnika' ORDER BY datum DESC";
      }
      if(!mysqli_query($link, $sql)) {
        echo "Error description: " . mysqli_error($link);
      } else {
        $result = mysqli_query($link, $sql);
        if(mysqli_num_rows($result) == 0) {
          echo "<br><h3>Ta uporabnik nima dostopa do nobenih izdelkov. </h3><br><p>Ali želite <a href='index.php?stran=dodajIzdelek' class='link'>dodati</a> izdelke? </p>";
        } else {
          while($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="card border-secondary shadow">
              <div class="card-header text-right">
                <a href="index.php?stran=izdelek&idIzdelka=<?php echo $row['id']; ?>" class="mr-1 btn-more"><i class="fas fa-plus"></i> </a>
                <a href="index.php?stran=edit&idIzdelka=<?php echo $row['id']; ?>" class="mr-1 btn-edit"><i class="fas fa-edit"></i> </a>
                <a href="javascript:void(0)" class="btn-del" data-toggle="modal" data-target="#modalIzbrisiIzdelek" data-whatever="<?php echo $row['id']; ?>"><i class="fas fa-trash-alt"></i> </a>
              </div>
              <div class="card-body">
                <div class="card border-0">
                  <div class="row no-gutters">
                    <div class="col-md-2">
                      <img src="template/images/uploads/<?php echo $row['slika']; ?>" class="card-img" alt="<?php echo $row['naslov']; ?>">
                    </div>
                    <div class="col-md-10">
                      <div class="card-body text-justify">
                        <h5 class="card-title"><?php echo $row['naslov']; ?></h5>
                        <p class="card-text"><?php echo $row['predstavitveniOpis']; ?></p>
                        <p class="card-text"><small class="text-muted">Dodano: <?php echo date('d. ', strtotime($row['datum'])) . vrniSLODatum(date('F', strtotime($row['datum']))) . date(' Y', strtotime($row['datum'])); ?></small></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <?php
          }
        }
      }
      close_database_connection($link);
    ?>
    </div>

    <div class="modal fade" id="modalIzbrisiIzdelek" tabindex="-1" role="dialog" aria-labelledby="modalIzbrisiIzdelekLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalIzbrisiIzdelekLabel">Izbris izdelka</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Zapri">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Ali ste prepričani da želite izbrisati izdelek? </p>
            <form method="post" id="izbrisIzdelka">
              <input type="text" class="form-control" id="recipient-name" hidden name="recipient-id">
            </form>
            <?php izbrisi(); ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zapri</button>
            <button type="submit" class="btn btn-danger" name="izbrisi" form="izbrisIzdelka">Izbriši</button>
          </div>
        </div>
      </div>
    </div>

    <script>
    $(document).ready(function() {
      $('#modalIzbrisiIzdelek').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var recipient = button.data('whatever')
        var modal = $(this)
        modal.find('.modal-body input').val(recipient)
      })
    })
    </script>

<?php
  $content = ob_get_clean();
  require 'template/layout.html.php';
?>
