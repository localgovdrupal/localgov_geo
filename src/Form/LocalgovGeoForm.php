<?php

namespace Drupal\localgov_geo\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the geo entity edit forms.
 */
class LocalgovGeoForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New geo %label has been created.', $message_arguments));
      $this->logger('localgov_geo')->notice('Created new geo %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The geo %label has been updated.', $message_arguments));
      $this->logger('localgov_geo')->notice('Updated new geo %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.localgov_geo.canonical', ['localgov_geo' => $entity->id()]);
  }

}
