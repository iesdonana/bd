<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Modificar un departamento</title>
    </head>
    <body><?php
        require '../comunes/auxiliar.php';

        $pdo = conectar_bd();
        $localidades = obtener_localidades($pdo);

        $dept_no = filter_input(INPUT_GET, "dept_no");

        if ($dept_no !== null){
            $result = buscar_por_dept_no($pdo, $dept_no);
            if (empty($result)){
                header("Location: index.php");
            }
            $result           = $result[0];
            $dnombre          = $result['dnombre'];
            $localidad_id     = $result['localidad_id'];
        } else {
            $dept_no_viejo = filter_input(INPUT_POST, "dept_no_viejo");
            $dept_no = filter_input(INPUT_POST, "dept_no");
            $dnombre = filter_input(INPUT_POST, "dnombre");
            $localidad_id = filter_input(INPUT_POST, "localidad_id");

            try{
                $error = [];
                comprobar_existen([$dept_no, $dnombre, $localidad_id]);
                comprobar_dept_no($dept_no, $error, ESC_MODIFICAR, $dept_no_viejo);
                comprobar_dnombre($dnombre, $error, ESC_INSERTAR);
                comprobar_localidad_id($localidad_id, $pdo, $error);
                comprobar_errores($error);
                $pdo = conectar_bd();
                $orden = $pdo->prepare("update depart
                                        set dept_no = :dept_no,
                                            dnombre = :dnombre,
                                            localidad_id = :localidad_id
                                      where dept_no = :dept_no_viejo");
                $orden->execute([
                        ':dept_no' => $dept_no,
                        ':dnombre' => $dnombre,
                        ':localidad_id' => $localidad_id,
                        ':dept_no_viejo' => $dept_no_viejo
                ]);

                header("Location: index.php");
            } catch (PDOException $e) { ?>
                <h3>Error de conexión a la base de datos</h3><?php
            } catch (Exception $e) {
                mostrar_errores($error);
            }
        }?>


        <form action="modificar.php" method="post">
            <input type="hidden" name="dept_no_viejo" value="<?= htmlentities($dept_no)?>">
            <label for="dept_no">Número de departamento: *</label>
            <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" /><br/>
            <label for="dnombre">Nombre de departamento: *</label>
            <input type="text" id="dnombre" name="dnombre" value="<?= htmlentities($dnombre) ?>" /><br/>
            <label for="loc">Localidad:</label><?php
            lista_localidades($localidades, $localidad_id)?><br/>
            <input type="submit" value="Modificar" />
            <a href="index.php" role="button">Cancelar</a>
        </form>
    </body>
</html>
