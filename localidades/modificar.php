<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Modificar un departamento</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    </head>
    <body><?php
        require "../comunes/auxiliar.php";

        $pdo = conectar_bd();
        $localidad_id = filter_input(INPUT_GET, "localidad_id");

        if ($localidad_id !== null){ //Venimos del index.php
            $result = buscar_por_localidad_id($pdo, $localidad_id);
            if (empty($result)){
                header("Location: index.php");
            }
            $loc = $result['loc'];
        } else {
            $localidad_id = filter_input(INPUT_POST, "localidad_id");
            $loc = filter_input(INPUT_POST, "loc");
            try{
                $error = [];
                comprobar_existen([$loc, $localidad_id]);
                comprobar_loc($loc, $error, ESC_INSERTAR);
                comprobar_localidad_id($localidad_id, $pdo, $error);
                comprobar_errores($error);
                $pdo = conectar_bd();
                $orden = $pdo->prepare("update localidades
                                        set loc = :loc
                                        where id = :localidad_id");
                $orden->execute([
                        ':loc' => $loc,
                        ':localidad_id' => $localidad_id,
                ]);

                header("Location: index.php");
            } catch (PDOException $e) { ?>
                <h3>Error de conexión a la base de datos</h3><?php
            } catch (Exception $e) {
                mostrar_errores($error);
            }
        }?>


        <form action="modificar.php" method="post">
            <input type="hidden" name="localidad_id" value="<?= htmlentities($localidad_id) ?>">
            <label for="loc">Localidad: *</label>
            <input type="text" id="loc" name="loc" value="<?= htmlentities($loc) ?>" /><br/>
            <input type="submit" value="Modificar" />
            <a href="index.php" role="button">Cancelar</a>
        </form>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
