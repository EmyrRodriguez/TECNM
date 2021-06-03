<?php

/*
 * usrController.js
 * @author FIGG - DIRAC
 * @description Archivo controlador para las operaciones con los usuarios.
 */

error_reporting(-1);
require_once '../model/Catalogo.php';
$evento = filter_input(INPUT_POST, 'evento', FILTER_SANITIZE_STRING);
//$evento = 1;
handler($evento);

function handler($evento) {
    $data = catchPOST();
    $cat = Catalogo::catSngltn($data['table']);
    switch ($evento) {
        case 1://GetCat
            $list = $cat->getCat();
            echo json_encode($list);
            break;
        case 2://add & update
            $data = catchPOST();
            $cat->setId($data["id"]);
            $cat->setClave($data["clave"]);
            $cat->setNombre($data["nombre"]);
            $cat->setDescripcion($data["descripcion"]);
            $cat->setEstatus($data["estatus"]);
            $add = $cat->replaceCat($cat, $data["opcion"]);
            echo json_encode($add);
            break;
        case 3://getCatById
            session_start();
            $data = catchPOST();
            $cat->setId($data["id"]);
            $catalogue = $cat->getCatById($cat);
//            var_dump($catalogue);
            echo json_encode($catalogue);
            break;
        case 4://get Profiles SGI
            session_start();
            $data = catchPOST();
            $profiles = $cat->getSGIProfiles($data["table"]);
//            var_dump($catalogue);
            echo json_encode($profiles);
            break;

        case 5:// Delete cat record
            $data = catchPOST();
            $delete = $cat->deleteCatRecord($data["table"], $data["id"]);
//            var_dump($catalogue);
            echo json_encode($delete);
            break;
        case 6://Get Puestos
            $list = $cat->getPuestos();
            echo json_encode($list);
            break;
         case 7://add & update
            $data = catchPOST();
            $cat->setId($data["id"]);
            $cat->setClave($data["clave"]);
            $cat->setNombre($data["nombre"]);
            $cat->setDescripcion($data["descripcion"]);
            $cat->setEvaluacion($data["evaluacion"]);
            $cat->setEstatus($data["estatus"]);
            $add = $cat->replacePuesto($cat, $data["opcion"]);
            echo json_encode($add);
            break;
    }
}

function catchPOST() {
    return $data = filter_input_array(INPUT_POST);
}
