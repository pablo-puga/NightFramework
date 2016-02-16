<?php

namespace Night\Component\FileParser;


class JSONParser implements FileParser
{
    const FILE_EXTENSION = 'json';

    public function parseFile($pathToFile)
    {
        $fileContents = file_get_contents($pathToFile);
        $parsedFile   = json_decode($fileContents, true);

        /*switch(json_last_error()) {
            case JSON_ERROR_NONE:
                echo ' - Sin errores';
                break;
            case JSON_ERROR_DEPTH:
                echo ' - Excedido tamaño máximo de la pila';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                echo ' - Desbordamiento de buffer o los modos no coinciden';
                break;
            case JSON_ERROR_CTRL_CHAR:
                echo ' - Encontrado carácter de control no esperado';
                break;
            case JSON_ERROR_SYNTAX:
                echo ' - Error de sintaxis, JSON mal formado';
                break;
            case JSON_ERROR_UTF8:
                echo ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
                break;
            default:
                echo ' - Error desconocido';
                break;
        }*/

        return $parsedFile;
    }
}

