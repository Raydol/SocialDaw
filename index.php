<?php
    require_once "vendor/autoload.php";
    require_once "cargarconfig.php";
    use NoahBuscher\Macaw\Macaw;
    use Controller\UsuarioController;

    session_start();

    //Listado
    Macaw::get($URL_PATH . "/", "controller\UsuarioController@listado");
    Macaw::get($URL_PATH . "/listado", "controller\UsuarioController@listado");

    //Registro
    Macaw::get($URL_PATH . "/registro", "controller\UsuarioController@registro");
    Macaw::post($URL_PATH . "/registro", "controller\UsuarioController@procesarRegistro");

    //Login
    Macaw::get($URL_PATH . "/login", "controller\UsuarioController@login");
    Macaw::post($URL_PATH . "/login", "controller\UsuarioController@procesarLogin");

    //Logout
    Macaw::get($URL_PATH . "/logout", "controller\UsuarioController@logout");

    //Subir un post
    Macaw::get($URL_PATH . "/subirpost", "controller\UsuarioController@post");
    Macaw::post($URL_PATH . "/subirpost", "controller\UsuarioController@procesarPost");

    //Perfil
    Macaw::get($URL_PATH . "/perfil/(:any)", "controller\UsuarioController@verPerfil");

    //Seguir y dejar de seguir
    Macaw::get($URL_PATH . "/perfil/(:any)/seguir", "controller\UsuarioController@seguir");
    Macaw::get($URL_PATH . "/perfil/(:any)/noseguir", "controller\UsuarioController@noSeguir");

    //Listado siguiendo
    Macaw::get($URL_PATH . "/listado/seguidos", "controller\UsuarioController@listadoDeSeguidos");

    Macaw::error(function() {
    echo '404 :: Not Found';
    });

    Macaw::dispatch();


















