<?php

namespace Drupal\dynamic_toolkit\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the Custom entity add/edit forms.
 */
class CustomEntityForm extends ContentEntityForm {

  /**
   * Builds the form for the custom entity.
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Initialize a new entity if one isn't provided.
    if (!$this->entity) {
      $this->entity = $this->entityTypeManager->getStorage('custom_entity')->create();
    }

    // Define the fields in the form.
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#required' => TRUE,
    ];

    $form['field_reference_node'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Referenced Node'),
      '#target_type' => 'node',
      '#selection_settings' => [
        'target_bundles' => ['page' => 'page'],
      ],
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Saves the custom entity after submission.
   *
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    // Add null check for label.
    $label = $entity->label() ?? $this->t('Default');

    if ($status) {
      $this->messenger()->addMessage($this->t('Saved the %label Custom entity.', [
        '%label' => $entity->label(),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('The %label Custom entity was not saved.', [
        '%label' => $entity->label(),
      ]));
    }

    $form_state->setRedirect('entity.custom_entity.collection');
  }

  /**
   * Handles form submission and creates the custom entity.
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $entity = \Drupal::entityTypeManager()->getStorage('custom_entity')->create([
      // Add null check for label if NULL.
      'label' => $values['label'] ?? $this->t('Default'),
      'field_reference_node' => $values['field_reference_node'],
    ]);

    $entity->save();

    $this->messenger()->addMessage($this->t('Custom entity saved successfully.'));

    $form_state->setRedirect('<front>');
  }

}
