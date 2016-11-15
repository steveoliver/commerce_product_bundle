<?php

namespace Drupal\commerce_product_bundle_static\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\commerce_product_bundle\Entity\BundleItemBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the static bundle item entity class.
 *
 * @ContentEntityType(
 *   id = "commerce_product_bundle_i_static",
 *   label = @Translation("Static bundle item"),
 *   label_singular = @Translation("static bundle item"),
 *   label_plural = @Translation("static bundle items"),
 *   label_count = @PluralTranslation(
 *     singular = "@count static bundle item",
 *     plural = "@count static bundle items",
 *   ),
 *   handlers = {
 *     "event" = "Drupal\commerce_product_bundle\Event\BundleItemEvent",
 *     "storage" = "Drupal\commerce_product_bundle\BundleItemStorage",
 *     "access" = "Drupal\commerce\EmbeddedEntityAccessControlHandler",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *     },
 *     "inline_form" = "Drupal\commerce_product_bundle\Form\BundleItemInlineForm",
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler"
 *   },
 *   admin_permission = "administer commerce_product_bundle",
 *   translatable = TRUE,
 *   content_translation_ui_skip = TRUE,
 *   base_table = "commerce_product_bundle_i_static",
 *   data_table = "commerce_product_bundle_i_static_data",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "label" = "title",
 *     "status" = "status",
 *   },
 *   fieldable = TRUE,
 *   field_ui_base_route = "entity.commerce_product_bundle_i_static.edit_form",
 * )
 */
class StaticBundleItem extends BundleItemBase {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Only allowed to reference static bundles.
    $fields['bundle_id']->setSetting('target_type', 'commerce_product_bundle_static');

    return $fields;
  }

}