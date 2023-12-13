<?php

namespace GWP;

require_once MODELS_PATH . 'AbstractModel.php';

class User extends AbstractModel {
  const TABLENAME = '`user`';

  public static function getAllUsers(): array {
    return self::read();
  }
}
