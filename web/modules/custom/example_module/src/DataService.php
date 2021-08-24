<?php

namespace Drupal\example_module;

/**
 * Class DataService.
 */
class DataService
{

  private $connection;

  /**
   * Constructs a new DataService object.
   */
  public function __construct()
  {
    $this->connection = \Drupal::service('database');
  }


  /**
   * Valida si un número de identificacion pasado por `$idNumber` ya está registrado en base de datos.
   * Retorna FALSE si existe y TRUE en caso contrario
   *
   * @param string $idNumber
   * @return boolean
   */
  public function validateId($id_number)
  {
    $result = $this->connection->query('select * from {example_users} where identificacion = :identificacion', [
      'identificacion' => $id_number
    ])->fetchAssoc();


    if (empty($result)) {
      return TRUE;
    }

    if (count($result) > 0) {
      return FALSE;
    }

    return TRUE;
  }

  public function validateBirthDate($date)
  {
    $now = time();

    if (strtotime($date) > $now) {
      return FALSE;
    }

    return TRUE;
  }
}
