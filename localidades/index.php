<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
    </head>
    <body><?php
        require '../comunes/auxiliar.php';



        $loc = filter_input(INPUT_GET, "loc");?>

        <form action="" method="get">
            <h4>Localidades</h4>
            <label for="localidad_id">Localidades:</label>
            <input type="text" name="loc" value="<?= htmlentities($loc) ?>">
            <input type="submit" value="Buscar" />
            <input type="button" value="Insertar" onclick="location.assign('insertar.php')" />
        </form><?php

        try {
            $error = [];
            comprobar_loc($loc, $error);
            comprobar_errores($error);
            $pdo = conectar_bd();
            $result = buscar_por_loc($pdo, $loc);
            comprobar_si_vacio($result, $error);
            comprobar_errores($error);
            dibujar_tabla_localidades($result);
        } catch (PDOException $e) { ?>
            <h3>Error de conexión a la base de datos</h3><?php
        } catch (Exception $e) {
            mostrar_errores($error);
        } ?>
    </body>
</html>
