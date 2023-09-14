<?php
    class ConexionMysql {
        public $mysqli;

        public function __construct() {
            $this->mysqli = new mysqli(SERVIDOR, USUARIO, CONTRASENA, BASE_DATOS);

            if ($this->mysqli->connect_error) {
                error_log("Error en la conexión: " . $this->mysqli->connect_error);
                throw new Exception('No se pudo establecer la conexión con la base de datos', $this->mysqli->connect_errno);
            }
        }

        public function totalFilas($sql) {
            $resultado = $this->mysqli->query($sql);

            if ($resultado) {
                return $resultado->num_rows;
            } else {
                return 0;
            }
        }

        public function errorInfo($origen, $json = TRUE) {
            try{
                if (!$origen->errno) {
                    $ok = TRUE;
                } else {
                    error_log('¡Pilas! ' . $origen->error); // dejar por si se requiere
                    $ok = FALSE;
                }
            }catch(Exception $ex){
                error_log('Ocurrio un error al procesar la respuesta! ' . $origen->error); // dejar por si se requiere
            }
            

            $mensaje = $origen->error;
            return $json ? json_encode(['ok' => $ok, 'mensaje' => $mensaje]) : ['ok' => $ok, 'mensaje' => $mensaje];
        }

        public function getFila($sql) {
            $resultado = $this->mysqli->query($sql);

            if ($resultado && $resultado->num_rows > 0) {
                $fila = $resultado->fetch_assoc();
                return $fila;
            } else {
                return FALSE;
            }
        }

        public function siguiente($param) {
            extract($param);

            $sql = "SELECT max($campo) as maximo FROM $tabla";
            $resultado = $this->mysqli->query($sql);

            if ($resultado && $resultado->num_rows > 0) {
                $fila = $resultado->fetch_assoc();
                $siguiente = $fila['maximo'] + 1;
                $info = ['ok' => true, 'mensaje' => '', 'siguiente' => $siguiente];
                echo json_encode($info);
            } else {
                echo $this->errorInfo($this->mysqli);
            }
        }

        public static function validarCaptcha($param) {
            $token = TRUE;
            extract($param);

            if (!$token) {
                $ok = FALSE;
            }

            $respuesta = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . CLAVE_RECAPTCHA . "&response=$token");
            $respuesta = json_decode($respuesta, true);
            $ok = (intval($respuesta["success"]) !== 1) ? FALSE : TRUE;

            echo json_encode(["ok" => $ok, "token" => $token, "respuesta" => $respuesta]);
        }
    }

?>