<?php
    ob_start();
    session_start();
    // session_destroy();

    // Siempre usa rutas absolutas
    require_once __DIR__ . "/models/DataBase.php";

    /**
     * Lista blanca de controladores permitidos.
     * Agrega aquí todos los controladores válidos de tu aplicación.
     */
    $allowedControllers = [
        'Landing' => [
            'file'  => __DIR__ . '/controllers/Landing.php',
            'class' => 'Landing',
            // acciones permitidas para este controlador
            'actions' => ['main'] // agrega más si las tienes: 'detalle', 'contacto', etc.
        ],
        'Login' => [
            'file'  => __DIR__ . '/controllers/Login.php',
            'class' => 'Login',
            'actions' => ['main'] // agrega las acciones válidas: 'main', 'auth', etc.
        ],
        // 'OtroControlador' => [
        //     'file'  => __DIR__ . '/controllers/OtroControlador.php',
        //     'class' => 'OtroControlador',
        //     'actions' => ['main', 'listar', 'ver']
        // ],
    ];

    // 1. Leer parámetro del controlador (GET es suficiente aquí)
    $controllerKey = isset($_GET['c']) ? $_GET['c'] : 'Landing';

    // 2. Validar contra la lista blanca; si no existe, usar uno seguro por defecto
    if (!array_key_exists($controllerKey, $allowedControllers)) {
        $controllerKey = 'Landing';
    }

    $controllerConfig  = $allowedControllers[$controllerKey];
    $route_controller  = $controllerConfig['file'];
    $controllerClass   = $controllerConfig['class'];
    $allowedActions    = $controllerConfig['actions'];

    // 3. Verificar que el archivo realmente exista y sea legible
    if (is_readable($route_controller)) {
        $view = $controllerKey;
        require_once $route_controller;

        // Instanciar el controlador
        $controller = new $controllerClass;

        // 4. Acción: también controlada por lista blanca
        $action = isset($_GET['a']) ? $_GET['a'] : 'main';
        if (!in_array($action, $allowedActions, true)) {
            $action = 'main';
        }

        // --- Vistas públicas ---
        if ($view === 'Landing' || $view === 'Login') {
            require_once __DIR__ . "/views/company/header.view.php";
            call_user_func([$controller, $action]);
            require_once __DIR__ . "/views/company/footer.view.php";

        // --- Vistas según rol (sesión ya controlada del lado servidor) ---
        } elseif (!empty($_SESSION['session'])) {
            require_once __DIR__ . "/models/User.php";
            $profile  = unserialize($_SESSION['profile']);
            $session  = $_SESSION['session'];

            /**
             * Opcional (recomendado): lista blanca de plantillas de rol.
             * Así también evitas concatenar $session en los require.
             */
            $roleTemplates = [
                'admin' => [
                    'header' => __DIR__ . '/views/roles/admin/header.view.php',
                    'footer' => __DIR__ . '/views/roles/admin/footer.view.php',
                ],
                'user' => [
                    'header' => __DIR__ . '/views/roles/user/header.view.php',
                    'footer' => __DIR__ . '/views/roles/user/footer.view.php',
                ],
                // Agrega aquí el resto de roles...
            ];

            if (!isset($roleTemplates[$session])) {
                // Si el rol no es válido, puedes redirigir o usar un rol por defecto
                header("Location: ?");
                ob_end_flush();
                exit;
            }

            require_once $roleTemplates[$session]['header'];
            call_user_func([$controller, $action]);
            require_once $roleTemplates[$session]['footer'];

        } else {
            header("Location: ?");
        }
    } else {
        header("Location: ?");
    }

    ob_end_flush();
?>
