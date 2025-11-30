<?php
    require_once "models/User.php";

    class Login {

        // Controlador Principal
        public function main() {

            if ($_SERVER['REQUEST_METHOD'] == 'GET') {

                if (empty($_SESSION['session'])) {
                    $message = "";
                    require_once "views/company/login.view.php";
                } else {
                    header("Location:?c=Dashboard");
                }
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $profile = new User(
                    $_POST['user_email'],
                    $_POST['user_pass']
                );

                $profile = $profile->login();

                if ($profile) {
                    $active = $profile->getUserState();

                    if ($active != 0) {

                        // Rol en sesión
                        $_SESSION['session'] = $profile->getRolName();

                        // En vez de serializar el objeto completo, guardamos solo los datos necesarios
                        $_SESSION['profile'] = [
                            'rol_code'      => $profile->getRolCode(),
                            'rol_name'      => $profile->getRolName(),
                            'user_code'     => $profile->getUserCode(),
                            'user_name'     => $profile->getUserName(),
                            'user_lastname' => $profile->getUserLastName(),
                            'user_id'       => $profile->getUserId(),
                            'user_email'    => $profile->getUserEmail(),
                            'user_state'    => $profile->getUserState(),
                        ];

                        header("Location:?c=Dashboard");

                    } else {

                        $message = "El Usuario NO está activo";
                        require_once "views/company/login.view.php";

                    }

                } else {

                    $message = "Credenciales incorrectas ó el Usuario NO existe";
                    require_once "views/company/login.view.php";

                }
            }

        }
    }
?>
