<?php

namespace GWP;
use DateTime;
use PDOException;

abstract class AbstractModel {
  const TYPE_INT = 'int';
  const TYPE_FLOAT = 'float';
  const TYPE_STRING = 'string';

	protected int $id = -1;
  protected $data = [];  // data which goes into the table

  // insert an entity to the database
  public static function create(array $parameter, array &$errors): int {
    $db = $GLOBALS['db'];

    $sql = '';
    try {
      $sql = 'INSERT INTO ' . self::tablename() . ' (';
      $valueString = ' VALUES (';

      foreach (self::schema() as $key => $schemaOptions) {
        if(array_key_exists($key, $parameter)) {
          $sql .= '`' . $key . '`,';
	        if($parameter[$key] === null) {
		        $valueString .= "NULL,";
	        } else {
		        $valueString .= $db->quote($parameter[$key]) . ',';
	        }
        }
      }

      $sql = trim($sql, ',');
      $valueString = trim($valueString, ',');
      $sql .= ')' . $valueString . ');';

      $statement = $db->prepare($sql);
      $db->beginTransaction();
      $statement->execute();
			$lastInsertedId = $db->lastInsertId();
      $db->commit();
      return $lastInsertedId;
    } catch (PDOException $e) {
      $errors[] = 'Fehler bei der Datensatzerstellung - Klasse: ' . get_called_class();
      $errors[] = "Error SQL: $sql";
      $errors[] = "Error Message: " . $e->getMessage();
      foreach ($errors as $error) {
        error_to_logFile($error);
      }
      $db->rollBack();
			return -1;
    }
  }

  // update an entity in the database
  public function update(array $parameter, array &$errors): void {
    $db = $GLOBALS['db'];

    $sql = '';
    try {
      $sql = 'UPDATE ' . self::tablename() . ' SET ';

      foreach (self::schema() as $key => $schemaOptions) {
        if(array_key_exists($key, $parameter)) {
					if($parameter[$key] === null) {
						$value = "NULL";
					} else {
						$value = $db->quote($parameter[$key]);
					}
          $sql .= "`$key` = $value,";
        }
      }

      $sql = trim($sql, ',');
      $sql .= " WHERE id = {$this->id}";

      $statement = $db->prepare($sql);
      $db->beginTransaction();
      $statement->execute();
      $db->commit();
    } catch (PDOException $e) {
      $errors[] = 'Fehler beim Updaten eines Datensatzes - Klasse: ' . get_called_class();
      $errors[] = "Error SQL: $sql";
      $errors[] = "Error Message: " . $e->getMessage();
      foreach ($errors as $error) {
        error_to_logFile($error);
      }
      $db->rollBack();
    }
  }

  public function delete(array &$errors = []): bool {
    $db = $GLOBALS['db'];
    $sql = '';
    try {
      $sql = 'DELETE FROM ' . self::tablename() . " WHERE id = '{$this->id}'";

      $db->beginTransaction();
      $db->exec($sql);
      $db->commit();
      return true;
    } catch (PDOException $e) {
      $errors[] = 'Fehler beim Löschen - Klasse: ' . get_called_class();
      $errors[] = "Error SQL: $sql";
      $errors[] = "Error Message: " . $e->getMessage();
      foreach ($errors as $error) {
        error_to_logFile($error);
      }
      $db->rollBack();
      return false;
    }
  }

  public function validate(array &$errors): void {
    foreach ($this->schema as $key => $schemaOptions) {
      if (isset($this->$key) && is_array($schemaOptions)) {
        $this->validateValue($this->data[$key], $schemaOptions, $errors);
      }
    }
  }

  // check the if the value is correct
  protected function validateValue(&$value, array &$schemaOptions, array &$errors): void {
    $type = $schemaOptions['type'];

    switch ($type) {
      case self::TYPE_INT:
      case self::TYPE_FLOAT:
        if(!is_numeric($value)) {
          $errors[] = $schemaOptions['translation'] . ': Die Eingabe ist keine Zahl!';
        }
        break;
      case self::TYPE_STRING:
        {
          if(isset($schemaOptions['min']) && mb_strlen($value) < $schemaOptions['min']) {
            $errors[] = $schemaOptions['translation'] . ': Eingabe muss mindestens aus ' . $schemaOptions['min'] . ' Zeichen bestehen!';
          }

          if(isset($schemaOptions['max']) && mb_strlen($value) > $schemaOptions['max']) {
            $errors[] = $schemaOptions['translation'] . ': Eingabe darf maximal aus ' . $schemaOptions['max'] . ' Zeichen bestehen!';
          }
        }
        break;
    }
  }

  // gives the tablename from the class
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

	public static function read(int $id = -1, string $where = '', string $join = ''): array	{
		$db = $GLOBALS['db'];

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
			error_to_logFile("SELECT ERROR:");
			error_to_logFile("SQL: $sql");
			error_to_logFile("Error Message: " . $e->getMessage());
			return [];
		}
	}

  public static function query(string $sql, array &$errors = [], bool $fetchAll = false): array {
    global $db;

    try {
      if($fetchAll) {
        $result = $db->query($sql)->fetchAll();
      } else {
        $result = $db->query($sql)->fetch();
      }

      if($result === false) {
        $errors[] = "Fehler beim ausführen des SQL-Statements";
        $errors[] = "SQL: $sql";
        return [];
      }

      return $result;
    } catch (PDOException $e) {
      $errors[] = "SQL: $sql";
      $errors[] = 'Select statement failed: ' . $e->getMessage();
      $errors[] = "Error Message: " . $e->getMessage();
      return [];
    }
  }

  public static function execute(string $sql, array &$errors = []): bool {
    global $db;

    try {
      return $db->exec($sql);
    } catch (PDOException $e) {
      $errors[] = "SQL: $sql";
      $errors[] = 'Statement failed: ' . $e->getMessage();
      $errors[] = "Error Message: " . $e->getMessage();
      return false;
    }
  }

  public static function getParameterFromPOST(array &$errors = []): array {
    $parameter = [];
    foreach (self::schema() as $name => $options) {

      if (!array_key_exists('hasPOST', $options)) {
        continue;
      }

      if(array_key_exists($name, $_POST) && $_POST[$name] !== '') {
        if(array_key_exists('isCheckBox', $options)) {
          $parameter[$name] = isset($_POST[$name]) ? 1 : 0;
        } else {
          $parameter[$name] = $_POST[$name];
        }
      } else if(array_key_exists('default', $options)) {
        $parameter[$name] = $options['default'];
      } else {
        if(array_key_exists('isCheckBox', $options)) {
          $parameter[$name] = 0;
        } else {
          $errors[] = "Das Attribut $name hat keinen POST-Wert und keinen Default-Wert";
        }
      }
    }

    return $parameter;
  }

	public function fillFieldsAfterError(array $parameter): void {
		foreach ($parameter as $key => $value) {
			if(property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
	}

  protected static function validateDate(string $date, string $format = 'Y-m-d'): bool	{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }
}