<?php
    ob_start();
    session_start();
    // session_destroy();

    require_once __DIR__ . "/models/DataBase.php";

    /*
     |-------------------------------------------------------------
     |  Controlador solicitado (por defecto: Landing)
     |-------------------------------------------------------------
     */
    $controller = isset($_GET['c']) ? $_GET['c'] : "Landing";

    // Saneamos el nombre del controlador (solo letras, números y guion bajo)
    if (!preg_match('/^[A-Za-z0-9_]+$/', $controller)) {
        $controller = 'Landing';
    }

    $route_controller = __DIR__ . "/controllers/" . $controller . ".php";

    if (file_exists($route_controller)) {

        require_once $route_controller;

        // Verificar que la clase del controlador exista
        if (!class_exists($controller)) {
            header("Location: ?");
            exit;
        }

        // Instanciar controlador
        $controllerInstance = new $controller();

        /*
         |-------------------------------------------------------------
         |  Lista blanca de acciones permitidas por controlador
         |  IMPORTANTE: agrega aquí las acciones/métodos que uses.
         |-------------------------------------------------------------
         */
        $allowedActions = [
            'Landing'   => ['main'],
            'Login'     => ['main', 'logout'],  // ejemplo de otra acción
            'Dashboard' => ['main'],
            // Ejemplos:
            // 'Users'     => ['main', 'create', 'update', 'delete'],
            // 'Products'  => ['main', 'list', 'detail'],
        ];

        // Nombre de la vista (usamos el mismo nombre del controlador)
        $view = $controller;

        /*
         |-------------------------------------------------------------
         |  Resolución segura de la acción
         |-------------------------------------------------------------
         */
        $action    = 'main';                      // acción por defecto
        $rawAction = isset($_GET['a']) ? $_GET['a'] : 'main';

        // Validar acción contra la lista blanca
        if (isset($allowedActions[$controller]) &&
            in_array($rawAction, $allowedActions[$controller], true)) {

            $action = $rawAction;
        }

        // Defensa extra: el método debe existir en el controlador
        if (!method_exists($controllerInstance, $action)) {
            $action = 'main';
        }

        /*
         |-------------------------------------------------------------
         |  Vistas públicas (sin sesión)
         |-------------------------------------------------------------
         */
        if ($view === 'Landing' || $view === 'Login') {

            require_once __DIR__ . "/views/company/header.view.php";
            call_user_func([$controllerInstance, $action]);  // acción ya validada
            require_once __DIR__ . "/views/company/footer.view.php";

        /*
         |-------------------------------------------------------------
         |  Vistas privadas (con sesión de rol)
         |-------------------------------------------------------------
         */
        } elseif (!empty($_SESSION['session'])) {

            require_once __DIR__ . "/models/User.php";

            // Nombre del rol en sesión (admin, seller, customer, etc.)
            $session = $_SESSION['session'];

            $headerRole = __DIR__ . "/views/roles/" . $session . "/header.view.php";
            $footerRole = __DIR__ . "/views/roles/" . $session . "/footer.view.php";

            if (file_exists($headerRole) && file_exists($footerRole)) {

                require_once $headerRole;
                call_user_func([$controllerInstance, $action]);  // usamos la misma acción validada
                require_once $footerRole;

            } else {
                // Si el rol no tiene vistas válidas configuradas, vuelve al inicio
                header("Location: ?");
                exit;
            }

        } else {
            // No hay sesión y la vista no es pública -> redirigir al inicio
            header("Location: ?");
            exit;
        }

    } else {
        // El archivo del controlador no existe -> redirigir al inicio
        header("Location: ?");
        exit;
    }

    ob_end_flush();
?>
