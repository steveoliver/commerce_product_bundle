<?php

namespace Drupal\commerce_product_bundle\Event;

use Drupal\commerce_product_bundle\Entity\BundleInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the bundle event.
 *
 * @see \Drupal\commerce_product_bundle\Event\BundleEvents
 */
class BundleEvent extends Event {

  /**
   * The bundle.
   *
   * @var \Drupal\commerce_product_bundle\Entity\BundleBase
   */
  protected $bundle;

  /**
   * Constructs a new BundleEvent.
   *
   * @param \Drupal\commerce_product_bundle\Entity\BundleInterface $bundle
   *   The bundle.
   */
  public function __construct(BundleInterface $bundle) {
    $this->bundle = $bundle;
  }

  /**
   * Gets the bundle.
   *
   * @return \Drupal\commerce_product_bundle\Entity\BundleInterface
   *   The bundle.
   */
  public function getBundle() {
    return $this->bundle;
  }

}
