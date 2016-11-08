<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Modificar una localidad</title>
    </head>
    <body><?php
        require "../comunes/auxiliar.php";

        $localidad_id = filter_input(INPUT_GET, "localidad_id");
        $pdo = conectar_bd();

        if ($localidad_id !== null) {
            $result = buscar_por_localidad_id($pdo, $localidad_id);
            if (empty($result)) {
                header("Location: index.php");
            }
            $loc = $result['loc'];
        } else {
            $localidad_id = filter_input(INPUT_POST, "localidad_id");
            $loc = filter_input(INPUT_POST, "loc");

            try {
                $error = [];
                comprobar_existen([$localidad_id, $loc]);
                comprobar_loc($loc, $error, ESC_INSERTAR);
                comprobar_localidad_id($localidad_id, $pdo, $error);
                comprobar_errores($error);
                $orden = $pdo->prepare("update localidades
                                        set loc = :loc
                                        where id = :localidad_id");
                $orden->execute([
                    ':loc' => $loc,
                    ':localidad_id' => $localidad_id]);
                header("Location: index.php");
            } catch (PDOException $e) { ?>
                <h3>Error de conexión a la base de datos</h3><?php
            } catch (Exception $e) {
                mostrar_errores($error);
            }
        }?>
        <form action="modificar.php" method="post">
            <h4>Modificar la localidad <?= htmlentities($loc) ?></h4>
            <input type="hidden" name="localidad_id" value="<?= $localidad_id ?>">
            <label for="dept_no">Nombre de la localidad*:</label>
            <input type="text" id="loc" name="loc"
            value="<?= htmlentities($loc) ?>"/><br/>
            <input type="submit" value="Modificar" />
            <input type="button" value="Cancelar" onclick="location.assign('index.php')" />
        </form>
    </body>
</html>
