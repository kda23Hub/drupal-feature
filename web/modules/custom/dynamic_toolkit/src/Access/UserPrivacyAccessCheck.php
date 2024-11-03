<?php

namespace Drupal\dynamic_toolkit\Access;

use Drupal\Core\Access\AccessCheckInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;
use Symfony\Component\Routing\Route;

/**
 * Provides a custom access check for user privacy settings.
 */
class UserPrivacyAccessCheck implements AccessCheckInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a UserPrivacyAccessCheck object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Checks if the access check applies to the given route.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route to check.
   *
   * @return bool
   *   TRUE if the access check applies, FALSE otherwise.
   */
  public function applies(Route $route) {
    return $route->getPath() === '/user/{user}';
  }

  /**
   * Checks access to the user profile based on privacy settings.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   * @param \Drupal\user\UserInterface $user
   *   The user entity.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, ?UserInterface $user = NULL) {
    // Deny anonymous users.
    if ($account->isAnonymous()) {
      return AccessResult::forbidden();
    }

    // Allow superadmin.
    if ($account->id() == 1) {
      return AccessResult::allowed();
    }

    // Deny if target user is missing.
    if (!$user) {
      return AccessResult::forbidden();
    }

    // Deny if profile is private and viewer is not the owner.
    $privacy_option = $user->get('field_privacy_option')->value;
    if ($privacy_option === 'keep_private' && $account->id() != $user->id()) {
      return AccessResult::forbidden();
    }

    return AccessResult::allowed();
  }

}
