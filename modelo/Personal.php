<?php

    class Personal implements Persistible {

        /**
         * Devuelve una cadena JSON que contiene el resultado de seleccionar personal
         * Se usa MySQLi
         */
        public function seleccionar($param) {
            extract($param);
            $sql = "SELECT id_persona, nombre, telefono, direccion, perfil, contrasena
                    FROM personal
                    ORDER BY nombre";

            // Prepara la consulta SQL para ejecutarla, luego de recibir los parámetros de filtrado
            $stmt = $conexion->mysqli->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $filas = $result->fetch_all(MYSQLI_ASSOC); // Devuelve un array que contiene todas las filas del conjunto de resultados
            echo json_encode($filas); // Las filas resultantes se envían en formato JSON al frontend
        }

        /**
         * Inserta un registro de personal en la base de datos
         */
        public function insertar($param) {
            extract($param);
            // error_log(print_r($param, TRUE)); // Quitar comentario para ver lo que se recibe del front-end

            $sql = "INSERT INTO personal (id_persona, nombre, telefono, direccion, perfil, contrasena)
                    VALUES (?, ?, ?, ?, ?, ?)";

            // Prepara la consulta SQL para ejecutarla luego de recibir los parámetros de inserción
            $stmt = $conexion->mysqli->prepare($sql);

            if ($stmt) {
                $contrasena = password_hash($data['contrasena'], PASSWORD_DEFAULT);
                $stmt->bind_param('isssss', $data['id_persona'], $data['nombre'], $data['telefono'], $data['direccion'], $data['perfil'], $contrasena);

                if ($stmt->execute()) {
                    echo $conexion->errorInfo($stmt);
                } else {
                    echo $conexion->errorInfo($stmt);
                }
            } else {
                echo json_encode(['ok' => FALSE, 'mensaje' => 'Falló en la instrucción de inserción para personal']);
            }
        }

        /**
         * Inserta un registro de personal en la base de datos
         */
        public function actualizar($param) {
            extract($param);
            // error_log(print_r($param, TRUE)); // Quitar comentario para ver lo que se recibe del front-end

            $sql = "UPDATE personal
                    SET id_persona=?, nombre=?, telefono=?, direccion=?, perfil=?, contrasena=?
                    WHERE id_persona = ?";

            // Prepara la consulta SQL para ejecutarla luego de recibir los parámetros de inserción
            $stmt = $conexion->mysqli->prepare($sql);

            if ($stmt) {
                $contrasena = password_hash($data['contrasena'], PASSWORD_BCRYPT);
                $stmt->bind_param('isssssi', $data['id_persona'], $data['nombre'], $data['telefono'], $data['direccion'], $data['perfil'], $contrasena, $data['id_actual']);

                if ($stmt->execute()) {
                    echo $conexion->errorInfo($stmt);
                } else {
                    echo $conexion->errorInfo($stmt);
                }
            } else {
                echo json_encode(['ok' => FALSE, 'mensaje' => 'Falló en la instrucción de actualización para personal']);
            }
        }

        /**
         * Elimina un registro con base en su PK
         */
        public function eliminar($param) {
            extract($param);
            // error_log(print_r($param, TRUE)); // Quitar comentario para ver lo que se recibe del front-end
            $sql = "DELETE FROM personal WHERE id_persona = ?";
            $stmt = $conexion->mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('i', $id_persona);
                if ($stmt->execute()) {
                    $estado = $conexion->errorInfo($stmt);
                    echo $conexion->errorInfo($stmt);
                } else {
                    echo $conexion->errorInfo($stmt);
                }
            } else {
                echo json_encode(['ok' => FALSE, 'mensaje' => 'Falló en la eliminación de personal']);
            }
        }

        public function listar($param) {
            extract($param);

            $sql = "SELECT * FROM personal ORDER BY nombre";

            // Se ejecuta la consulta SQL, para obtener el conjunto de resultados (si los hay) como un objeto MySQLi_Result
            $result = $conexion->mysqli->query($sql);
            
            if ($result) {
                $lista = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode(['ok' => TRUE, 'lista' => $lista]);
            } else {
                echo json_encode(['ok' => FALSE, 'mensaje' => 'Imposible consultar el personal']);
            }
        }

        public function autenticar($param) {
            extract($param);

            $sql = "SELECT id_persona, nombre, perfil, contrasena FROM personal
                    WHERE id_persona = ?";

            $stmt = $conexion->mysqli->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $idPersona);
                if ($stmt->execute()) {
                    $stmt->bind_result($id_persona, $nombre, $perfil, $contrasenaBD);
                    $stmt->fetch();
                    if (password_verify($contrasena, $contrasenaBD)) {
                        $usuario = [
                            'id_persona' => $id_persona,
                            'nombre' => $nombre,
                            'perfil' => $perfil,
                        ];
                        echo json_encode(["ok" => TRUE, "usuario" => $usuario]);
                    } else {
                        echo json_encode(["ok" => FALSE, "mensaje" => "Falló la autenticación del usuario"]);
                    }
                } else {
                    echo $conexion->errorInfo($stmt);
                }
            } else {
                echo json_encode(['ok' => FALSE, 'mensaje' => 'Falló el acceso a los datos del usuario']);
            }
        }
    }
?>