<?php

namespace Drupal\storm_cms_layout_builder\Controller;

use Drupal\layout_builder\Controller\ChooseSectionController;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Returns responses for LB Section extra custom routes.
 */
final class StormLayoutBuilderChooseSectionController extends ChooseSectionController {

  /**
   * {@inheritdoc}
   */
  public function build(SectionStorageInterface $section_storage, $delta) {
    $build = parent::build($section_storage, $delta);
    $build['#attached']['library'][] = 'storm_cms_layout_builder/browser';
    return $build;
  }

}
