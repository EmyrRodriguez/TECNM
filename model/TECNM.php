<?php

require_once realpath(dirname(__FILE__) . '/../config/ConnectionDB.php');
require_once realpath(dirname(__FILE__) . '/../config/properties.php');

class TECNM implements JsonSerializable {

    public $db;
    public $response;
    private static $instance;

    function __construct() {
        $this->db = ConnectionDB::connectSngtn()->DBConnect();
        $this->response = array();
//        $this->table = "cat_encuestas";
    }

    // Método singleton
    public static function TECNMSngltn() {
        if (!isset(self::$instance)) {
            $TECNM = __CLASS__;
            self::$instance = new $TECNM();
        }
        return self::$instance;
    }

    // Evita que el objeto se pueda clonar
    public function __clone() {
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
    }

    /*     * ************************************************************* */

    function login($usuario, $contrasenia) {
        try {
            $query = "SELECT * FROM tecnm_usuarios WHERE usuario = ? AND contrasenia = ? ";

            $oUsr = $this->db->prepare($query);
            $oUsr->execute(array($usuario, md5($contrasenia)));
            $data = array();
            if ($oUsr->rowCount() == 1) {
//                $this->response["data"] = $data;
                //Auqi se tienen que guardar las variabkles de sesion
                $this->response["errorCode"] = SUCCESS_CODE;
                $this->response["msg"] = SUCCESS;
            } else {
                $this->response["errorCode"] = ERROR_CODE_L1;
                $this->response["msg"] = ERROR_L1;
            }
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function obtenerUsuarios($q = "1=1") {
        try {
            $query = "SELECT * FROM tecnm_usuarios WHERE " . $q;
            $oRF = $this->db->prepare($query);
            $oRF->execute();
            $data = array();
            foreach ($oRF as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["usuario"] = $cat["usuario"];
                $catt["nombre"] = $cat["nombre"];
                $catt["apellidos"] = $cat["apellidos"];
                $catt["correo"] = $cat["correo"];
                $catt["contrasenia"] = $cat["contrasenia"];
                $catt["id_departamento"] = $cat["id_departamento"];
                $catt["estatus"] = $cat["estatus"];
                $data[] = $cat;
            }
            $this->response["data"] = $data;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function editarUsuario($data) {
        $this->db->beginTransaction();
        try {
            $query = "UPDATE tecnm_usuarios SET usuario = ?, nombre = ?, apellidos = ?, correo = ?, id_departamento = ?, estatus = ?, contrasenia = ? WHERE id =? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(array(
                $data["usuario"],
                $data["nombre"],
                $data["apellidos"],
                $data["correo"],
                $data["id_departamento"],
                $data["estatus"],
                md5($data["contrasenia"]),
                $data["id_usuario"]
            ));
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
//            echo $exc->getMessage();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
            $this->response["info2"] = $exc->getMessage();
        }
        return $this->response;
    }

    function agregarUsuario($data) {
        $this->db->beginTransaction();
        try {
            $query = "INSERT INTO tecnm_usuarios (usuario, nombre, apellidos, correo, contrasenia, id_departamento, estatus) VALUES(?,?,?,?,?,?,?) ";
            $oComp = $this->db->prepare($query);
            $oComp->execute(array(
                $data["usuario"],
                $data["nombre"],
                $data["apellidos"],
                $data["correo"],
                md5($data["contrasenia"]),
                $data["id_departamento"],
                $data["estatus"]
            ));
            $id = $this->db->lastInsertId();
            $this->db->Commit();

            $this->response["data"] = $id;
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    function eliminarUsuario($id) {
        $this->db->beginTransaction();
        try {
            $query = "DELETE FROM tecnm_usuarios WHERE id =? ";
            $oRec = $this->db->prepare($query);
            $oRec->execute(array($id));
            $this->db->Commit();

            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
            $this->response["info"] = $exc->getTraceAsString();
        }
        return $this->response;
    }

    public function jsonSerialize() {
        $var = get_object_vars($this);
        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value, 'getJsonData')) {
                $value = $value->getJsonData();
            }
        }
        return $var;
    }

}
