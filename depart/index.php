<?php session_start() ?>
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
        menu(CTX_DEPART);

        try {
            $pdo = conectar_bd();
            $localidades = obtener_localidades($pdo);

            $dept_no = filter_input(INPUT_GET, "dept_no");
            $dnombre = filter_input(INPUT_GET, "dnombre");
            $localidad_id = filter_input(INPUT_GET, "localidad_id");?>

            <div class="container">
                <div class="row">
                    <div class="col-md-offset-2 col-md-8">
                        <div class="panel panel-info">
                            <div class="panel-heading">Consulta de localidades</div>
                            <div class="panel-body">
                                <form action="" method="get">
                                    <label for="dept_no">Número de departamento:</label>
                                    <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" class="form-control" /><br/>
                                    <label for="dnombre">Nombre de departamento:</label>
                                    <input type="text" id="dnombre" name="dnombre" value="<?= htmlentities($dnombre) ?>" class="form-control"/><br/>
                                    <label for="localidad_id">Localidad:</label><?php
                                    lista_localidades($localidades, $localidad_id)?><br/>
                                    <input type="submit" value="Buscar"  class="btn btn-default"/>
                                    <button type="reset" class="btn">Limpiar</button>
                                    <a href="insertar.php" role="button" class="btn btn-warning">Insertar</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div><?php
            $error = [];
            comprobar_dept_no($dept_no, $error);
            comprobar_dnombre($dnombre, $error);
            comprobar_localidad_id($localidad_id, $pdo, $error);
            comprobar_errores($error);
            $pdo = conectar_bd();
            $result = buscar_por_dept_no_y_dnombre_y_localidad_id(
                        $pdo, $dept_no, $dnombre, $localidad_id
                    );
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
