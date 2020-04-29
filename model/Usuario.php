<?php
namespace model;

    class Usuario
    {
        public $login;
        public $password;
        public $nombre;
        public $email;
        public $rol_id;
        public $seguidores = 0;
        public $siguiendo = 0;
        public $loSigues = false;

        /*function __construct($login, $password, $nombre, $email, $rol_id) {
            $this->login = $login;
            $this->password = $password;
            $this->nombre = $nombre;
            $this->email = $email;
            $this->rol_id = $rol_id;

        }*/
    }



?>