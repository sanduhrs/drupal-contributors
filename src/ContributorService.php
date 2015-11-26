<?php

/**
 * @file
 * Contains \Drupal\contributors\ContributorService.
 */

namespace Drupal\contributors;


use Drupal\Core\Entity\EntityInterface;
use Drupal\user\Entity\User;

/**
 * Class ContributorService.
 *
 * @package Drupal\contributors
 */
class ContributorService {

  /**
   * The contributors.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   A Drupal entity.
   *
   * @return array
   *   An array of contributor accounts.
   */
  public static function getContributors(EntityInterface $entity) {
    $database = \Drupal::database();
    $rows = $database->query(
      "SELECT DISTINCT revision_uid as uid FROM {node_revision} nr WHERE nr.nid = :nid",
      array(
        ':nid' => $entity->id(),
      )
    );

    $uids = [];
    foreach ($rows as $row) {
      $uids[] = $row->uid;
    }

    $users = User::loadMultiple($uids);
    return array(
      'accounts' => $users,
      'entity' => $entity,
    );
  }

  /**
   * Last updated by.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   A Drupal entity.
   *
   * @return array
   *   An array of last updated info.
   */
  public static function lastUpdatedBy(EntityInterface $entity) {
    $database = \Drupal::database();
    $rows = $database->query(
      "SELECT revision_uid as uid, revision_timestamp as timestamp FROM {node_revision} nr WHERE nr.nid = :nid ORDER BY timestamp DESC LIMIT 1",
      array(
        ':nid' => $entity->id(),
      )
    );

    foreach ($rows as $row) {
      $uid = $row->uid;
      $timestamp = $row->timestamp;
    }

    $users = User::loadMultiple([$uid]);
    return array(
      'account' => array_shift($users),
      'timestamp' => $timestamp,
      'entity' => $entity,
    );
  }

}
