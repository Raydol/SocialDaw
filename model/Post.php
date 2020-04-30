<?php
    namespace model;

    class Post
    {
        public $id;
        public $fecha;
        public $texto;
        public $foto;
        public $usuario_login;
        public $categoria_post_id;

        public $categoria;
        public $numero_comentarios = 0;
        public $numero_likes = 0;

    }




?>