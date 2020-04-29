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

        public function obtenerSeguidos($login) {
            $conn = Klasto::getInstance();
            $sql = "SELECT COUNT(usuario_login_seguidor) as siguiendo FROM sigue WHERE usuario_login_seguidor = ?";
            return $conn->queryOne($sql, [$login])["siguiendo"];
        }

        public function obtenerSeguidores($login) {
            $conn = Klasto::getInstance();
            $sql = "SELECT COUNT(usuario_login_seguido) as seguidores FROM sigue WHERE usuario_login_seguido = ?";
            return $conn->queryOne($sql, [$login])["seguidores"];
        }


        public function loSigues($sessionLogin, $login) {
            $conn = Klasto::getInstance();
            $sql = "SELECT COUNT(*) as seguimiento FROM sigue where usuario_login_seguidor = ? AND usuario_login_seguido = ?";
            return $conn->queryOne($sql, [$sessionLogin, $login])["seguimiento"];
        }

        public function seguirUsuario($seguido, $seguidor) {
            $conn = Klasto::getInstance();
            $sql = "INSERT into sigue (usuario_login_seguido, usuario_login_seguidor) values(?, ?)";
            $conn->execute($sql, [$seguido, $seguidor]);
        }

        public function dejarDeSeguirUsuario($seguido, $seguidor) {
            $conn = Klasto::getInstance();
            $sql = "DELETE from sigue WHERE usuario_login_seguido = ? AND usuario_login_seguidor = ?";
            $conn->execute($sql, [$seguido, $seguidor]);
        }

        public function obtenerPostsPorUsuario($login) {
            $conn = Klasto::getInstance();
            $sql = "SELECT id, fecha, resumen, texto, foto, usuario_login, categoria_post_id 
            FROM post WHERE usuario_login = ? ORDER BY fecha DESC";
            $posts = $conn->query($sql, [$login], "model\\Post");
            $categorias = $this->obtenerCategorias();
            foreach ($posts as $post) {
                $post->categoria = $categorias[$post->categoria_post_id]["descripcion"];
            }
            return $posts;
        }

        public function obtenerPostsSiguiendo($login) {
            $conn = Klasto::getInstance();
            $sql = "SELECT id, fecha, resumen, texto, foto, usuario_login, categoria_post_id 
            FROM post INNER JOIN sigue ON post.usuario_login = sigue.usuario_login_seguido WHERE sigue.usuario_login_seguidor = ?";
            $posts = $conn->query($sql, [$login], "model\\Post");
            $categorias = $this->obtenerCategorias();
            foreach ($posts as $post) {
                $post->categoria = $categorias[$post->categoria_post_id]["descripcion"];
            }
            return $posts;
        }







        

    }




?>