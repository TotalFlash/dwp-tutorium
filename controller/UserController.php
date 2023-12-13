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
    $this->html = User::getLoginViewHTML($it, $errors);
  }

  public function list(): void {
    $it = $this->createIntegratedTemplate();
    $it->loadTemplatefile('list.html');

    $users = User::getAllUsers();

    foreach($users as $user) {
    $it->setCurrentBlock('userlist');
      $placeholder = [
        'userId' => $user['id'],
        'name' => $user['username'],
        'createdAt' => $user['created_at']
      ];
      $it->setVariable($placeholder);

      for($i = 0; $i < 5; $i++) {
        $it->setCurrentBlock('number');
        $placeholderNumber = [
          'favoritNumber' => rand(0, 5000)
        ];
        $it->setVariable($placeholderNumber);
        $it->parse('number');
      }

      $it->parse('userlist');
    }

    $this->html = $it->get();
  }
}
