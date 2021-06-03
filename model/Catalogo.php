<?php

/*
 * Catalogo.php
 * @author FIGG - DIRAC
 * @copyright (c) 2017, DIRAC.
 * @description Clase de usuario para controlas funciones en SGI
 */

require_once '../config/ConnectionDB.php';
require_once '../config/properties.php';

class Catalogo implements JsonSerializable {

    //put your code here

    public $table;
    public $id;
    public $clave;
    public $nombre;
    public $descripcion;
    public $estatus;
    public $evaluacion;
    public $db;
    public $response;
    private static $instance;

    function __construct($table) {
        $this->db = ConnectionDB::connectSngtn()->DBConnect();
        $this->response = array();
        $this->table = $table;
    }

    // Método singleton
    public static function catSngltn($table) {
        if (!isset(self::$instance)) {
            $Catalogo = __CLASS__;
            self::$instance = new $Catalogo($table);
        }
        return self::$instance;
    }

    // Evita que el objeto se pueda clonar
    public function __clone() {
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
    }

    /*     * ************************************************************* */

    function getCat() {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE estatus = 1 ORDER BY nombre ASC";
            $oCat = $this->db->prepare($query);
            $oCat->execute();
            $data = array();
            foreach ($oCat as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["clave"] = $cat["clave"];
                $catt["nombre"] = utf8_encode($cat["nombre"]);
                $catt["descripcion"] = utf8_encode($cat["descripcion"]);
                $catt["estatus"] = $cat["estatus"];
                $data[] = $cat;
            }
//            var_dump($data);
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
    
    function getPuestos() {
        try {
            $query = "SELECT * FROM cat_perfil_dirac WHERE estatus = 1 ORDER BY nombre ASC";
            $oCat = $this->db->prepare($query);
            $oCat->execute();
            $data = array();
            foreach ($oCat as $key => $cat) {
                $catt = array();
                $catt["id"] = $cat["id"];
                $catt["clave"] = $cat["clave"];
                $catt["nombre"] = utf8_encode($cat["nombre"]);
                $catt["descripcion"] = utf8_encode($cat["descripcion"]);
                $catt["estatus"] = $cat["estatus"];
                $catt["evaluacion"] = $cat["evaluacion"];
                $data[] = $cat;
            }
//            var_dump($data);
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

    function getCatById(Catalogo $catalogo) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id= ?";
            $oCat = $this->db->prepare($query);
            $oCat->execute(array($catalogo->getId()));

//            var_dump($oCat);

            if ($oCat->rowCount() == 1) {
                $cat = $oCat->fetch(PDO::FETCH_OBJ);
                $catalogo->setId($cat->id);
                $catalogo->setClave($cat->clave);
                $catalogo->setNombre($cat->nombre);
                $catalogo->setDescripcion($cat->descripcion);
                $catalogo->setEstatus($cat->estatus);

                $this->response["data"] = $catalogo->jsonSerialize();
                $this->response["errorCode"] = SUCCESS_CODE;
                $this->response["msg"] = SUCCESS;
            } else {
                $this->response["errorCode"] = ERROR_CODE_C1;
                $this->response["msg"] = ERROR_C1;
            }
        } catch (PDOException $exc) {
            echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function replaceCat(Catalogo $catalogo, $opcion) {
        $this->db->beginTransaction();
        try {
            $query = "";
            if (intval($opcion) === 1) {//Insertar
                $query = "INSERT INTO " . $this->table . " (clave, nombre, descripcion, estatus) VALUES(?,?,?,?) ";
                $oCat = $this->db->prepare($query);
                $oCat->execute(array(
                    $catalogo->getClave(),
                    $catalogo->getNombre(),
                    $catalogo->getDescripcion(),
                    $catalogo->getEstatus()
                ));
            } else {//Actualizar
                $query = "UPDATE " . $this->table . " SET clave = ?, nombre = ?, descripcion = ?, estatus = ? WHERE id =? ";
                $oCat = $this->db->prepare($query);
                $oCat->execute(array(
                    $catalogo->getClave(),
                    $catalogo->getNombre(),
                    $catalogo->getDescripcion(),
                    $catalogo->getEstatus(),
                    $catalogo->getId()
                ));
            }

            $id = $this->db->lastInsertId();
            $catalogo->setId($id);
            $this->db->Commit();
            $this->response["data"] = $catalogo->jsonSerialize();
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }
    
    function replacePuesto(Catalogo $catalogo, $opcion) {
        $this->db->beginTransaction();
        try {
            $query = "";
            if (intval($opcion) === 1) {//Insertar
                $query = "INSERT INTO " . $this->table . " (clave, nombre, descripcion, estatus, evaluacion) VALUES(?,?,?,?,?) ";
                $oCat = $this->db->prepare($query);
                $oCat->execute(array(
                    $catalogo->getClave(),
                    $catalogo->getNombre(),
                    $catalogo->getDescripcion(),
                    $catalogo->getEstatus(),
                    $catalogo->getEvaluacion()
                ));
            } else {//Actualizar
                $query = "UPDATE " . $this->table . " SET clave = ?, nombre = ?, descripcion = ?, estatus = ?, evaluacion = ? WHERE id =? ";
                $oCat = $this->db->prepare($query);
                $oCat->execute(array(
                    $catalogo->getClave(),
                    $catalogo->getNombre(),
                    $catalogo->getDescripcion(),
                    $catalogo->getEstatus(),
                    $catalogo->getEvaluacion(),
                    $catalogo->getId()
                ));
            }

            $id = $this->db->lastInsertId();
            $catalogo->setId($id);
            $this->db->Commit();
            $this->response["data"] = $catalogo->jsonSerialize();
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            $this->db->rollback();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    function getSGIProfiles() {
        try {
            $query = "SELECT * FROM perfil_usuario_sgi WHERE estatus = 1";
            $oCat = $this->db->prepare($query);
            $oCat->execute();
            $data = array();
            foreach ($oCat as $key => $cat) {
                $catt = array();
                $catt["id_perfil_sgi"] = $cat["id_perfil_sgi"];
                $catt["nombre"] = utf8_encode($cat["nombre"]);
                $catt["descripcion"] = utf8_encode($cat["descripcion"]);
                $catt["estatus"] = $cat["estatus"];
                $data[] = $cat;
            }
//            var_dump($data);
            $this->response["numElems"] = $oCat->rowCount();
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

    function deleteCatRecord($table, $id) {
        try {
            $query = "DELETE FROM " . $table . " WHERE id=?";
            $oCat = $this->db->prepare($query);
            $oCat->execute(array($id));

//            $this->db->Commit();
            $this->response["errorCode"] = SUCCESS_CODE;
            $this->response["msg"] = SUCCESS;
        } catch (PDOException $exc) {
            //echo $exc->getTraceAsString();
            $this->response["errorCode"] = ERROR_CODE;
            $this->response["msg"] = ERROR;
        }
        return $this->response;
    }

    /*     * ************************************************************* */

    function getId() {
        return $this->id;
    }

    function getClave() {
        return $this->clave;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getDescripcion() {
        return $this->descripcion;
    }

    function getEstatus() {
        return $this->estatus;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    function setEstatus($estatus) {
        $this->estatus = $estatus;
    }

    function getTable() {
        return $this->table;
    }

    function setTable($table) {
        $this->table = $table;
    }
    
    function getEvaluacion() {
        return $this->evaluacion;
    }

    function setEvaluacion($evaluacion) {
        $this->evaluacion = $evaluacion;
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
