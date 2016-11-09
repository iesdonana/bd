<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    </head>
    <body><?php
        require '../comunes/auxiliar.php';

        comprobar_logueado();
        menu(CTX_LOCALIDADES);

        try {
            $pdo = conectar_bd();
            $localidades = obtener_localidades($pdo);


            $loc = filter_input(INPUT_GET, "loc");?>

            <div class="container">
                        <div class="row">
                            <div class="col-md-offset-2 col-md-8">
                                <div class="panel panel-info">
                                    <div class="panel-heading">Consulta de localidades</div>
                                    <div class="panel-body">
                                        <form action="" method="get">
                                            <div class="form-group">
                                                <label for="loc">Localidad</label>
                                                <input type="text" id="loc" name="loc" value="<?= htmlentities($loc) ?>" /><br/>
                                            </div>
                                            <button type="submit" class="btn btn-default">Buscar</button>
                                            <button type="reset" class="btn">Limpiar</button>
                                            <a href="insertar.php" class="btn btn-warning" role="button">Insertar</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><?php
            $error = [];
            comprobar_loc($localidad_id, $error);
            comprobar_errores($error);
            $pdo = conectar_bd();
            $result = buscar_por_loc(
                        $pdo, $loc
                    );
            comprobar_si_vacio($result, $error);
            comprobar_errores($error);
            dibujar_tabla_localidades($result);
        } catch (PDOException $e) { ?>
            <h3>Error de conexión a la base de datos</h3><?php
        } catch (Exception $e) {
            mostrar_errores($error);
        } ?>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
