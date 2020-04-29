<?php
    namespace model;
    use dawfony\Klasto;
    use model\Usuario;

    class Orm
    {

        public function obtenerCategorias() {
            $conn = Klasto::getInstance();
            $sql = "SELECT id, descripcion from categoria_post";
            return $conn->query($sql);
        }

        public function comprobarLogin($login) {
            $conn = Klasto::getInstance();
            $sql = "SELECT login from usuario where login = ?";
            if ($conn->queryOne($sql, [strtoupper($login)])) {
                return true;
            } else {
                return false;
            }
        }


        public function insertarUsuario($login, $password, $nombre, $email, $rol_id) {
            $conn = Klasto::getInstance();
            $sql = "INSERT into usuario values(?, ?, ?, ?, ?)";
            $password = password_hash($password, PASSWORD_DEFAULT);
            $conn->execute($sql, [$login, $password, $nombre, $email, $rol_id]);
        }

        public function obtenerUsuario($login) {
            $conn = Klasto::getInstance();
            $sql = "SELECT login, password, nombre, email, rol_id from usuario where login = ?";
            $usuario = $conn->queryOne($sql, [$login], "model\\Usuario");
            return $usuario;
        }

        public function obtenerTodosLosPost() {
            $conn = Klasto::getInstance();
            $sql = "SELECT id, fecha, resumen, texto, foto, usuario_login, categoria_post_id FROM post";
            $posts =  $conn->query($sql, [], "model\\Post");
            $categorias = $this->obtenerCategorias();
            foreach ($posts as $post) {
                $post->categoria = $categorias[$post->categoria_post_id]["descripcion"];
            }
            return $posts;

        }

        public function insertarPost($post) {
            $conn = Klasto::getInstance();
            $sql = "INSERT into post (fecha, resumen, texto, foto, usuario_login, categoria_post_id) values (?, ?, ?, ?, ?, ?)";
            $conn->execute($sql, [$post->fecha, $post->resumen, $post->texto, $post->foto, $post->usuario_login, $post->categoria_post_id]);
        }







        

    }




?>