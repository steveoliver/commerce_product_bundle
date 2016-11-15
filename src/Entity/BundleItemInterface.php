<?php

namespace Drupal\commerce_product_bundle\Entity;

use Drupal\commerce_price\Price;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Defines the interface for bundle items.
 */
interface BundleItemInterface extends EntityChangedInterface, EntityOwnerInterface {

  public function getTitle();

  public function setTitle($title);

  public function getBundle();

  public function getBundleId();

  public function getPurchasableEntity();

  public function setPurchasableEntity(PurchasableEntityInterface $entity);

  public function getPrice();

  public function setPrice(Price $price);

  public function getQty();

  public function setQty($qty);

  /**
   * Gets the variation creation timestamp.
   *
   * @return int
   *   The variation creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the variation creation timestamp.
   *
   * @param int $timestamp
   *   The variation creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

}
