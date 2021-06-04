<?php
date_default_timezone_set("America/Mexico_City");
//define("DOMAIN", "148.243.10.117/diracmx");
define("DOMAIN", "201.149.54.149/TECNM");

//define("DOMAIN", "10.0.8.30/diracmx");
define("DIR_APP", "/");
define("SYSTEM_PATH", "http://" . DOMAIN . DIR_APP);

define("CONFIG_PATH", "http://" . DOMAIN . DIR_APP . "config" . DIR_APP);
define("CONTROLLERS_PATH", DOMAIN . DIR_APP . "controller" . DIR_APP);
define("MODELS_PATH", "http://" . DOMAIN . DIR_APP . "model" . DIR_APP);
define("UTILS_PATH", "http://" . DOMAIN . DIR_APP . "utils" . DIR_APP);


/* * ****************************************************** */

//MENSAJES DE ERROR        
define("SUCCESS_CODE", 0);
define("SUCCESS", "¡Operación Exitosa!");

define("SUCCESS_UU", "¡Usuario actualizado exitosamente!");

define("ERROR_CODE", 1);
define("ERROR", "Ha ocurrido un error, por favor intente más tarde.");

define("ERROR_CODE_L1", 11);
define("ERROR_L1", "El usuario no existe, o se encuentra inactivo");

define("ERROR_CODE_C1", 21);
define("ERROR_C1", "El registro solicitado no esta disponible");

define("WARNING_LOGIN_C", 2);

define("WARNING_UPLOAD_FILE_C", 2);
define("WARNING_UPLOAD_FILE_C2", 3);
define("WARNING_UPLOAD_FILE_C3", 4);
define("WARNING_UPLOAD_FILE_C4", 5);

define("WARNING_UPLOAD_FILE", "El archivo ya existe en el servidor.");
define("WARNING_UPLOAD_FILE2", "Tipo de archivo no permitido.");
define("WARNING_UPLOAD_FILE3", "Error al procesar el archivo.");
define("WARNING_UPLOAD_FILE4", "Error al cargar archivo");


