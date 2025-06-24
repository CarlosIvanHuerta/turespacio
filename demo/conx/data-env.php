<?php
function cargarEntornoDesdeEnv($rutaArchivo = __DIR__ . '/.env') {
    if (!file_exists($rutaArchivo)) {
        return;
    }

    $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#') continue;

        list($nombre, $valor) = explode('=', $linea, 2);
        $nombre = trim($nombre);
        $valor = trim($valor);

        if (!array_key_exists($nombre, $_ENV)) {
            putenv("$nombre=$valor");
            $_ENV[$nombre] = $valor;
        }
    }
}
