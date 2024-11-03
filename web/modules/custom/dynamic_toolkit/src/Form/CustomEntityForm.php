<?php

namespace Drupal\dynamic_toolkit\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Custom entity edit forms.
 */
class CustomEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

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
}
