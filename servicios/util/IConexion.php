<?php
interface IConexion {

    public function totalFilas($sql);

    public function errorInfo($origen, $json = TRUE);

    public function getFila($sql);

    public function siguiente($param);
    
    public static function validarCaptcha($param);

}