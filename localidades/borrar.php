<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Borrar una localidad</title>
    </head>
    <body><?php
        require "../comunes/auxiliar.php";

        $pdo = conectar_bd();
        $localidades = obtener_localidades($pdo);

        $localidad_id = filter_input(INPUT_POST,'localidad_id');

        if ($localidad_id !== null) {
            //hacer borrado
            $orden = $pdo->prepare("delete from localidades where id = :localidad_id");
            $orden-> execute([':localidad_id' => $localidad_id]);
            header("Location: index.php");
        }

        $localidad_id = filter_input(INPUT_GET,'localidad_id');

        if ($localidad_id === null) {
            header("Location: index.php");
        }

        $localidad_id = trim($localidad_id);

        $orden = $pdo->prepare("select loc from localidades where id = :localidad_id");
        $orden-> execute([':localidad_id' => $localidad_id]);
        $loc = $orden->fetch();?>
        <h3>¿Seguro que quiere borrar la localidad <?= htmlentities($loc['loc']) ?>?</h3>
        <form action="" method="post">
            <input type="hidden" name="localidad_id" value="<?= $localidad_id ?>">
            <input type="submit" value="Si">
            <input type="button" value="No" onclick="location.assign('index.php');">
        </form>
    </body>
</html>
