<?php

namespace Drupal\dynamic_toolkit;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a list builder for Custom entities.
 *
 * This class is unused as the custom entity list is displayed by
 * view, but serves as a fallback if the view is unavailable, not
 * configured correctly, flexibility and extensibility.
 */
class CustomEntityListBuilder extends EntityListBuilder {

  /**
   * Builds the header for the entity list table.
   *
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('ID');
    return $header + parent::buildHeader();
  }

  /**
   * Builds a row in the entity list table.
   *
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->toLink();
    $row['id'] = $entity->id();
    return $row + parent::buildRow($entity);
  }

}
