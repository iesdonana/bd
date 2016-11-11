<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Menú principal</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    </head>
    <body><?php
        require "comunes/auxiliar.php";
        menu();
        $pdo = conectar_bd();
        $orden = $pdo->prepare("select * from fichas");
        $orden->execute(); ?>

        <div class="container">
            <div class="row">
                <div class="col-md-offset-1 col-md-11">
                    <h2>Próximos estrenos</h2><?php
                    foreach ($orden->fetchAll() as $fila) {
                        $id = $fila['id'];
                        $ruta = RUTA_IMG . "$id.jpg"; ?>
                        <div style="float: left; margin-right: 5px">
                            <a href="/bd/fichas/ver.php?id=<?= $id ?>">
                                <img src="<?= $ruta ?>" width="160" height="250" />
                            </a>
                        </div><?php
                    } ?>
                </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
