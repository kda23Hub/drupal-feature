<?php

namespace Drupal\dynamic_toolkit\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Database\Connection;
use Drupal\node\Entity\NodeType;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * Provides a 'NodeListing' block.
 *
 * @Block(
 *  id = "node_listing_block",
 *  admin_label = @Translation("Node Listing Block"),
 * )
 */
class NodeListingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $database;
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
   * {@inheritdoc}
   */
  public function build() {
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    $rows = [];

    foreach ($node_types as $node_type) {
      $nids = $this->entityTypeManager->getStorage('node')
        ->getQuery()
        ->condition('type', $node_type->id())
        ->condition('status', 1)
        ->accessCheck(TRUE)
        ->execute();

      $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

      foreach ($nodes as $node) {
        $rows[] = [
          'title' => $node->getTitle(),
          'type' => $node_type->label(),
        ];
      }
    }

    $header = [
      'title' => $this->t('Title'),
      'type' => $this->t('Type'),
    ];

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No content available.'),
      '#attributes' => ['class' => ['node-listing-block']],
      '#attached' => ['library' => ['dynamic_toolkit/node_listing_block']],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    if ($account->hasPermission('view node listing block')) {
      return AccessResult::allowed();
    }
    return AccessResult::forbidden();
  }
}
