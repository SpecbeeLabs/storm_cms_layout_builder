<?php

namespace Drupal\storm_cms_layout_builder\Controller;

use Drupal\layout_builder\SectionStorageInterface;
use Drupal\layout_builder_browser\Controller\BrowserController;

/**
 * Returns responses for LB Section extra custom routes.
 */
final class StormLayoutBuilderBrowseComponents extends BrowserController {

  /**
   * {@inheritdoc}
   */
  public function browse(SectionStorageInterface $section_storage, $delta, $region) {
    $build = parent::browse($section_storage, $delta, $region);
    $build['#attached']['library'][] = 'storm_cms_layout_builder/browser';
    return $build;
  }

}
