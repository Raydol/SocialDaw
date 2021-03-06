<?php
    namespace Controller;
    require_once "funciones.php";
    use dawfony\Ti;
    use dawfony\Klasto;
    use model\Orm;
    use model\Usuario;
    use model\Post;
    use model\Comentario;
    
    class UsuarioController extends Controller
    {
        public function listado() {
            $title = "Listado";
            $orm = new Orm();
            $posts = $orm->obtenerTodosLosPost();
            foreach ($posts as $post) {
                //$post->numero_likes = $orm->contarLikes($id);
                $post->numero_comentarios = $orm->contarComentarios($post->id); 
            }
            echo Ti::render("templates\\ListadoView.phtml", compact("title", "posts"));
        }

        public function listadoDeSeguidos() {
            if (isset($_SESSION["login"])) {
                $orm = new Orm();
                $posts = $orm->obtenerPostsSiguiendo($_SESSION["login"]);
                $title = "Listado de seguidos";
                echo Ti::render("templates\\ListadoView.phtml", compact("title", "posts"));
            } else {
                echo "Algo salió mal";
            }
        }

        public function registro() {
            $title = "Registro";
            $login = "";
            $password = "";
            $repassword = "";
            $nombre = "";
            $email = "";
            $errorLogin = "";
            $errorRepassword = "";
            echo Ti::render("templates\\RegistroView.phtml", compact("title","login", "password", "repassword", "nombre", 
            "email", "errorLogin", "errorRepassword"));
        }

        public function procesarRegistro() {
            global $URL_PATH;
            $title = "Registro";
            $login = $_POST["login"] ?? "";
            $password = $_POST["password"] ?? "";
            $repassword = $_POST["repassword"] ?? "";
            $nombre = $_POST["nombre"] ?? "";
            $email = $_POST["email"] ?? "";
            $rol_id = $_POST["rol_id"];
            $errorLogin = "";
            $errorRepassword = "";

            $hayProblemas = false;

            if ($login != "") {
                $orm = new Orm;
                $login = strtolower($login);
                if ($orm->comprobarLogin($login)) {
                    $errorLogin = "El login que introdujo ya está en uso";
                    $hayProblemas = true;
                } else {
                    if ($password != $repassword) {
                        $errorRepassword = "Las contraseñas no coinciden";
                        $hayProblemas = true;
                    } else{
                        $nombre = strtolower($nombre);
                        $orm->insertarUsuario($login, $password, $nombre, $email, $rol_id);
                        header("Location: $URL_PATH");
                    }
                }
            } else {
                $errorLogin = "El login no puede estar vacío";
                $hayProblemas = true;
            }

            if ($hayProblemas) {
                echo Ti::render("templates\\RegistroView.phtml", compact("title","login", "password", "repassword", "nombre", 
            "email", "errorLogin", "errorRepassword"));

            }
        }

        public function login() {
            $title = "Login";
            $login = "";
            $password = "";
            $errorLog = "";
            $errorLoginVacio = "";

            echo Ti::render("templates\\LoginView.phtml", compact("title", "login", "password", "errorLog", "errorLoginVacio"));
        }

        public function procesarLogin() {
            $title = "Login";
            $login = $_POST["login"] ?? "";
            $password = $_POST["password"] ?? "";
            $errorLog = "";
            $errorLoginVacio = "";
            $hayProblemas = false;
            global $URL_PATH;

            if ($login != "") {
                $orm = new Orm();
                $login = strtolower($login);
                $usuario = $orm->obtenerUsuario($login);
                if ($usuario) {
                    if (password_verify($password, $usuario->password)) {
                        $_SESSION["login"] = $login;
                        header("Location: $URL_PATH");
                    } else {
                        $errorLog = "El login o la contraseña que ha introducido no son válidos";
                        $hayProblemas = true;
                    }
                } else {
                    $errorLog = "El login o la contraseña que ha introducido no son válidos";
                    $hayProblemas = true;
                }
            } else {
                $errorLoginVacio = "Debe introducir un login para iniciar sesión";
                $hayProblemas = true;
            }

            if ($hayProblemas) {
                echo Ti::render("templates\\LoginView.phtml", compact("title", "login", "password", "errorLog", "errorLoginVacio"));
            }
        }


        public function logout() {
            session_destroy();
            global $URL_PATH;
            header("Location: $URL_PATH");
        }


        public function post() {

            if (isset($_SESSION["login"])) {
                $title = "Nuevo Post";
                $errorCategoria = "";
                $errorTexto = "";
                $errorImagen = "";
                $resumen = "";
                $texto = "";
                echo Ti::render("templates\\FormularioPostView.phtml", compact("title", "errorCategoria", "errorTexto", "resumen", "texto", "errorImagen"));
            } else {
                echo "Algo salió mal";
            }
            


        }

        public function procesarPost() {
            global $URL_PATH;
            $title = "Nuevo Post";
            $errorCategoria = "";
            $errorTexto = "";
            $errorImagen = "";
            $resumen = $_POST["resumen"];
            $texto = $_POST["texto"] ?? "";
            $categoria_id = $_POST["categoria"];
            $hayProblemas = false;

            if ($texto == "") {
                $hayProblemas = true;
                $errorTexto = "*No ha introducido ningún texto";
            }

            if ($categoria_id == "") {
                $hayProblemas = true;
                $errorCategoria = "*Introduce una categoría";
            }

            //COMPROBACIONES AQUI DEL FICHERO QUE NOS LLEGA DEL FORMULARIO

            if ($_FILES["imagen"]["type"] == "image/jpg"
            || $_FILES["imagen"]["type"] == "image/jpeg"
            || $_FILES["imagen"]["type"] == "image/png"
            || $_FILES["imagen"]["type"] == "image/gif") {
                if ($_FILES["imagen"]["error"] == 0) {
                    move_uploaded_file($_FILES["imagen"]["tmp_name"],
                     "assets\\publicaciones\\" . $_SESSION["login"] . $_FILES["imagen"]["name"]);
                } else {
                    $hayProblemas = true;
                    $errorImagen = "*La imagen tiene un error";
                }
            } else {
                $hayProblemas = true;
                $errorImagen = "*La imagen no tiene la extensión apropiada";
            }

            if ($hayProblemas) {
                echo Ti::render("templates\\FormularioPostView.phtml", compact("title", "resumen", "texto", "errorCategoria", "errorTexto", "errorImagen"));
            } else {

                //AQUI SUBIMOS EL POST A LA BASE DE DATOS
                $fecha = getdate();
                $post = new Post;
                $post->fecha = $fecha["year"] . "-" . $fecha["mon"] . "-" . $fecha["mday"] . " " . $fecha["hours"] . ":" . $fecha["minutes"] . ":" . $fecha["seconds"];
                $post->resumen = $resumen;
                $post->texto = $texto;
                $post->foto = $_SESSION["login"] . $_FILES["imagen"]["name"];
                $post->usuario_login = $_SESSION["login"];
                $post->categoria_post_id = $categoria_id;

                $orm = new Orm();
                $orm->insertarPost($post);
                header("Location: $URL_PATH");
            }

        }

        public function verPerfil($login) {
            $orm = new Orm();
            $usuario = $orm->obtenerUsuario($login);
            $usuario->seguidores = $orm->obtenerSeguidores($login);
            $usuario->seguidos = $orm->obtenerSeguidos($login);
            if (isset($_SESSION["login"])) {
                $usuario->loSigues = $orm->loSigues($_SESSION["login"], $login);
            }
            $title = "Perfil de $login";

            $posts = $orm->obtenerPostsPorUsuario($login);
            echo Ti::render("templates\\PerfilView.phtml", compact("usuario", "title", "posts"));
        }

        public function seguir($login) {
            global $URL_PATH;
            if (isset($_SESSION["login"])) {
                $orm = new Orm();
                $orm->seguirUsuario($login, $_SESSION["login"]);
                header("Location: $URL_PATH/perfil/$login");
            } else {
                echo "Algo salió mal";
            }

        }

        public function noSeguir($login) {
            global $URL_PATH;
            if (isset($_SESSION["login"])) {
                $orm = new Orm();
                $orm->dejarDeSeguirUsuario($login, $_SESSION["login"]);
                header("Location: $URL_PATH/perfil/$login");
            } else {
                echo "Algo salió mal";
            }
        }

        public function verPost($id) {
            $orm = new Orm();
            $post = $orm->obtenerPost($id);
            if ($post) {
                $title = "Ver Post";
                $fechaSeparada = explode(" ", $post->fecha);
                $fecha = $fechaSeparada[0];
                $hora = $fechaSeparada[1];
                //$post->numero_likes = $orm->contarLikes($id);
                $post->numero_comentarios = $orm->contarComentarios($id);

                $comentarios = $orm->obtenerComentariosPorPost($id);
                echo Ti::render("templates\\PostView.phtml", compact("post", "title", "fecha", "hora", "comentarios"));
            } else {
                echo "Algo salió mal";
            }
        }

        public function nuevoComentario() {
            global $URL_PATH;
            $fecha = getdate();
            $comentario = new Comentario;
            $comentario->fecha = $fecha["year"] . "-" . $fecha["mon"] . "-" . $fecha["mday"] . " " . $fecha["hours"] . ":" . $fecha["minutes"] . ":" . $fecha["seconds"];
            $comentario->texto = sanitizar($_POST["textarea"]);
            $comentario->post_id = $_POST["post_id"];
            $comentario->usuario_login = $_SESSION["login"];
            $orm = new Orm();
            $orm->annadirComentario($comentario);
            header("Location: $URL_PATH/post/$comentario->post_id");
        }

    }






?>