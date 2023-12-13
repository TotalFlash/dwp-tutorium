<?php
namespace GWP;

require_once CONTROLLER_PATH . 'AbstractController.php';
require_once MODELS_PATH . 'User.php';

class UserController extends AbstractController {

  ########################## Login/Logout ####################################
  public function login(): void {
    $it = $this->createIntegratedTemplate();
    $it->loadTemplatefile('login.html');

    $errors = [];
    if(isset($_POST['username'], $_POST['password'])) {
      if($_POST['username'] === '') {
        $errors[] = "Nutzername vergessen";
      }

      if($_POST['password'] === '') {
        $errors[] = "Password vergessen";
      }

      if(empty($errors)) {
        if($_POST['username'] == 'asdf' && $_POST['password'] == 'asdf') {
          $_SESSION['loggedIn'] = true;

          sendHeaderByControllerAndAction('User', 'dashboard');
        } else {
          $errors[] = "Nutzername und Passwort falsch";
        }
      }
    }

    $this->html = User::getLoginViewHTML($it, $errors);
  }

  public function logout(): void {
    $this->html = "LOGOUT PAGE";
  }

  public function dashboard(): void {
    $this->html = "Landing PAGE";
  }
}
