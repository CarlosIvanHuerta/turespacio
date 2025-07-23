<?php

namespace utils;

class ayudasBasicas
{
    public function generateUrlSlug($string) {
        // Convertir a minúsculas
        $string = mb_strtolower($string, 'UTF-8');

        // Reemplazar acentos y caracteres especiales
        $unwantedChars = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ñ' => 'n',
            'À' => 'a', 'È' => 'e', 'Ì' => 'i', 'Ò' => 'o', 'Ù' => 'u',
            'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u'
        ];

        $string = strtr($string, $unwantedChars);

        // Reemplazar cualquier carácter no alfanumérico (excepto espacios) por nada
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);

        // Reemplazar espacios y guiones múltiples con un solo guion
        $string = preg_replace('/[\s-]+/', '-', $string);

        // Eliminar guiones al inicio o al final
        $string = trim($string, '-');

        return $string;
    }

}