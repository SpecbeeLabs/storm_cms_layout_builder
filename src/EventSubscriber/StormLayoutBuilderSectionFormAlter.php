<?php

namespace Drupal\storm_cms_layout_builder\EventSubscriber;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\core_event_dispatcher\Event\Form\FormAlterEvent;
use Drupal\core_event_dispatcher\FormHookEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The custom form alter event subscriber.
 */
class StormLayoutBuilderSectionFormAlter implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * The config object.
   *
   * @var Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The route object.
   *
   * @var Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * The entity type manager service..
   *
   * @var Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * StormLayoutBuilderSectionFormAlter constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $routeMatch
   *   The route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager object.
   */
  public function __construct(ConfigFactoryInterface $configFactory, CurrentRouteMatch $routeMatch, EntityTypeManagerInterface $entityTypeManager) {
    $this->configFactory = $configFactory;
    $this->routeMatch = $routeMatch;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return [
      FormHookEvents::FORM_ALTER => 'alterForm',
    ];
  }

  /**
   * Alter form.
   */
  public function alterForm(FormAlterEvent $event) {
    $form = &$event->getForm();
    if ($form['#form_id'] == 'layout_builder_configure_section') {
      // $form_state = $event->getFormState();
      $config = $event->getFormState()->getFormObject()->getCurrentLayout()->getConfiguration();
      $styleSettingConfigs = $this->configFactory->get('storm_cms_layout_builder.settings');

      // Section css attributes.
      if ($styleSettingConfigs->get('enable_section_attributes')) {
        $form['layout_settings']['section_attributes'] = [
          '#type' => 'details',
          '#title' => $this->t('Section Attributes'),
          '#tree' => TRUE,
        ];
        $form['layout_settings']['section_attributes']['id'] = [
          '#type' => 'textfield',
          '#title' => $this->t('ID'),
          '#description' => $this->t('<p>A unique HTML identifier for the section.</p>'),
          '#default_value' => $config['storm_cms_layout_builder']['section_attributes']['id'] ?? '',
        ];
        $form['layout_settings']['section_attributes']['data'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Data-* attributes'),
          '#description' => $this->t('<p>Enter one value per line, in the format <b>key|label</b> format. The pipe (|) separating its name and its optional value:<br>data-section|example-value<br>data-attribute-with-no-value</p>'),
          '#default_value' => $config['storm_cms_layout_builder']['section_attributes']['data'] ?? [],
        ];
      }

      // Title Attributes.
      if ($styleSettingConfigs->get('enable_title_attributes')) {
        $form['layout_settings']['section_title'] = [
          '#type' => 'details',
          '#title' => $this->t('Section Title'),
          '#tree' => TRUE,
        ];
        $form['layout_settings']['section_title']['heading'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Title'),
          '#description' => $this->t('Provide an optional title to the layout section'),
          '#default_value' => $config['storm_cms_layout_builder']['section_title']['heading'] ?? '',
        ];
        $form['layout_settings']['section_title']['heading_style'] = [
          '#type' => 'select',
          '#title' => $this->t('Style'),
          '#options' => [
            'h1' => $this->t('H1'),
            'h2' => $this->t('H2'),
            'h3' => $this->t('H3'),
            'h4' => $this->t('H4'),
            'h5' => $this->t('H5'),
            'h6' => $this->t('H6'),
          ],
          '#default_value' => $config['storm_cms_layout_builder']['section_title']['heading_style'] ?? 'h1',
        ];
        $form['layout_settings']['section_title']['heading_alignment'] = [
          '#type' => 'select',
          '#title' => $this->t('Alignment'),
          '#options' => [
            'text-start' => $this->t('Left'),
            'text-end' => $this->t('Right'),
            'text-center' => $this->t('Center'),
          ],
          '#default_value' => $config['storm_cms_layout_builder']['section_title']['heading_alignment'] ?? 'text-start',
        ];
      }

      // Background configs.
      if ($styleSettingConfigs->get('bg_show_config')) {
        $form['layout_settings']['section_background'] = [
          '#type' => 'details',
          '#title' => $this->t('Section Background'),
        ];
        $colorOptions = $this->getColors($styleSettingConfigs->get('background_colors'));
        $form['layout_settings']['section_background']['background_color'] = [
          '#type' => 'select',
          '#options' => $colorOptions,
          '#default_value' => $config['storm_cms_layout_builder']['section_background']['background_color'] ?? 'bg-none',
          '#title' => $this->t('Background Color'),
        ];
      }

      // Padding configs.
      if ($styleSettingConfigs->get('padding_show_config')) {
        $form['layout_settings']['padding'] = [
          '#type' => 'details',
          '#title' => $this->t('Section Padding'),
        ];
        $paddingOptions = [
          'padding_top' => $this->t('Top padding'),
          'padding_bottom' => $this->t('Bottom padding'),
          'padding_left' => $this->t('Left padding'),
          'padding_right' => $this->t('Right padding'),
        ];
        foreach ($paddingOptions as $key => $value) {
          $form['layout_settings']['padding'][$key] = [
            '#type' => 'select',
            '#title' => $value,
            '#options' => [
              'none' => $this->t('None'),
            ],
            '#empty_value' => 'none',
            '#default_value' => $config['storm_cms_layout_builder']['padding'][$key] ?? 'none',
          ];
          $form['layout_settings']['padding'][$key]['#options'] = $this->getConfigValues($styleSettingConfigs->get($key));
        }
      }

      // Margin configs.
      if ($styleSettingConfigs->get('spacing_show_config')) {
        $form['layout_settings']['spacing'] = [
          '#type' => 'details',
          '#title' => $this->t('Section Spacing'),
        ];
        $spacingOptions = [
          'spacing_top' => $this->t('Top spacing'),
          'spacing_bottom' => $this->t('Bottom spacing'),
          'spacing_left' => $this->t('Left spacing'),
          'spacing_right' => $this->t('Right spacing'),
        ];
        foreach ($spacingOptions as $key => $value) {
          $form['layout_settings']['spacing'][$key] = [
            '#type' => 'select',
            '#title' => $value,
            '#options' => [
              'none' => $this->t('None'),
            ],
            '#empty_value' => 'none',
            '#default_value' => $config['storm_cms_layout_builder']['spacing'][$key] ?? 'none',
          ];
          $form['layout_settings']['spacing'][$key]['#options'] = $this->getConfigValues($styleSettingConfigs->get($key));
        }
      }

      // Theming config.
      if ($styleSettingConfigs->get('theme_show_config')) {
        $form['layout_settings']['section_theme'] = [
          '#type' => 'details',
          '#title' => $this->t('Section Theme'),
        ];
        $form['layout_settings']['section_theme']['styles'] = [
          '#type' => 'checkboxes',
          '#multiple' => TRUE,
          '#options' => $this->getConfigValues($styleSettingConfigs->get('styles')),
          '#default_value' => $config['storm_cms_layout_builder']['section_theme']['styles'] ?? [],
        ];
      }

      // Adding custom validation and submit.
      $form['#validate'] = 'Drupal\storm_cms_layout_builder\EventSubscriber\StormLayoutBuilderSectionFormAlter::layoutBuilderSectionFormAlterValidate';
      array_unshift(
        $form['#submit'],
        'Drupal\storm_cms_layout_builder\EventSubscriber\StormLayoutBuilderSectionFormAlter::layoutBuilderSectionFormAlterSubmit'
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function layoutBuilderSectionFormAlterValidate(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    if (isset($values['layout_settings']['section_attributes']['id']) && static::layoutBuilderAttributeValidation($values['layout_settings']['section_attributes']['id'])) {
      $form_state->setError($form['layout_settings']['section_attributes']['id'], t('ID attribute must be valid for CSS.'));
    }
    if (isset($values['layout_settings']['section_attributes']['data'])) {
      $data_attrs = preg_split('/\R/', $values['layout_settings']['section_attributes']['data']);
      foreach ($data_attrs as $data_attr) {
        if (empty($data_attr)) {
          break;
        }
        $data_attr = explode('|', $data_attr);
        if (substr($data_attr[0], 0, 5) !== 'data-') {
          $form_state->setError($form['layout_settings']['section_attributes']['data'], t('Data attributes must begin with "data-"'));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function layoutBuilderSectionFormAlterSubmit(array &$form, FormStateInterface $form_state) {
    $formObject = $form_state->getFormObject();
    $config = $formObject->getCurrentLayout()->getConfiguration();
    if (!$config) {
      $config = [];
    }

    $config['storm_cms_layout_builder'] = [
      'section_attributes' => [
        'id' => $form_state->getValue(['layout_settings', 'section_attributes', 'id']),
        'data' => $form_state->getValue(['layout_settings', 'section_attributes', 'data']),
      ],
      'section_title' => [
        'heading' => $form_state->getValue(['layout_settings', 'section_title', 'heading']),
        'heading_style' => $form_state->getValue(['layout_settings', 'section_title', 'heading_style']),
        'heading_alignment' => $form_state->getValue(['layout_settings', 'section_title', 'heading_alignment'])
      ],
      'section_background' => [
        'background_color' => $form_state->getValue(['layout_settings', 'section_background', 'background_color'])
      ],
      'padding' => [
        'padding_top' => $form_state->getValue(['layout_settings', 'padding', 'padding_top']),
        'padding_bottom' => $form_state->getValue(['layout_settings', 'padding', 'padding_bottom']),
        'padding_left' => $form_state->getValue(['layout_settings', 'padding', 'padding_left']),
        'padding_right' => $form_state->getValue(['layout_settings', 'padding', 'padding_right']),
      ],
      'spacing' => [
        'spacing_top' => $form_state->getValue(['layout_settings', 'spacing', 'spacing_top']),
        'spacing_bottom' => $form_state->getValue(['layout_settings', 'spacing', 'spacing_bottom']),
        'spacing_left' => $form_state->getValue(['layout_settings', 'spacing', 'spacing_left']),
        'spacing_right' => $form_state->getValue(['layout_settings', 'spacing', 'spacing_right']),
      ],
      'section_theme' => [
        'styles' => $form_state->getValue(['layout_settings', 'section_theme', 'styles']),
      ],
    ];

    $formObject->getCurrentLayout()->setConfiguration($config);
  }

  /**
   * Helper function to validate css attributes.
   */
  protected static function layoutBuilderAttributeValidation($value) {
    if ($value == Html::getId($value)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Helper function to get colors.
   */
  private function getColors($colorConfigs) {
    $options = [];
    if (isset($colorConfigs) && $colorConfigs !== '') {
      $colors = $this->getConfigValues($colorConfigs);
      foreach ($colors as $class => $color) {
        $options[$class] = $color;
      }
      $options = ['bg-none' => $this->t("None")] + $options;
    }
    return $options;
  }

  /**
   * Helper function to get config values into an options array.
   */
  private function getConfigValues($configs) {
    $options = [];
    foreach (explode("\r\n", $configs) as $config) {
      $config = trim($config);
      if (!empty($config)) {
        [$class, $label] = explode('|', $config);
        $options[$class] = $label;
      }
    }

    return $options;
  }

}
