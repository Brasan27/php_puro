<?php
    ob_start();
    session_start();
    // session_destroy();

    require_once __DIR__ . "/models/DataBase.php";

    // Controlador por defecto
    $controller = isset($_REQUEST['c']) ? $_REQUEST['c'] : "Landing";
    $route_controller = __DIR__ . "/controllers/" . $controller . ".php";

    if (file_exists($route_controller)) {

        $view = $controller;
        require_once $route_controller;

        // Instanciar controlador
        $controller = new $controller;
        $action = isset($_REQUEST['a']) ? $_REQUEST['a'] : 'main';

        // Vistas públicas (sin sesión)
        if ($view === 'Landing' || $view === 'Login') {

            require_once __DIR__ . "/views/company/header.view.php";
            call_user_func(array($controller, $action));
            require_once __DIR__ . "/views/company/footer.view.php";

        // Vistas con sesión (roles)
        } elseif (!empty($_SESSION['session'])) {

            require_once __DIR__ . "/models/User.php";
            // OJO: aquí quitamos el unserialize inseguro
            // $profile  = unserialize($_SESSION['profile']);  // <- eliminado
            $session  = $_SESSION['session'];

            // Armamos la ruta de las vistas según el rol en sesión
            $headerRole = __DIR__ . "/views/roles/" . $session . "/header.view.php";
            $footerRole = __DIR__ . "/views/roles/" . $session . "/footer.view.php";

            if (file_exists($headerRole) && file_exists($footerRole)) {
                require_once $headerRole;
                call_user_func(array($controller, $action));
                require_once $footerRole;
            } else {
                // Si el rol no tiene plantillas válidas, redirecciona al login
                header("Location: ?");
            }

        } else {
            header("Location: ?");
        }

    } else {
        header("Location: ?");
    }

    ob_end_flush();
?>
