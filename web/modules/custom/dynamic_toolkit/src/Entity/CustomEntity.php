<?php

namespace Drupal\dynamic_toolkit\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the Custom entity.
 *
 * @ContentEntityType(
 *   id = "custom_entity",
 *   label = @Translation("Custom Entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dynamic_toolkit\CustomEntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\dynamic_toolkit\Form\CustomEntityForm",
 *       "add" = "Drupal\dynamic_toolkit\Form\CustomEntityForm",
 *       "edit" = "Drupal\dynamic_toolkit\Form\CustomEntityForm",
 *       "delete" = "Drupal\dynamic_toolkit\Form\CustomEntityDeleteForm",
 *     },
 *     "access" = "Drupal\dynamic_toolkit\CustomEntityAccessControlHandler",
 *   },
 *   base_table = "custom_entity",
 *   admin_permission = "administer custom entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/custom_entity/{custom_entity}",
 *     "add-form" = "/custom_entity/add",
 *     "edit-form" = "/custom_entity/{custom_entity}/edit",
 *     "delete-form" = "/custom_entity/{custom_entity}/delete",
 *     "collection" = "/custom_entity",
 *   },
 *   field_ui_base_route = "custom_entity.settings",
 * )
 */
class CustomEntity extends ContentEntityBase implements ContentEntityInterface {

  use EntityChangedTrait;
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Custom entity.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Custom entity.'))
      ->setReadOnly(TRUE);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setDescription(t('The label of the Custom entity.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('Default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['field_reference_node'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Referenced Node'))
      ->setDescription(t('A reference to a basic_page node.'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default:node')
      ->setSetting('handler_settings', [
        'target_bundles' => ['page' => 'page'],
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference_label',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
