<?php

namespace GWP;
use DateTime;
use PDOException;

abstract class AbstractModel {
  const TYPE_INT = 'int';
  const TYPE_FLOAT = 'float';
  const TYPE_STRING = 'string';

	protected int $id = -1;

  public static function read(int $id = -1, string $where = '', string $join = ''): array	{
    global $db;

    $sql = '';
    try {
      $sql = 'SELECT * FROM ' . self::tablename();

      if($join != '') {
        $sql .= " $join";
      }

      if($id !== -1) {
        $sql .= " WHERE id = $id";
      } else if($where != '') {
        $sql .= " WHERE $where";
      }

      return $db->query($sql)->fetchAll();
    } catch (PDOException $e) {
      debug_to_logFile("SELECT ERROR:");
      debug_to_logFile("SQL: $sql");
      debug_to_logFile("Error Message: " . $e->getMessage());
      return [];
    }
  }

  public static function tablename(): string {
    $class = get_called_class();
    if (defined($class . '::TABLENAME')) {
      return $class::TABLENAME;
    }
    return '';
  }

  public static function schema(): array {
		$class = get_called_class();
		if (defined($class . '::SCHEMA')) {
			return $class::SCHEMA;
		}
		return [];
	}

	public function fillFieldsAfterError(array $parameter): void {
		foreach ($parameter as $key => $value) {
			if(property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
	}
}