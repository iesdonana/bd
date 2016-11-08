<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
    </head>
    <body><?php
        require '../comunes/auxiliar.php';

        menu();

        $pdo = conectar_bd();

        try {
            $loc = filter_input(INPUT_GET, "loc"); ?>
            <form action="" method="get">
                <label for="loc">Localidad:</label>
                <input type="text" id="loc" name="loc" value="<?= htmlentities($loc) ?>" /><br/>
                <input type="submit" value="Buscar" />
                <input type="reset" value="Limpiar" />
                <a href="insertar.php">Insertar</a>
            </form><?php
            $error = [];
            comprobar_loc($loc, $error);
            comprobar_errores($error);
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
