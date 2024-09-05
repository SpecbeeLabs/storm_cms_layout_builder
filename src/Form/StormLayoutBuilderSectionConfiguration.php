<?php

namespace Drupal\storm_cms_layout_builder\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Storm layout builder settings for this site.
 */
class StormLayoutBuilderSectionConfiguration extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'storm_cms_layout_builder.settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['storm_cms_layout_builder.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('storm_cms_layout_builder.settings');

    $form['enable_section_attributes'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Section Attributes'),
      '#default_value' => $config->get('enable_section_attributes') ?? 0,
    ];

    $form['enable_title_attributes'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Title Attributes'),
      '#default_value' => $config->get('enable_title_attributes') ?? 0,
    ];

    $form['bg_show_config'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Background Section'),
      '#default_value' => $config->get('bg_show_config') ?? 0,
    ];
    $form['background'] = [
      '#type' => 'details',
      '#title' => $this->t('Background Configurations'),
      '#open' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="bg_show_config"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['background']['background_colors'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Color palette'),
      '#description' => $this->t('<p>Enter one value per line, in the format <b>key|label</b> where <em>key</em> is the CSS class name (without the perfixed period CSS selector), and <em>label</em> is the human readable name of the background.</p>'),
      '#default_value' => $config->get('background_colors'),
    ];

    $form['padding_show_config'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Padding Section'),
      '#default_value' => $config->get('padding_show_config') ?? 0,
    ];
    $form['padding'] = [
      '#type' => 'details',
      '#title' => $this->t('Padding'),
      '#open' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="padding_show_config"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['padding']['markup'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Enter one value per line, in the format <b>key|label</b> where <em>key</em> is the CSS class name (without the perfixed period CSS selector), and <em>label</em> is the human readable name.') . '</p>',
    ];
    $form['padding']['padding_top'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Padding top'),
      '#default_value' => $config->get('padding_top'),
    ];
    $form['padding']['padding_bottom'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Padding bottom'),
      '#default_value' => $config->get('padding_bottom'),
    ];
    $form['padding']['padding_left'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Padding left'),
      '#default_value' => $config->get('padding_left'),
    ];
    $form['padding']['padding_right'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Padding right'),
      '#default_value' => $config->get('padding_right'),
    ];

    $form['spacing_show_config'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Spacing Section'),
      '#default_value' => $config->get('spacing_show_config') ?? 0,
    ];
    $form['spacing'] = [
      '#type' => 'details',
      '#title' => $this->t('Spacing'),
      '#open' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="spacing_show_config"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['spacing']['markup'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Enter one value per line, in the format <b>key|label</b> where <em>key</em> is the CSS class name (without the perfixed period CSS selector), and <em>label</em> is the human readable name.') . '</p>',
    ];
    $form['spacing']['spacing_top'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Spacing top'),
      '#default_value' => $config->get('spacing_top'),
    ];
    $form['spacing']['spacing_bottom'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Spacing bottom'),
      '#default_value' => $config->get('spacing_bottom'),
    ];
    $form['spacing']['spacing_left'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Spacing left'),
      '#default_value' => $config->get('spacing_left'),
    ];
    $form['spacing']['spacing_right'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Spacing right'),
      '#default_value' => $config->get('spacing_right'),
    ];

    $form['theme_show_config'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Theme Section'),
      '#default_value' => $config->get('theme_show_config') ?? 0,
    ];
    $form['theme'] = [
      '#type' => 'details',
      '#title' => $this->t('Theme'),
      '#open' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="theme_show_config"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['theme']['markup'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Enter the classes which will allow site builders to select from a list of styles to apply to layout builder sections.') . '</p>
      <p>' . $this->t('Enter one value per line, in the format <b>key|label</b> where <em>key</em> is the CSS class name (without the perfixed period CSS selector), and <em>label</em> is the human readable name.') . '</p>',
    ];
    $form['theme']['styles'] = [
      '#type' => 'textarea',
      '#default_value' => $config->get('styles'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $ignore = [
      'submit',
      'form_build_id',
      'form_token',
      'form_id',
      'op',
    ];
    $configuration = $this->config('storm_cms_layout_builder.settings');
    foreach ($form_state->getValues() as $key => $value) {
      if (!in_array($key, $ignore)) {
        if (!is_array($value)) {
          $configuration->set($key, trim($value));
        }
        else {
          $configuration->set($key, $value);
        }
      }
    }
    $configuration->save();

    parent::submitForm($form, $form_state);
  }

}
