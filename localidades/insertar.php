<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Insertar un departamento</title>
    </head>
    <body><?php
        require "../comunes/auxiliar.php";

        menu();

        $loc = filter_input(INPUT_POST, "loc");

        try {
            $error = [];
            comprobar_existen([$loc]);
            comprobar_loc($loc, $error, ESC_INSERTAR);
            comprobar_errores($error);
            $pdo = conectar_bd();
            $orden = $pdo->prepare("insert into localidades (loc)
                                    values (:loc)");
            $orden->execute([
                ':loc' => $loc
            ]);
            header("Location: index.php");
        } catch (PDOException $e) { ?>
            <h3>Error de conexión a la base de datos</h3><?php
        } catch (Exception $e) {
            mostrar_errores($error);
        }

     ?>
        <form action="" method="post">
            <label for="localidad_id">Localidad *:</label>
            <input type="text" id="localidad_id" name="loc" value="<?= htmlentities($loc) ?>"><br />
            <input type="submit" value="Insertar" />
            <input type="reset" value="Limpiar" />
            <a href="index.php">Cancelar</a>
        </form>
    </body>
</html>
