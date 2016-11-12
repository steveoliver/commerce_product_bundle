<?php

namespace Drupal\commerce_product_bundle_static;

use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\commerce_order\Entity\OrderInterface;

/**
 * Provides an order processor that adds tax adjustments for bundles based on their items.
 */
class OrderTaxProcessor implements OrderProcessorInterface {

  /**
   * Constructs a new TaxOrderProcessor object.
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public function process(OrderInterface $order) {
    foreach ($order->getItems() as $order_item) {
      $purchased_entity = $order_item->getPurchasedEntity();
      if ($purchased_entity) {
//        $available = $this->availabilityManager->check($order_item->getPurchasedEntity(), $order_item->getQuantity());
//        if (!$available) {
//          $order->removeItem($order_item);
//          $order_item->delete();
//        }
      }
    }
  }

}
