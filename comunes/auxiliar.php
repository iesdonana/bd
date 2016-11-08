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
        <h3>Error: <?= htmlentities($e) ?></h3><?php
    }
}

/**
 * Muestra un mensaje de saludo.
 * @param  string $nombre   El nombre de la persona
 * @param  string $telefono El teléfono de la persona
 */
function saludar(string $nombre, string $telefono)
{ ?>
    <h2>Hola, <?= htmlentities($nombre) ?>.
        Tu teléfono es <?= htmlentities($telefono) ?></h2><?php
}

function param_falta($param, $humano, &$error)
{
    if ($param === null) {
        $error[] = "Falta el campo $humano";
        return true;
    }
    return false;
}

function param_longmax($param, $humano, $max, &$error)
{
    if (mb_strlen($param) > $max) {
        $error[] = "El campo $humano no puede superar los $max caracteres";
        return true;
    }
    return false;
}

function param_vacio($param, $humano, &$error)
{
    if ($param === "") {
        $error[] = "El campo $humano no puede estar vacío";
        return true;
    }
    return false;
}

function comprobar_nombre($nombre, $humano, &$error)
{
    if (param_falta($nombre, $humano, $error) ||
        param_longmax($nombre, $humano, 50, $error) ||
        param_vacio($nombre, $humano, $error)) {
        return;
    }
}

function comprobar_telefono($telefono, $humano, &$error)
{
    if (param_falta($telefono, $humano, $error) ||
        param_longmax($telefono, $humano, 9, $error)) {
        return;
    }

    if (filter_var($telefono, FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 100000000,
            'max_range' => 999999999
        ]
    ]) === false) {
        $error[] = "El campo $humano debe ser un número de 9 dígitos";
    }
}

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

function comprobar_dept_no(&$dept_no, array &$error, $escenario = ESC_CONSULTA, $dept_no_viejo = null)
{

    $dept_no = trim($dept_no);

    if ($escenario === ESC_INSERTAR){
        if($dept_no === '') {
            $error[] = "El número es obligatorio";
        } elseif (!empty(buscar_por_dept_no(conectar_bd(), $dept_no))){
            $error[] = "El departamento " . htmlentities($dept_no) .
                       " ya existe";
        }
    } elseif ($escenario === ESC_MODIFICAR) {
        if($dept_no === '') {
            $error[] = "El número es obligatorio";
        } elseif ($dept_no !== $dept_no_viejo &&
                  !empty(buscar_por_dept_no(conectar_bd(), $dept_no))) {
                    $error[] = "El departamento " . htmlentities($dept_no) .
                               " ya existe";
        }
    }

    if ($dept_no !== "" && !ctype_digit($dept_no)) {
        $error[] = "El número de departamento debe ser un número";
    }

    if (mb_strlen($dept_no) > 2) {
        $error[] = "El número de departamento debe contener 1 ó 2 dígitos";
    }
}

function comprobar_dnombre(&$dnombre, array &$error, $escenario = ESC_CONSULTA)
{
    $dnombre = mb_strtoupper(trim($dnombre));

    if ($escenario === ESC_INSERTAR && $dnombre === '') {
        $error[] = "El nombre es obligatorio";
    }

    if (mb_strlen($dnombre) > 20) {
        $error[] = "El nombre del departamento no puede tener más de 20 caracteres";
    }
}

function comprobar_loc(&$loc, &$error, $escenario = ESC_CONSULTA)
{
    if ($escenario === ESC_INSERTAR && $loc === '') {
        $error[] = "El nombre de la localidad es obligatorio";
    }
    $loc = mb_strtoupper(trim($loc));
    if (mb_strlen($loc) > 100) {
        $error[] = "La localidad no puede tener más de 100 caracteres";
    }
}

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

function comprobar_si_vacio(array $result, array &$error)
{
    if (empty($result)) {
        $error[] = "No existe ese departamento";
    }
}

function comprobar_si_hay_uno(array $params, array &$error)
{
    foreach ($params as $p) {
        if ($p !== "") {
            return;
        }
    }
    $error[] = "Debe indicar al menos un criterio de búsqueda";
}

function conectar_bd(): PDO
{
    return new PDO(
        'pgsql:host=localhost;dbname=prueba',
        'alumno',
        '4904321weB'
    );
}

function buscar_por_dept_no(PDO $pdo, string $dept_no): array
{
    return buscar_por_dept_no_y_dnombre($pdo, $dept_no, "");
}

/**
 * Busca en la BD por dept_no y dnombre
 * @param  PDO    $pdo     Objeto para conexión a la BD
 * @param  string $dept_no Parámetro de búsqueda en la BD
 * @param  string $dnombre Parámetro de búsqueda en la BD
 * @return array           Resultado de la consulta
 */
function buscar_por_dept_no_y_dnombre(
    PDO $pdo,
    string $dept_no,
    string $dnombre
): array {
    $sql = "select * from depart where true";
    $params = [];
    if ($dept_no !== "") {
        $sql .= " and dept_no = :dept_no";
        $params[':dept_no'] = $dept_no;
    }
    if ($dnombre !== "") {
        $sql .= " and dnombre like :dnombre";
        $params[':dnombre'] = "%$dnombre%";
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

/**
 * Buscar en la tabla depart
 * @param  PDO    $pdo     Objeto para conexión con BD
 * @param  string $dept_no Parámetro para la BD
 * @param  string $dnombre Parámetro para la BD
 * @param  string $loc     Parámetro para la BD
 * @return array           Devuelve el resultado de la consulta
 */
function buscar_en_depart(PDO $pdo,
    string $dept_no,
    string $dnombre,
    string $localidad_id): array
{
    $sql = "select * from depart_v where true";
    $params = [];
    if ($dept_no !== "") {
        $sql .= " and dept_no = :dept_no";
        $params[':dept_no'] = $dept_no;
    }
    if ($dnombre !== "") {
        $sql .= " and dnombre like :dnombre";
        $params[':dnombre'] = "%$dnombre%";
    }
    if ($localidad_id !== "") {
        $sql .= " and localidad_id = :localidad_id";
        $params[':localidad_id'] = "$localidad_id";
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

function buscar_por_loc(PDO $pdo, $loc)
{
    $sql = "select * from localidades where true";
    $params = [];

    if ($loc !== "") {
        $sql .= " and loc = :loc";
        $params[':loc'] = "$loc";
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

function buscar_por_localidad_id(PDO $pdo, $localidad_id)
{
    $orden = $pdo->prepare("select * from localidades where id = :localidad_id");
    $orden->execute([':localidad_id' => $localidad_id]);
    return $orden->fetch();
}

function obtener_localidades(PDO $pdo): array
{
    $orden = $pdo->prepare("select * from localidades");
    $orden->execute();
    return $orden->fetchAll();
}

function lista_localidades(array $localidades, $localidad_id = null)
{ ?>
    <select id="localidad_id" name="localidad_id">
        <option value=""></option><?php
        foreach($localidades as $loc) { ?>
            <option value="<?= htmlentities($loc['id']) ?>"
            <?= ($loc['id'] == $localidad_id? "selected='selected'" : "") ?>>
                <?= htmlentities($loc['loc']) ?>
            </option><?php
        }
        ?>
    </select><br/><?php
}

/**
 * Dibuja la tabla con los resultados de la consulta
 * @param  array  $result Resultado de la consulta a la BD
 */
function dibujar_tabla(array $result)
{ ?>
    <table border="1">
        <thead>
            <th>Número</th>
            <th>Nombre</th>
            <th>Localidad</th>
            <th>Opciones</th>
        </thead>
        <tbody><?php
            foreach ($result as $fila) {?>
                <tr>
                    <td><?= htmlentities($fila['dept_no']) ?></td>
                    <td><?= htmlentities($fila['dnombre']) ?></td>
                    <td><?= htmlentities($fila['loc']) ?></td>
                    <td>
                        <a href="borrar.php?dept_no=<?= htmlentities($fila['dept_no']) ?>">Borrar</a>
                        <a href="modificar.php?dept_no=<?= htmlentities($fila['dept_no']) ?>">Modificar</a>
                    </td>
                </tr><?php
            } ?>
        </tbody>
    </table><?php
}

function dibujar_tabla_localidades(array $result)
{ ?>
    <table border="1">
        <thead>
            <th>Localidad</th>
            <th>Operaciones</th>
        </thead>
        <tbody><?php
            foreach ($result as $fila) {?>
                <tr>
                    <td><?= htmlentities($fila['loc']) ?></td>
                    <td>
                        <a href="borrar.php?localidad_id=<?= htmlentities($fila['id']) ?>">Borrar</a>
                        <a href="modificar.php?localidad_id=<?= htmlentities($fila['id']) ?>">Modificar</a>
                    </td>
                </tr><?php
            } ?>
        </tbody>
    </table><?php
}
