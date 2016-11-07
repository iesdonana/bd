<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
    </head>
    <body><?php
        require 'auxiliar.php';


        $pdo = conectar_bd();
        $localidades = obtener_localidades($pdo);

        $dept_no = filter_input(INPUT_GET, "dept_no");
        $dnombre = filter_input(INPUT_GET, "dnombre");
        $localidad_id = filter_input(INPUT_GET, "localidad_id");?>

        <form action="" method="get">
            <h4>Departamentos</h4>
            <label for="dept_no">Número de departamento:</label>
            <input type="text" id="dept_no" name="dept_no"
                value="<?= htmlentities($dept_no) ?>"/><br/>
            <label for="dnombre">Nombre de departamento:</label>
            <input type="text" id="dnombre" name="dnombre"
                value="<?= htmlentities($dnombre) ?>" /><br/>
            <label for="localidad_id">Localidad del departamento:</label>
            <?php lista_localidades($localidades, $localidad_id) ?>
            <input type="submit" value="Buscar" />
            <input type="button" value="Insertar" onclick="location.assign('insertar.php')" />
        </form><?php

        try {
            $error = [];
            comprobar_dept_no($dept_no, $error);
            comprobar_dnombre($dnombre, $error);
            comprobar_localidad_id($localidad_id, $pdo, $error);
            comprobar_errores($error);
            $result = buscar_en_depart($pdo, $dept_no, $dnombre, $localidad_id);
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
