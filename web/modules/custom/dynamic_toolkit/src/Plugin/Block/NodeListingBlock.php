<?php

namespace Drupal\dynamic_toolkit\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * Provides a custom 'NodeListing' block that lists published nodes by type.
 *
 * @Block(
 *  id = "node_listing_block",
 *  admin_label = @Translation("Node Listing Block"),
 * )
 */
class NodeListingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Database connection service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * Constructs a new NodeListingBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->entityTypeManager = $entityTypeManager;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Builds the node listing block content.
   *
   * @return array
   *   Render array for the block content.
   */
  public function build() {
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    $rows = [];

    if (!empty($node_types)) {
      foreach ($node_types as $node_type) {
        if ($this->entityTypeManager->getStorage('node')) {
          // Query to retrieve published nodes of the current type.
          $nids = $this->entityTypeManager->getStorage('node')
            ->getQuery()
            ->condition('type', $node_type->id())
            ->condition('status', 1)
            ->accessCheck(TRUE)
            ->execute();

          // Load node entities and prepare data for rendering.
          if (!empty($nids)) {
            $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
            foreach ($nodes as $node) {
              $rows[] = [
                'title' => $node->getTitle(),
                'type' => $node_type->label(),
              ];
            }
          }
        }
      }
    }

    $build['content'] = [
      '#theme' => 'node_listing',
      '#nodes' => $rows,
      '#empty' => $this->t('No content available.'),
      '#attached' => ['library' => ['dynamic_toolkit/dynamic_toolkit_node_listing']],
    ];

    return $build;
  }

  /**
   * Controls access to the block based on user permissions.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account object.
   *
   * @return \Drupal\Core\Access\AccessResult
   *    The access result.
   */
  protected function blockAccess(AccountInterface $account) {
    if ($account->hasPermission('view node listing block')) {
      return AccessResult::allowed();
    }
    return AccessResult::forbidden();
  }
}
