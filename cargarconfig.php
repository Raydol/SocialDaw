<?php

    //Autoload para cargar las clases creadas por mi

        spl_autoload_register(function($class) {
            require_once str_replace("\\", "/", $class) . ".php";
        });

    //Parsear el archivo de configuración en un array global

        if (!$db = parse_ini_file("config.ini")) {
            die("El archivo de configuración no existe");
        }

    //Tener el PATH en una variable global

        $URL_PATH = $db["urlpath"];

    //Abrir la conexión con la base de datos

        dawfony\Klasto::init($db["host"], $db["user"], $db["password"], $db["name"]);



?>