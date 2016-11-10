<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Borrar un departamento</title>
    </head>
    <body><?php
        require "../comunes/auxiliar.php";

        comprobar_logueado();
        menu();

        $localidad_id = filter_input(INPUT_POST, "localidad_id");

        if ($localidad_id !== null) {
            $pdo = conectar_bd();
            $orden = $pdo->prepare("delete from localidades where id = :id");
            $orden->execute([':id' => $localidad_id]);
            header("Location: index.php");
        }

        $localidad_id = filter_input(INPUT_GET, "localidad_id");

        if ($localidad_id === null) {
            header("Location: index.php");
        }

        $localidad_id = trim($localidad_id);
        $pdo = conectar_bd();

        if (empty(buscar_por_localidad_id($pdo, $localidad_id))) { ?>
            <h3>Error: el departamento <?= htmlentities($localidad_id) ?> no existe</h3>
            <a href="index.php" role="button">Volver</a><?php
        } else { ?>
            <h3>¿Seguro que quiere borrar el departamento <?= htmlentities($localidad_id) ?>?</h3>
            <form action="" method="post">
                <input type="hidden" name="localidad_id" value="<?= htmlentities($localidad_id) ?>" />
                <button type="submit">Si</button>
                <a href="index.php" role="button">No</a>
            </form><?php
        } ?>
    </body>
</html>
