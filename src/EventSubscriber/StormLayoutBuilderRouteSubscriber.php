<?php

namespace Drupal\storm_cms_layout_builder\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Route subscriber.
 */
final class StormLayoutBuilderRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('layout_builder.choose_block')) {
      $route->setDefault('_title', 'Add Component');
      $route->setDefault('_controller', '\Drupal\storm_cms_layout_builder\Controller\StormLayoutBuilderBrowseComponents::browse');
    }

    if ($route = $collection->get('layout_builder.choose_section')) {
      $route->setDefault('_title', 'Select Layout');
      $route->setDefault('_controller', '\Drupal\storm_cms_layout_builder\Controller\StormLayoutBuilderChooseSectionController::build');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -150];
    return $events;
  }

}
