<?php
/**
 * Setup script; Adds the meta_autogenerate attribute for products
 *
 * @category Borriglione
 * @package Borriglione_SetMeta
 * @author André Herrn <info@andre-herrn.de>
 * @author FireGento Team <team@firegento.com>
 * @copyright 2014 André Herrn (http://www.andre-herrn.de)
 * @copyright 2013 FireGento Team (http://www.firegento.com)
 * @license http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/** @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute(
    'catalog_product',
    'meta_autogenerate',
    array(
        'label' => 'Auto-Generate Meta-Information',
        'input' => 'select',
        'source' => 'eav/entity_attribute_source_boolean',
        'required' => false,
        'user_defined' => true,
        'default' => '0',
        'group' => 'Meta Information',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => true,
        'filterable' => false,
        'searchable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
        'used_in_product_listing' => false,
        'is_html_allowed_on_front' => false,
    )
);

$installer->endSetup();
