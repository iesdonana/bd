<?php session_start() ?>
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
    <body><?php
        require "../comunes/auxiliar.php";
        menu();
        $pdo = conectar_bd();
        $id = filter_input(INPUT_GET, "id");

        if ($id === null) {
            header("Location: index.php");
        }

        $id = trim($id);

        $orden = $pdo-> prepare("select * from fichas where id = :id");
        $orden->execute([':id' => $id]);
        $result = $orden->fetch();
        $titulo = $result['titulo'];
        $ruta = RUTA_IMG . "$id.jpg";?>

        <div class="container">
            <div class="row">
                <div class="col-md-offset-1 col-md-9">
                    <div class="panel panel-info">
                        <div class="panel-heading">Ficha</div>
                        <div class="panel-body">
                            <h2><?= htmlentities($titulo) ?> </h2>
                            <img src="<?= $ruta ?>" alt="" class="thumbnail"/>

                        </div>

                    </div>
                </div>
            </div>
        </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
