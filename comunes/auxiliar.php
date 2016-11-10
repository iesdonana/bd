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
/**
 * Comprueba si el dept_no es valido
 * @param  string $dept_no Número de departamento
 * @param  array  $error   Array de errores
 */
function comprobar_dept_no(&$dept_no, array &$error, $escenario = ESC_CONSULTA, $dept_no_viejo = null)
{
    $dept_no = trim($dept_no);
    if ($escenario === ESC_INSERTAR) {
        if ($dept_no === "") {
            $error[] = "El número es obligatorio";
        } elseif (!empty(buscar_por_dept_no(conectar_bd(),$dept_no))) {
            $error[] = "El departamento " . htmlentities($dept_no) .
                        " ya existe";
        }
    } elseif ($escenario === ESC_MODIFICAR) {
        if ($dept_no === "") {
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
/**
 * Comprueba si el dnombre es valido
 * @param  string $dnombre Nombre del departamento
 * @param  array  $error   Array de errores
 */
function comprobar_dnombre(&$dnombre, array &$error, $escenario = ESC_CONSULTA)
{
    $dnombre = strtoupper(trim($dnombre));
    if ($escenario === ESC_INSERTAR && $dnombre === "") {
        $error[] = "El nombre es obligatorio";
    }
    if (mb_strlen($dnombre) > 20) {
        $error[] = "El nombre del departamento no puede tener más de 20 caracteres";
    }
}
/**
 * Comprueba si el loc es valido
 * @param  string $loc   Localidad del departamento
 * @param  array  $error Array de errores
 */
function comprobar_loc(&$loc, array $error, $escenario = ESC_CONSULTA)
{
    $loc = strtoupper(trim($loc));

    if ($escenario === ESC_INSERTAR && $loc === "") {
        $error[] = "la localidad es obligatoria";
    }

    if (mb_strlen($loc) > 100) {
        $error[]  = "La localidad no puede tener mas de 50 caracteres.";
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
            $error[] = "No existe la localidad";
        }
    } else {
        $localidad_id = null;
    }
}
/**
 * Comprueba si alguno de los elementos del array de resultados de la busqueda
 * esta vacio
 * @param  array  $result Array de resultados de la busqueda
 * @param  array  $error  Array de errores
 */
function comprobar_si_vacio(array $result, array &$error)
{
    if (empty($result)) {
        $error[] = "No existe ese departamento";
    }
}
/**
 * Comprueba si alguno de los elementos del array existe
 * @param  array  $params Array con los elementos a buscar
 * @param  array  $error  Array de errores
 */
function comprobar_si_hay_uno(array $params, array &$error)
{
    foreach ($params as $p) {
        if ($p !== "") {
            return;
        }
    }
    $error[] = "Debe indicar al menos un criterio de búsqueda";
}
/**
 * Crea una conexión a la base de datos
 * @return PDO Conexión a la base de datos
 */
function conectar_bd(): PDO
{
    return new PDO(
        'pgsql:host=localhost;dbname=prueba',
        'christian',
        'christian'
    );
}
/**
 * Realiza la busqueda unicamente por dept_no
 * @param  PDO    $pdo     Conexion a la base de datos
 * @param  string $dept_no Númerode departamento
 * @return array           Array con los datos obtenidos de la busqueda
 */
function buscar_por_dept_no(PDO $pdo, string $dept_no): array
{
    return buscar_por_dept_no_dnombre_y_localidad_id($pdo, $dept_no, "", "");
}
/**
 * Realiza la busqueda en la base de datos por dept_no, dnombre y loc
 * @param  PDO    $pdo     Conexion a la base de datos
 * @param  string $dept_no Número de departamento
 * @param  string $dnombre Nombre de departamento
 * @param  string $loc     Localidad del departamento
 * @return array           Array con los datos obtenidos de la busqueda
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

function buscar_por_loc(PDO $pdo, string $loc = null): array
{
    $sql = "select * from localidades where true";
    $params = [];

    if ($loc !== "" && $loc !== null) {
        $sql .= " and loc ilike :loc";
        $params[':loc'] = "%$loc%";
    }
    $orden = $pdo->prepare($sql);
    $orden->execute($params);
    return $orden->fetchAll();
}

function buscar_por_localidad_id(PDO $pdo, $localidad_id): array
{
    $orden = $pdo->prepare("select * from localidades where id = :localidad_id");
    $orden->execute([':localidad_id' => $localidad_id]);
    return $orden->fetch();
}

/**
 * Dibuja la tabla con los datos solicitados
 * @param  array  $result Array con el resultado de la consulta
 */
function dibujar_tabla(array $result)
{ ?>
    <table border="1">
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
                        <a href="borrar.php?dept_no=<?= $dept_no ?>" role="button">Borrar</a>
                        <a href="modificar.php?dept_no=<?= $dept_no ?>" role="button">Modificar</a>
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
            foreach ($result as $fila) {
                $id = htmlentities($fila['id']); ?>
                <tr>
                    <td><?= htmlentities($fila['loc']) ?></td>
                    <td>
                        <a href="borrar.php?localidad_id=<?= $id ?>" role="button">Borrar</a>
                        <a href="modificar.php?localidad_id=<?= $id ?>" role="button">Modificar</a>
                    </td>
                </tr><?php
            } ?>
        </tbody>
    </table><?php
}
/**
 * Dibuja el formlario de consulta
 * @param  string $dept_no Número del departamento
 * @param  string $dnombre Nombre del departamento
 * @param  string $loc     Localidad del departamento
 */
function dibujar_formulario($dept_no,$dnombre,$loc)
{ ?>
    <form action="" method="get">
        <label for="dept_no">Número de departamento:</label>
        <input type="text" id="dept_no" name="dept_no" value="<?= htmlentities($dept_no) ?>" /><br/>
        <label for="dnombre">Nombre de departamento:</label>
        <input type="text" id="dnombre" name="dnombre" value="<?= htmlentities($dnombre) ?>" /><br/>
        <label for="loc">Localidad del departamento:</label>
        <input type="text" id="loc" name="loc" value="<?= htmlentities($loc) ?>" /><br/>
        <input type="submit" value="Buscar" />
        <input type="reset" value="Limpiar" />
        <a href="insertar.php">Insertar</a>
    </form><?php
}

function obtener_localidades(PDO $pdo): array
{
    $orden = $pdo->prepare("select * from localidades");
    $orden->execute();
    return $orden->fetchAll();
}

function lista_localidades(array $localidades, $localidad_id = null)
{?>
    <select name="localidad_id" id="localidad_id">
        <option value=""></option><?php
        foreach ($localidades as $loc) { ?>
            <option value="<?= htmlentities($loc['id'])?>" <?=
                ($loc['id'] == $localidad_id) ? "selected" : "" ?>>
                <?= htmlentities($loc['loc']) ?>
            </option><?php
        }
     ?>
 </select><?php
}

function menu($contexto = null)
{ ?>
    <h1><a href="/bd/index.php">Menu principal</a></h1>
    <ul>
        <li><a href="/bd/depart">Departamentos</a></li>
        <li><a href="/bd/localidades">Localidades</a></li>
    </ul><?php
        if (isset($_SESSION['login'])) { ?>
            <p><?= htmlentities($_SESSION['login']) ?></p>
            <a href="/bd/comunes/logout.php">Logout</a><?php
        } else { ?>
            <a href="/bd/comunes/login.php">Login</a><?php
        } ?>
    <hr /><?php
}

function comprobar_credenciales(PDO $pdo, $login, $pass, array &$error)
{
    $orden = $pdo->prepare("select * from usuarios where nombre = :login");
    $orden->execute([':login' => $login]);
    $result = $orden->fetch();

    if (empty($result) || !password_verify($pass, $result['pass'])) {
        $error[] = "Credenciales incorrectas";
    }
}

function comprobar_logueado()
{
    if (!usuario_logueado()) {
        header("Location: /bd/comunes/login.php");
    }
}

function usuario_logueado(): bool
{
    return isset($_SESSION['login']);
}
