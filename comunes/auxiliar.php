<?php

define("ESC_CONSULTA", 0);
define("ESC_INSERTAR", 1);
define("ESC_MODIFICAR", 2);

function exception_error_handler($severidad, $mensaje, $fichero, $línea) {
    if (!(error_reporting() & $severidad)) {
        // Este código de error no está incluido en error_reporting
        return;
    }
    throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
}
set_error_handler("exception_error_handler");

/**
 * Muestra por la salida los errores del argumento.
 * @param  array $err El array que contiene los errores
 */
function mostrar_errores($err)
{
    foreach ($err as $e) { ?>
        <div class="alert alert-danger" role="alert">
            Error: <?= htmlentities($e) ?>
        </div><?php
    }
}

/**
 * Comprueba si hay errores
 * @param  array $error El array que contiene los errores
 */
function comprobar_errores($error)
{
    if (!empty($error)) {
        throw new Exception;
    }
}

function comprobar_existen($params)
{
    foreach ($params as $p) {
        if ($p !== null) {
            return true;
        }
    }
    throw new Exception;
}

/**
 * Comprueba que los datos del número de departamento son correctos
 * @param  string $dept_no El numero del departamento
 * @param  array  $error   El array que contiene los errores
 */
function comprobar_dept_no(&$dept_no, array &$error, $escenario = ESC_CONSULTA, $dept_no_viejo = null)
{
    if ($dept_no === null) {
        throw new Exception;
    }

    $dept_no = trim($dept_no);

    if ($escenario === ESC_INSERTAR) {
        if ($dept_no === "") {
            $error[] = "El número es obligatorio";
        } elseif (!empty(buscar_por_dept_no(conectar_bd(), $dept_no))) {
            $error[] = "El departamento " . htmlentities($dept_no) . " ya existe.";
        }
    } elseif ($escenario === ESC_MODIFICAR) {
        if ($dept_no === "") {
            $error[] = "El número es obligatorio";
        } elseif ($dept_no !== $dept_no_viejo && !empty(buscar_por_dept_no(conectar_bd(), $dept_no))) {
            $error[] = "El departamento " . htmlentities($dept_no) . " ya existe.";
        }
    }

    if ($dept_no !== "" && !ctype_digit($dept_no)) {
        $error[] = "El número de departamento debe ser un número";
    }

    if (mb_strlen($dept_no) > 2) {
        $error[] = "El número de departamento debe contener 1 ó 2 dígitos";
    }
}

/**
 * Comprueba que los datos del nombre de departamento son correctos
 * @param  string $dnombre El nombre del departamento
 * @param  array  $error   El array que contiene los errores
 */
function comprobar_dnombre(&$dnombre, array &$error, $escenario = ESC_CONSULTA)
{
    if ($dnombre === null) {
        throw new Exception;
    }

    $dnombre = strtoupper(trim($dnombre));

    if ($escenario === ESC_INSERTAR && $dnombre === "") {
        $error[] = "El nombre es obligatorio";
    }

    if (mb_strlen($dnombre) > 20) {
        $error[] = "El nombre del departamento no puede tener más de 20 caracteres";
    }
}

/**
 * Comprueba que los datos de la localidad de departamento son correctos
 * @param  string $loc     La localidad del departamento
 * @param  array  $error   El array que contiene los errores
 */
function comprobar_localidad_id(&$localidad_id, PDO $pdo, array &$error)
{
    $localidad_id = trim($localidad_id);

    if ($localidad_id !== "") {
        $orden = $pdo->prepare("select * from localidades where id = :localidad_id");
        $orden->execute([':localidad_id' => $localidad_id]);
        $result = $orden->fetchAll();

        if (empty($result)) {
            $error[] = "No existe la localidad indicada";
        }
    } else {
        $localidad_id = null;
    }
}

/**
 * Comprueba si existe el departamento
 * @param  array  $result El array de los resultados de la búsqueda
 * @param  array  $error  El array que contiene los errores
 */
function comprobar_si_vacio(array $result, array &$error)
{
    if (empty($result)) {
        $error[] = "No existe ese departamento";
    }
}

/**
 * Conecta con la base de datos
 * @return PDO La conexión con la base de datos
 */
function conectar_bd(): PDO
{
    return new PDO(
        'pgsql:host=localhost;dbname=prueba',
        'joseluis',
        'joseluis'
    );
}

function buscar_por_dept_no(PDO $pdo, string $dept_no): array
{
    return buscar_por_dept_no_dnombre_y_localidad_id($pdo, $dept_no, "", "");
}

/**
 * Realiza la busqueda en la base de datos con los datos recibidos
 * @param  PDO    $pdo     La conexión con la base de datos
 * @param  string $dept_no El número del departamento
 * @param  string $dnombre El nombre del departamento
 * @param  string $loc     La localidad del departamento
 * @return array           El array con los datos encontrados en la búsqueda
 */
function buscar_por_dept_no_dnombre_y_localidad_id(
    PDO $pdo,
    string $dept_no,
    string $dnombre,
    string $localidad_id = null
): array {
    $sql = "select * from depart_v where true";
    $params = [];
    if ($dept_no !== "") {
        $sql .= " and dept_no = :dept_no";
        $params[':dept_no'] = $dept_no;
    }
    if ($dnombre !== "") {
        $sql .= " and dnombre ilike :dnombre";
        $params[':dnombre'] = "%$dnombre%";
    }
    if ($localidad_id !== "" && $localidad_id !== null) {
        $sql .= " and localidad_id = :localidad_id";
        $params[':localidad_id'] = $localidad_id;
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

/**
 * Muestra por pantalla el resultado de la tabla
 * @param  array  $result El array de los resultados de la búsqueda
 */
function dibujar_tabla(array $result)
{ ?>
    <table class="table">
        <thead>
            <th>Número</th>
            <th>Nombre</th>
            <th>Localidad</th>
            <th>Operaciones</th>
        </thead>
        <tbody><?php
            foreach ($result as $fila) {
                $dept_no = htmlentities($fila['dept_no']) ?>
                <tr>
                    <td><?= $dept_no ?></td>
                    <td><?= htmlentities($fila['dnombre']) ?></td>
                    <td><?= htmlentities($fila['loc']) ?></td>
                    <td><a href="borrar.php?dept_no=<?= $dept_no ?>" class="btn btn-danger btn-xs" role="button">Borrar</a>
                        <a href="modificar.php?dept_no=<?= $dept_no ?>" class="btn btn-info btn-xs" role="button">Modificar</a>
                        <a href="ver.php" class="btn btn-warning btn-xs" role="button">Ver</a></td>
                </tr><?php
            } ?>
        </tbody>
    </table><?php
}

function obtener_localidades(PDO $pdo): array
{
    $orden = $pdo->prepare("select * from localidades");
    $orden->execute();
    return $orden->fetchAll();
}

function lista_localidades(array $localidades, $localidad_id = null)
{ ?>
    <select name="localidad_id" id="localidad_id" class="form-control">
        <option value=""></option> <?php
        foreach ($localidades as $loc) { ?>
            <option value="<?= htmlentities($loc['id']) ?>" <?=
            ($loc['id'] == $localidad_id) ? "selected" : "" ?>
            > <?= htmlentities($loc['loc']) ?> </option><?php
        } ?>
    </select> <?php
}
