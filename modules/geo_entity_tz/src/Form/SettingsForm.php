<?php

declare(strict_types=1);

namespace Drupal\geo_entity_tz\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Geofield Timezone settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'geo_entity_tz_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['geo_entity_tz.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#description' => 'API enabled user account name. Registered <a href="http://www.geonames.org/login">a username on Geonames</a>. You will then receive an email with a confirmation link and after you have confirmed the email you can enable your account for the webservice on your account page.',
      '#default_value' => $this->config('geo_entity_tz.settings')->get('username'),
      '#required' => TRUE,
    ];
    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token'),
      '#description' => '<a href="http://www.geonames.org/commercial-webservices.html">Premium webservices</a> accounts may have a token.',
      '#default_value' => $this->config('geo_entity_tz.settings')->get('token'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('geo_entity_tz.settings')
      ->set('username', $form_state->getValue('username'))
      ->save();
    $this->config('geo_entity_tz.settings')
      ->set('token', $form_state->getValue('token') ?: NULL)
      ->save();
    parent::submitForm($form, $form_state);
  }

}
