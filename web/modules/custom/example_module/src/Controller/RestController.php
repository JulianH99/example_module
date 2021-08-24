<?php

namespace Drupal\example_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class RestController.
 */
class RestController extends ControllerBase
{

  /**
   * Get.
   *
   * @return string
   *   Return Hello string.
   */
  public function get()
  {

    $connection = \Drupal::service('database');
    $result = $connection->query('select * from {example_users}');
    $records = [];

    if (empty($result)) {
      return new JsonResponse([]);
    }

    foreach ($result as $record) {
      $records[] = $record;
    }

    return new JsonResponse($records);
  }

  public function post()
  {
    $connection = \Drupal::service('database');
    $json = json_decode(\Drupal::request()->getContent(), TRUE);
    $data_service = \Drupal::service('example_module.data');


    if (!($json['nombre'] && $json['identificacion'] && $json['cargo'])) {
      return new JsonResponse(['error' => 'Nombre, identificacion y cargo son campos requeridos'], 400);
    }

    if (!$data_service->validateId($json['identificacion'])) {
      return new JsonResponse(['error' => 'El número de identificación ya se encuentra registrado'], 400);
    }

    if (!is_numeric($json['identificacion'])) {
      return new JsonResponse(['error' => 'El número de identificación debe ser númerico'], 400);
    }



    if (!in_array($json['cargo'], ['administrador', 'webmaster', 'desarrollador'])) {
      return new JsonResponse([
        'error' => 'El cargo debe ser administrador, webmaster o desarrollador'
      ], 400);
    }


    $connection->insert('example_users')
      ->fields([
        'nombre' => $json['nombre'],
        'identificacion' => $json['identificacion'],
        'fecha_nacimiento' => $json['fecha_nacimiento'] ? date('y-m-d', strtotime($json['fecha_nacimiento'])) : '',
        'cargo' => $json['cargo'],
        'estado' => $json['cargo'] === 'administrador' ? 1 : 0
      ])
      ->execute();

    return new JsonResponse([
      'message' => 'Datos correctamente guardados'
    ]);
  }

  public function put($id)
  {

    $connection = \Drupal::service('database');
    $json = json_decode(\Drupal::request()->getContent(), TRUE);
    $data_service = \Drupal::service('example_module.data');


    if (!($json['nombre'] && $json['identificacion'] && $json['cargo'])) {
      return new JsonResponse(['error' => 'Nombre, identificacion y cargo son campos requeridos'], 400);
    }

    if (!is_numeric($json['identificacion'])) {
      return new JsonResponse(['error' => 'El número de identificación debe ser númerico'], 400);
    }

    if (!in_array($json['cargo'], ['administrador', 'webmaster', 'desarrollador'])) {
      return new JsonResponse([
        'error' => 'El cargo debe ser administrador, webmaster o desarrollador'
      ], 400);
    }

    $json['fecha_nacimiento'] = $json['fecha_nacimiento'] ? date('y-m-d', strtotime($json['fecha_nacimiento'])) : '';

    $connection->update('example_users')
      ->condition('id', $id)
      ->fields($json)
      ->execute();

    return new JsonResponse([
      'message' => 'Datos correctamente actualizados'
    ]);
  }

  public function delete($id)
  {
    $connection = \Drupal::service('database');

    $connection->delete('example_users')
      ->condition('id', $id)
      ->execute();

    return new JsonResponse([
      'message' => 'Dato eliminado correctamente'
    ]);
  }
}
