<?php

namespace GWP;

require_once MODELS_PATH . 'AbstractModel.php';

class User extends AbstractModel {
  const TABLENAME = '`user`';

	protected string $roleId;
	protected string $username;
	protected string $password;
	protected string $isLocked;
	protected string $createdAt;
	protected string $updatedAt;

  protected const SCHEMA = [
    'id' => ['type' => AbstractModel::TYPE_INT],
    'roleId' => ['type' => AbstractModel::TYPE_INT],
    'username' => ['type' => AbstractModel::TYPE_STRING, 'min' => 2,   'max' => 20],
    'password' => ['type' => AbstractModel::TYPE_STRING, 'min' => 8,   'max' => 60],
    'isLocked' => ['type' => AbstractModel::TYPE_INT],
    'createdAt' => ['type' => AbstractModel::TYPE_STRING],
    'updatedAt' => ['type' => AbstractModel::TYPE_STRING]
  ];

	public function __construct(int $id = -1) {

	}

  ###########################
  ### HTML VIEW FUNCTIONS
  ###########################
	public static function getLoginViewHTML(\HTML_Template_IT &$it, array $errors = []): string {

		$usernameLabel = HTMLGenerator::getLabel('username', 'Nutzername');
		$passwordLabel = HTMLGenerator::getLabel('password', 'Passwort');

		$placeholder = [
			'username' => HTMLGenerator::getInputField('username', 'username', $_POST['username'] ?? '', 'text', true, 'Nutzername', $usernameLabel),
			'password' => HTMLGenerator::getInputField('password', 'password', '', 'password', true, 'Passwort', $passwordLabel),
			'submitButton' => HTMLGenerator::createSubmitButton('login', 'login', 'Login<i class="fas fa-sign-in-alt" aria-hidden="true"></i>', 'User', 'login')
		];
		$it->setVariable($placeholder);

    HTMLGenerator::fillErrorMessages($it, $errors);

		$it->parseCurrentBlock();
		return $it->get();
	}
}
