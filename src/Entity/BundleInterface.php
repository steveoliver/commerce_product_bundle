<?php

namespace Drupal\commerce_product_bundle\Entity;

use Drupal\commerce_store\Entity\EntityStoresInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\commerce\PurchasableEntityInterface;

/**
 * Defines the interface for bundles.
 */
interface BundleInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface, EntityStoresInterface, PurchasableEntityInterface {

  /**
   * Gets the bundle title.
   *
   * @return string
   *   The bundle title
   */
  public function getTitle();

  /**
   * Sets the bundle title.
   *
   * @param string $title
   *   The bundle title.
   *
   * @return $this
   */
  public function setTitle($title);

  /**
   * Get whether the bundle is published.
   *
   * Unpublished bundles are only visible to their authors and administrators.
   *
   * @return bool
   *   TRUE if the bundle is published, FALSE otherwise.
   */
  public function isPublished();

  /**
   * Sets whether the bundle is published.
   *
   * @param bool $published
   *   Whether the bundle is published.
   *
   * @return $this
   */
  public function setPublished($published);

  /**
   * Gets the bundle creation timestamp.
   *
   * @return int
   *   The bundle creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the bundle creation timestamp.
   *
   * @param int $timestamp
   *   The bundle creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the bundle items.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface[]
   *   All the referenced bundle item entities.
   */
  public function getItems();

}
