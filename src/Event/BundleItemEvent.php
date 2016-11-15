<?php

namespace Drupal\commerce_product_bundle\Event;

use Drupal\commerce_product_bundle\Entity\BundleItemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines the bundle item event.
 *
 * @see \Drupal\commerce_product_bundle\Event\BundleEvents
 */
class BundleItemEvent extends Event {

  /**
   * The bundle item.
   *
   * @var \Drupal\commerce_product_bundle\Entity\BundleItemBase
   */
  protected $bundleItem;

  /**
   * Constructs a new BundleItemEvent.
   *
   * @param \Drupal\commerce_product_bundle\Entity\BundleItemInterface $bundle_item
   *   The bundle item.
   */
  public function __construct(BundleItemInterface $bundle_item) {
    $this->bundleItem = $bundle_item;
  }

  /**
   * Gets the bundle item.
   *
   * @return \Drupal\commerce_product_bundle\Entity\BundleItemInterface
   *   The bundle item.
   */
  public function getBundleItem() {
    return $this->bundleItem;
  }

}
