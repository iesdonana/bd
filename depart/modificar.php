<?php session_start() ?>
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

        comprobar_logueado();
        menu(CTX_DEPART);

        $pdo = conectar_bd();
        $localidades = obtener_localidades($pdo);

        $dept_no = filter_input(INPUT_GET, "dept_no");

        if ($dept_no !== null) {
            $result = buscar_por_dept_no($pdo, $dept_no);
            if (empty($result)) {
                header("Location: index.php");
            }
            $result  = $result[0];
            $dnombre = $result['dnombre'];
            $localidad_id = $result['localidad_id'];
        } else {
            $dept_no_viejo = filter_input(INPUT_POST, "dept_no_viejo");
            $dept_no       = filter_input(INPUT_POST, "dept_no");
            $dnombre       = filter_input(INPUT_POST, "dnombre");
            $localidad_id  = filter_input(INPUT_POST, "localidad_id");

            try {
                $error = [];
                comprobar_existen([$dept_no_viejo, $dept_no, $dnombre, $localidad_id]);
                comprobar_dept_no($dept_no, $error, ESC_MODIFICAR, $dept_no_viejo);
                comprobar_dnombre($dnombre, $error, ESC_INSERTAR);
                comprobar_localidad_id($localidad_id, $pdo, $error);
                comprobar_errores($error);
                $orden = $pdo->prepare("update depart
                                           set dept_no      = :dept_no,
                                               dnombre      = :dnombre,
                                               localidad_id = :localidad_id
                                         where dept_no = :dept_no_viejo");
                $orden->execute([
                    ':dept_no'       => $dept_no,
                    ':dnombre'       => $dnombre,
                    ':localidad_id'  => $localidad_id,
                    ':dept_no_viejo' => $dept_no_viejo
                ]);
                header("Location: index.php");
            } catch (PDOException $e) { ?>
                <h3>Error de conexión a la base de datos</h3><?php
            } catch (Exception $e) {
                mostrar_errores($error);
            }
        } ?>

        <div class="container">
            <div class="row">
                <div class="col-md-offset-2 col-md-8">
                    <div class="panel panel-info">
                        <div class="panel-heading">Modificar un departamento</div>
                        <div class="panel-body">
                            <form action="modificar.php" method="post">
                                <input type="hidden" name="dept_no_viejo" value="<?= htmlentities($dept_no) ?>" />
                                <div class="form-group">
                                    <label for="dept_no">Número *</label>
                                    <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="dnombre">Nombre *</label>
                                    <input type="text" id="dnombre" name="dnombre"  value="<?= htmlentities($dnombre) ?>" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="loc">Localidad</label>
                                    <?php lista_localidades($localidades, $localidad_id) ?>
                                </div>
                                <button type="submit" class="btn btn-default">Modificar</button>
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
