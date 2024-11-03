<?php

namespace Drupal\dynamic_toolkit;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Provides a service to fetch user and node information from the current route.
 */
class CustomUserNodeService {

  /**
   * The current route match service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new CustomUserNodeService instance.
   *
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The current route match service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   */
  public function __construct(CurrentRouteMatch $current_route_match, EntityTypeManagerInterface $entityTypeManager) {
    $this->currentRouteMatch = $current_route_match;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Fetches the user from the current route and returns their label.
   *
   * @return string|null
   *   The user label or null if no user is found.
   */
  public function fetchUserByRoute() {
    $user = $this->currentRouteMatch->getParameter('user');
    if ($user) {
      return $user->getDisplayName();
    }
    return NULL;
  }

  /**
   * Fetches the node from the current route and returns its title.
   *
   * @return string|null
   *   The node title or null if no node is found.
   */
  public function fetchNodeByRoute() {
    $node = $this->currentRouteMatch->getParameter('node');
    if ($node) {
      return $node->getTitle();
    }
    return NULL;
  }

}
