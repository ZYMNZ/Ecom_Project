<?php
    class LoginController {
        function route() {
            global $action;
            global $controllerPrefix;
            if($action == "login") {
                $this->render($action);
            }
            else {

            }
        }
        function render($action, $dataToSend = []) {
            extract($dataToSend);

            include_once "Views/Login/$action.php";
        }
    }

?>