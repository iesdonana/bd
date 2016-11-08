<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
    </head>
    <body><?php
        require '../comunes/auxiliar.php';

        try {
            $pdo = conectar_bd();
            $localidades = obtener_localidades($pdo);

            $dept_no = filter_input(INPUT_GET, "dept_no");
            $dnombre = filter_input(INPUT_GET, "dnombre");
            $localidad_id = filter_input(INPUT_GET, "localidad_id");?>

        <form action="" method="get">
            <label for="dept_no">Número de departamento:</label>
            <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" /><br/>
            <label for="dnombre">Nombre de departamento:</label>
            <input type="text" id="dnombre" name="dnombre" value="<?= htmlentities($dnombre) ?>" /><br/>
            <label for="localidad_id">Localidad:</label><?php
            lista_localidades($localidades, $localidad_id)?><br/>
            <input type="submit" value="Buscar" />
            <a href="insertar.php" role="button">Insertar</a>
        </form><?php
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
    </body>
</html>
