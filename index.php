<?php
    ob_start();
    session_start();

    require_once __DIR__ . "/models/DataBase.php";

    // 🔐 Lista blanca de controladores permitidos
    $allowedControllers = [
        "Landing",
        "Login",
        "Dashboard",
        "User",
        "Role",
        "Company",
        "Product"
    ];

    // 🔒 Sanitizar parámetro
    $controller = $_GET['c'] ?? "Landing";

    // 🔐 Validar nombre contra whitelist
    if (!in_array($controller, $allowedControllers, true)) {
        $controller = "Landing";  // fallback seguro
    }

    // Rutas
    $route_controller = __DIR__ . "/controllers/" . $controller . ".php";

    if (file_exists($route_controller)) {
        $view = $controller;
        require_once $route_controller;

        // Instanciar controlador
        if (class_exists($controller)) {
            $controllerInstance = new $controller();

            $action = $_GET['a'] ?? 'main';

            // Solo métodos públicos del controlador
            if (!method_exists($controllerInstance, $action)) {
                $action = 'main';
            }

            // Vistas públicas
            if ($view === 'Landing' || $view === 'Login') {
                require_once __DIR__ . "/views/company/header.view.php";
                call_user_func([$controllerInstance, $action]);
                require_once __DIR__ . "/views/company/footer.view.php";

            } elseif (!empty($_SESSION['session'])) {

                require_once __DIR__ . "/models/User.php";

                $session = $_SESSION['session'];

                require_once __DIR__ . "/views/roles/" . $session . "/header.view.php";
                call_user_func([$controllerInstance, $action]);
                require_once __DIR__ . "/views/roles/" . $session . "/footer.view.php";

            } else {
                header("Location:?");
                exit;
            }
        }

    } else {
        header("Location:?");
        exit;
    }

    ob_end_flush();
?>
