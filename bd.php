<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
    </head>
    <body><?php
        require 'auxiliar.php';

        try {
            $dept_no = filter_input(INPUT_POST, "dept_no");
            $dnombre = filter_input(INPUT_POST, "dnombre");
            $loc = filter_input(INPUT_POST, "loc");
            dibujar_formulario($dept_no,$dnombre,$loc);
            $error = [];
            comprobar_dept_no($dept_no, $error);
            comprobar_dnombre($dnombre, $error);
            comprobar_loc($loc, $error);
            comprobar_errores($error);
            $pdo = conectar_bd();
            $result = buscar_por_dept_no_dnombre_y_loc($pdo, $dept_no, $dnombre,$loc);
            comprobar_si_vacio($result, $error);
            comprobar_errores($error);
            dibujar_tabla($result);
        } catch (PDOException $e) { ?>
            <h3>Error de conexión a la base de datos</h3><?php
        } catch (Exception $e) {
            mostrar_errores($error);
        } ?>
    </body>
</html>
