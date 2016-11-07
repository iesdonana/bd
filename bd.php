<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <style type="text/css">
            body { padding: 60px; }
        </style>
    </head>
    <body><?php
        require 'auxiliar.php';

        $dept_no = filter_input(INPUT_GET, "dept_no");
        $dnombre = filter_input(INPUT_GET, "dnombre");
        $loc     = filter_input(INPUT_GET, "loc"); ?>

        <div class="container">
            <div class="row">
                <div class="col-md-offset-2 col-md-8">
                    <div class="panel panel-info">
                        <div class="panel-heading">Consulta de departamentos</div>
                        <div class="panel-body">
                            <form action="" method="get">
                                <div class="form-group">
                                    <label for="dept_no">Número</label>
                                    <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="dnombre">Nombre</label>
                                    <input type="text" id="dnombre" name="dnombre"  value="<?= htmlentities($dnombre) ?>" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="loc">Localidad</label>
                                    <select class="form-control" name="loc">
                                        <option></option><?php
                                        $pdo = conectar_bd();
                                        $result = buscar_localidades($pdo);
                                        foreach ($result as $fila) {
                                                if ($loc === $fila['loc']) { ?>
                                                    <option selected><?= htmlentities($fila['loc']) ?> </option><?php
                                                } else { ?>
                                                    <option><?= htmlentities($fila['loc']) ?> </option><?php
                                                }
                                        } ?>
                                    </select>
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
        try {
            $error = [];
            comprobar_dept_no($dept_no, $error);
            comprobar_dnombre($dnombre, $error);
            comprobar_loc($loc, $error);
            comprobar_errores($error);
            //$pdo = conectar_bd();
            $result = buscar_por_dept_no_dnombre_loc($pdo, $dept_no, $dnombre, $loc);
            comprobar_si_vacio($result, $error);
            comprobar_errores($error);
            dibujar_tabla($result);
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
