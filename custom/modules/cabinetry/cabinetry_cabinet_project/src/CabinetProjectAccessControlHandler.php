<?php

namespace Drupal\cabinetry_cabinet_project;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the cabinet project entity.
 *
 * @see \Drupal\cabinetry_cabinet_project\Entity\CabinetProject.
 */
class CabinetProjectAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view cabinet project');

      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit cabinet project');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete cabinet project');
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add cabinet project');
  }

}
