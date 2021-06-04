<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../model/TECNM.php';

$params = json_decode(file_get_contents('php://input'), true);

$evento = 0;
$data = "";
if (is_null($params)) {
    $evento = filter_input(INPUT_POST, "evento");
    $data = catchPOST();
} else {
    $evento = $params["evento"];
    $data = $params;
}

//var_dump($_REQUEST);
handler($evento, $data);

function handler($evento, $data) {
    $tecNM = TECNM::TECNMSngltn();
    switch ($evento) {
        case 1:
            $usrs = $tecNM->obtenerUsuarios("id = " . $data["id_usuario"]);
            echo json_encode($usrs);
            break;
        case 2:
            $edit_usr = $tecNM->editarUsuario($data);
            echo json_encode($edit_usr);
            break;
        case 3:
            $add_usr = $tecNM->agregarUsuario($data);
            echo json_encode($add_usr);
            break;
        case 4:
            $del_usr = $tecNM->eliminarUsuario($data["id_usuario"]);
            echo json_encode($del_usr);
            break;
        case 5:
//            var_dump($data);
            $login = $tecNM->login($data["usuario"], $data["contrasenia"]);
            echo json_encode($login);
            break;

        default:
            break;
    }
}

function catchPOST() {
    return $data = filter_input_array(INPUT_POST);
}
