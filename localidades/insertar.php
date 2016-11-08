<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Insertar un departamento</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <style type="text/css">
            body { padding: 60px; }
        </style>
    </head>
    <body><?php
        require '../comunes/auxiliar.php';

        $pdo = conectar_bd();

        $loc = filter_input(INPUT_POST, "loc");

        try {
            $error = [];
            comprobar_existen([$loc]);
            comprobar_loc($loc, $error, ESC_INSERTAR);
            comprobar_errores($error);
            $orden = $pdo->prepare("insert into localidades (loc)
                                    values (:loc)");
            $orden->execute([':loc' => $loc]);
            header("Location: index.php");
        } catch (PDOException $e) { ?>
            <h3>Error de conexión a la base de datos</h3><?php
        } catch (Exception $e) {
            mostrar_errores($error);
        } ?>

        <div class="container">
            <div class="row">
                <div class="col-md-offset-2 col-md-8">
                    <div class="panel panel-info">
                        <div class="panel-heading">Insertar una localidad</div>
                        <div class="panel-body">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="dnombre">Nombre *</label>
                                    <input type="text" id="loc" name="loc"  value="<?= htmlentities($loc) ?>" class="form-control" />
                                </div>
                                <button type="submit" class="btn btn-default">Insertar</button>
                                <button type="reset" class="btn">Limpiar</button>
                                <a href="index.php" class="btn btn-warning" role="button">Cancelar</a>
                            </form>
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
