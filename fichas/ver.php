<?php session_start(); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    </head>
    <body> <?php
        require('../comunes/auxiliar.php');
        menu();

        $id = filter_input(INPUT_GET, "id");

        if ($id === null) {
            header("Location: /iesdonana/bd/");
        }

        $pdo = conectar_bd();
        $orden = $pdo->prepare("select * from fichas where id = :id");
        $orden->execute([':id' => $id]);
        $result = $orden->fetch(); ?>

        <div class="container">
            <div class="row">
                <div class="col-md-offset-1 col-md-9">
                    <h2 id="titulo"><?= htmlentities($result['titulo']); ?></h2> <hr/>
                    <ul class="nav nav-pills" role="tablist">
                      <li role="presentation" class="active"><a href="#">Ficha</a></li>
                      <li role="presentation"><a href="#">Criticas <span class="badge">34</span></a></li>
                      <li role="presentation"><a href="#">Trailers <span class="badge">2</span></a></li>
                      <li role="presentation"><a href="#">Imágenes <span class="badge">6</span></a></li>
                    </ul>
                    <hr/>
                    <div class="contenido" >
                        <p style="float:left; width:60%">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </p>

                    <?php
                        $id = $result['id'];
                        $ruta =  RUTA_IMG . "$id.jpg"; ?>
                        <div style="float:left; margin-left: 100px">
                            <div class="thumbnail">
                            <p>
                                <img src="<?= $ruta ?>" width="160" height="250" />
                            </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <script type="text/javascript">
            document.title = document.getElementById("titulo").innerHTML;
        </script>

    </body>
</html>
