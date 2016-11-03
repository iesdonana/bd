<?php

define("ESC_CONSULTA", 0);
define("ESC_INSERTAR", 1);

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

/**
 * Comprueba si el array que se le pasa por parametro no esta vacio
 * @param  array $error el array de errores
 * @throws Exception
 */
function comprobar_errores($error)
{
    if (!empty($error)) {
        throw new Exception;
    }
}

/**
 * Comprueba que hay algun elemento del array $params que es distinto de null
 * @param  array $params array con elementos
 * @throws Exception
 */
function comprobar_existen($params)
{
    foreach ($params as $p) {
        if ($p !== null) {
            return;
        }
    }
    throw new Exception;
}

/**
 * Comprueba que el numero del departamento este correcto para su buen uso
 * @param  string $dept_no numero del departamento
 * @param  array  $error   array de errores
 */
function comprobar_dept_no(&$dept_no, array &$error, $escenario = ESC_CONSULTA)
{
    $dept_no = trim($dept_no);

    if ($escenario === ESC_INSERTAR) {
        if ($dept_no === "") {
            $error[] = "El número es obligatorio";
        } elseif (!empty(buscar_por_dept_no(conectar_bd(), $dept_no))) {
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

/**
 * Comprueba que el nombre del departamento este correcto para su buen uso
 * @param  string $dnombre nombre del departamento
 * @param  array  $error   array de errores
 */
function comprobar_dnombre(&$dnombre, array &$error, $escenario = ESC_CONSULTA)
{
    $dnombre = trim($dnombre);
    $dnombre = mb_strtoupper($dnombre);

    if ($escenario === ESC_INSERTAR && $dnombre === "") {
        $error[] = "El nombre es obligatorio";
    }

    if (mb_strlen($dnombre) > 20) {
        $error[] = "El nombre del departamento no puede tener más de 20 caracteres";
    }
}

/**
 * Comprueba que la localizacion del departamento este correcta para su buen uso
 * @param  string $loc   localización del departamento
 * @param  array  $error array de errores
 */
function comprobar_loc(&$loc, array &$error)
{
    $loc = trim($loc);
    $loc = mb_strtoupper($loc);

    if (mb_strlen($loc) > 50) {
        $error[] = "La localidad del departamento no puede tener más de 50 caracteres";
    }
}

/**
 * Se comprueba si el array que se le pasa esta vacio, si es asi rellena
 * el array error
 * @param  array  $result array a comprobar si esta vacio
 * @param  array  $error  array de errores
 */
function comprobar_si_vacio(array $result, array &$error)
{
    if (empty($result)) {
        $error[] = "No existe ese departamento";
    }
}

/**
 * Comprueba si al menos uno de los parametros de $params es distinto de cadena
 * vacia, si no es asi añade un error al array $error
 * @param  array  $params array a comprobar
 * @param  array  $error  array de errores
 */
function comprobar_si_hay_uno(array $params, array &$error)
{
    foreach ($params as $p) {
        if ($p !== null) {
            return;
        }
    }
    $error[] = "Debe indicar al menos un criterio de búsqueda";
}

/**
 * Conecta con la base de datos
 * @return PDO devuelve un objeto PDO
 */
function conectar_bd(): PDO
{
    return new PDO(
        'pgsql:host=localhost;dbname=prueba',
        'recetas',
        'recetas'
    );
}

/**
 * Busca en la tabla depart solo por dept_no
 * @param  PDO    $pdo     el objeto PDO con la conexion a la base de datos
 * @param  string $dept_no el numero de departamento a buscar
 * @return array           devuelve el array con los resultados
 */
function buscar_por_dept_no(PDO $pdo, string $dept_no): array
{
    return buscar_en_depart($pdo, $dept_no, "", "");
}

/**
 * Busca en la tabla depart por sus tres campos (dept_no, dnombre y loc)
 * @param  PDO    $pdo     el objeto PDO con la conexion a la base de datos
 * @param  string $dept_no el numero de departamento a buscar
 * @param  string $dnombre el nombre del departamento a buscar
 * @param  string $loc     la localización del departamento a buscar
 * @return array           devuelve el array con los resultados
 */
function buscar_en_depart(
    PDO $pdo,
    string $dept_no,
    string $dnombre,
    string $loc
): array {
    $sql = "select * from depart where true";
    $params = [];
    if ($dept_no !== "") {
        $sql .= " and dept_no = :dept_no";
        $params[':dept_no'] = $dept_no;
    }
    if ($dnombre !== "") {
        $sql .= " and dnombre ilike :dnombre";
        $params[':dnombre'] = "%$dnombre%";
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

function buscar_por_dept_no_dnombre_loc(
    PDO $pdo,
    string $dept_no,
    string $dnombre,
    string $loc
): array {
    $sql = "select * from depart where true";
    $params = [];
    if ($dept_no !== "") {
        $sql .= " and dept_no = :dept_no";
        $params[':dept_no'] = $dept_no;
    }
    if ($dnombre !== "") {
        $sql .= " and dnombre ilike :dnombre";
        $params[':dnombre'] = "%$dnombre%";
    }
    if ($loc !== "") {
        $sql .= " and loc like :loc";
        $params[':loc'] = "%$loc%";
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

/**
 * Dibuja una tabla con los resultados de la select en la tabla depart
 * @param  array  $result un array con los resultados de la select
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
                $dept_no = htmlentities($fila['dept_no']); ?>
                <tr>
                    <td><?= $dept_no ?></td>
                    <td><?= htmlentities($fila['dnombre']) ?></td>
                    <td><?= htmlentities($fila['loc']) ?></td>
                    <td>
                        <a href="borrar.php?dept_no=<?= $dept_no ?>" class="btn btn-danger btn-xs" role="button">Borrar</a>
                        <a href="modificar.php" class="btn btn-info btn-xs" role="button">Modificar</a>
                        <a href="ver.php" class="btn btn-warning btn-xs" role="button">Ver</a>
                    </td>
                </tr><?php
            } ?>
        </tbody>
    </table><?php
}
