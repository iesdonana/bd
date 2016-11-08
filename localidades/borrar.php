<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Borrar una localidad</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <style type="text/css">
            body { padding: 60px; }
        </style>
    </head>
    <body><?php
        require "../comunes/auxiliar.php";

        $localidad_id = filter_input(INPUT_POST, "localidad_id");

        if ($localidad_id !== null) {
            $pdo = conectar_bd();
            $orden = $pdo->prepare("delete from localidades
                                     where id = :localidad_id");
            $orden->execute([':localidad_id' => $localidad_id]);
            header("Location: index.php");
        }

        $localidad_id = filter_input(INPUT_GET, "localidad_id");

        if ($localidad_id === null) {
            header("Location: index.php");
        }

        $localidad_id = trim($localidad_id);
        $pdo = conectar_bd();

        if (empty(buscar_por_localidad_id($pdo, $localidad_id))) { ?>
            <h3>Error: la localidad <?= htmlentities($localidad_id) ?> no existe</h3>
            <a href="index.php" class="btn btn-warning" role="button">Volver</a><?php
        } else { ?>
            <h3>¿Seguro que quiere borrar la localidad <?= htmlentities($localidad_id) ?>?</h3>
            <form action="" method="post">
                <input type="hidden" name="localidad_id" value="<?= htmlentities($localidad_id) ?>" />
                <button type="submit" class="btn btn-default">Sí</button>
                <a href="index.php" class="btn btn-warning" role="button">No</a>
            </form><?php
        } ?>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
