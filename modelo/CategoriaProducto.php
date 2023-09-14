<?php
class CategoriaProducto implements Persistible {

    /**
     * Devuelve una cadena JSON que contiene el resultado de seleccionar todas las categorías de productos guardadas
     * Se usa MySQLi
     */
    public function seleccionar($param) {
        extract($param);
        $sql = "SELECT * FROM categorias_productos ORDER BY id_categoria_producto";

        // Prepara la consulta SQL para ejecutarla, luego de recibir los parámetros de filtrado
        $stmt = $conexion->mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $filas = $result->fetch_all(MYSQLI_ASSOC); // Devuelve un array que contiene todas las filas del conjunto de resultados
        echo json_encode($filas); // Las filas resultantes se envían en formato JSON al frontend
    }

    /**
     * Inserta un registro de categorías de productos en la base de datos
     */
    public function insertar($param) {
        extract($param);
        // error_log(print_r($param, TRUE)); // Quitar comentario para ver lo que se recibe del front-end

        $sql = "CALL insertar_categoria(?, @id_categoria)";

        // Prepara la consulta SQL para ejecutarla luego de recibir los parámetros de inserción
        $stmt = $conexion->mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('s', $categoria);

            if ($stmt->execute()) {
                $stmt->close();
                $result = $conexion->mysqli->query("SELECT @id_categoria as id_categoria");
                $fila = $result->fetch_assoc();
                $info['id_categoria'] = $fila['id_categoria'];
                $info['ok'] = $fila['id_categoria'] > 0;
                echo json_encode($info);
            } else {
                echo $conexion->mysqli->error;
            }
        } else {
            echo json_encode(['ok' => FALSE, 'mensaje' => 'Falló en la instrucción de inserción de categorias de productos']);
        }
    }

    /**
     * Inserta un registro de categorías de productos en la base de datos
     */
    public function actualizar($param) {
        extract($param);
        // error_log(print_r($param, TRUE)); // Quitar comentario para ver lo que se recibe del front-end

        $sql = "UPDATE categorias_productos SET nombre=? WHERE id_categoria_producto = ?";

        // Prepara la consulta SQL para ejecutarla luego de recibir los parámetros de inserción
        $stmt = $conexion->mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('si', $data['nombre'], $data['id_actual']);

            if ($stmt->execute()) {
                echo $conexion->errorInfo($stmt);
            } else {
                echo $conexion->errorInfo($stmt);
            }
        } else {
            echo json_encode(['ok' => FALSE, 'mensaje' => 'Falló en la instrucción de actualización para categorias_productos']);
        }
    }

    /**
     * Elimina un registro con base en su PK  //////////////// id_categoria
     */
    public function eliminar($param) {
        extract($param);
        // error_log(print_r($param, TRUE)); // Quitar comentario para ver lo que se recibe del front-end
        $sql = "DELETE FROM categorias_productos WHERE id_categoria_producto = ?";
        $stmt = $conexion->mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('i', $id_categoria);
            if ($stmt->execute()) {
                echo $conexion->errorInfo($stmt);
            } else {
                echo $conexion->errorInfo($stmt);
            }
        } else {
            echo json_encode(['ok' => FALSE, 'mensaje' => 'Falló en la eliminación de categorias']);
        }
    }

    public function listar($param) {
        extract($param);

        $sql = "SELECT * FROM categorias_productos ORDER BY nombre";

        // Se ejecuta la consulta SQL, para obtener el conjunto de resultados (si los hay) como un objeto MySQLi_Result
        $result = $conexion->mysqli->query($sql);
        
        if ($result) {
            $lista = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['ok' => TRUE, 'lista' => $lista]);
        } else {
            echo json_encode(['ok' => FALSE, 'mensaje' => 'Imposible consultar las categorías de productos']);
        }
    }
}
