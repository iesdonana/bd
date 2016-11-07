<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Insertar un departamento</title>
    </head>
    <body><?php
        require "auxiliar.php";

        $pdo = conectar_bd();
        $localidades = obtener_localidades($pdo);

        $dept_no = filter_input(INPUT_POST, "dept_no");
        $dnombre = filter_input(INPUT_POST, "dnombre");
        $localidad_id = filter_input(INPUT_POST, "localidad_id");

    ?>
        <form action="" method="post">
            <h4>Insertar un departamento</h4>
            <label for="dept_no">Número de departamento*:</label>
            <input type="text" id="dept_no" name="dept_no"
                value="<?= htmlentities($dept_no) ?>"/><br/>
            <label for="dnombre">Nombre de departamento*:</label>
            <input type="text" id="dnombre" name="dnombre"
                value="<?= htmlentities($dnombre) ?>" /><br/>
            <label for="localidad_id">Localidad del departamento:</label>
            <?php lista_localidades($localidades) ?>
            <input type="submit" value="Insertar" />
            <input type="button" value="Cancelar" onclick="location.assign('bd.php')" />
        </form><?php

        try {
            $error = [];
            comprobar_existen([$dept_no, $dnombre, $localidad_id]);
            comprobar_dept_no($dept_no, $error, ESC_INSERTAR);
            comprobar_dnombre($dnombre, $error, ESC_INSERTAR);
            comprobar_localidad_id($localidad_id, $pdo, $error);
            comprobar_errores($error);
            $orden = $pdo->prepare("insert into depart (dept_no, dnombre, localidad_id)
                           values (:dept_no, :dnombre, :localidad_id)");
            $orden->execute([
                ':dept_no' => $dept_no,
                ':dnombre' => $dnombre,
                ':localidad_id' => $localidad_id
            ]);
            header("Location: bd.php");
        } catch (PDOException $e) { ?>
            <h3>Error de conexión a la base de datos</h3><?php
        } catch (Exception $e) {
            mostrar_errores($error);
        } ?>
    </body>
</html>
