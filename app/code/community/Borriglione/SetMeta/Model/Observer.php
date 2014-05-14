<?php
/**
 * Observer class
 *
 * @category Borriglione
 * @package Borriglione_SetMeta
 * @author André Herrn <info@andre-herrn.de>
 * @author FireGento Team <team@firegento.com>
 * @copyright 2014 André Herrn (http://www.andre-herrn.de)
 * @copyright 2013 FireGento Team (http://www.firegento.com)
 * @license http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
class Borriglione_SetMeta_Model_Observer
{
    /**
     * Auto-Generates the meta information of a product.
     * Event: <catalog_product_save_before>
     *
     * @param  Varien_Event_Observer $observer Observer
     * @return Borriglione_SetMeta_Model_Observer Observer
     */
    public function autogenerateMetaInformation(Varien_Event_Observer $observer)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        if ($product->getData('meta_autogenerate') == 1) {
            // Set Meta Title
            $product->setMetaTitle($product->getName());

            // Set Meta Keywords
            $keywords = $this->_getCategoryKeywords($product);
            if (!empty($keywords)) {
                if (mb_strlen($keywords) > 255) {
                    $remainder = '';
                    $keywords = Mage::helper('core/string')->truncate($keywords, 255, '', $remainder, false);
                }
                $product->setMetaKeyword($keywords);
            }

            // Set Meta Description
            $description = $product->getShortDescription();
            if (empty($description)) {
                $description = $product->getDescription();
            }
            if (empty($description)) {
                $description = $keywords;
            }
            if (mb_strlen($description) > 255) {
                $remainder = '';
                $description = Mage::helper('core/string')->truncate($description, 255, '...', $remainder, false);
            }
            $product->setMetaDescription($description);
        }

        return $this;
    }

    /**
     * Get the categories of the current product
     *
     * @param  Mage_Catalog_Model_Product $product Product
     * @return array Categories
     */
    protected function _getCategoryKeywords($product)
    {
        $categories = $product->getCategoryIds();
        $categoryArr = $this->_fetchCategoryNames($categories);
        $keywords = $this->_buildKeywords($categoryArr);

        return $keywords;
    }

    /**
     * Fetches all category names via category path; adds first the assigned
     * categories and second all categories via path.
     *
     * @param  array $categories Category Ids
     * @return array Categories
     */
    protected function _fetchCategoryNames($categories)
    {
        $return = array(
            'assigned' => array(),
            'path' => array()
        );

        foreach ($categories as $categoryId) {
            // Check if category was already added
            if (array_key_exists($categoryId, $return['assigned'])
                || array_key_exists($categoryId, $return['path'])
            ) {
                return;
            }

            /* @var $category Mage_Catalog_Model_Category */
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $return['assigned'][$categoryId] = $category->getName();

            // Fetch path ids and remove the first two (base and root category)
            $path = $category->getPath();
            $pathIds = explode('/', $path);
            array_shift($pathIds);
            array_shift($pathIds);

            // Fetch the names from path categories
            if (count($pathIds) > 0) {
                foreach ($pathIds as $pathId) {
                    if (!array_key_exists($pathId, $return['assigned'])
                        && !array_key_exists($pathId, $return['path'])
                    ) {
                        /* @var $pathCategory Mage_Catalog_Model_Category */
                        $pathCategory = Mage::getModel('catalog/category')->load($pathId);
                        $return['path'][$pathId] = $pathCategory->getName();
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Processes the category array and generates a string
     *
     * @param  array $categoryTypes Categories
     * @return string Keywords
     */
    protected function _buildKeywords($categoryTypes)
    {
        if (!$categoryTypes) {
            return '';
        }

        $keywords = array();
        foreach ($categoryTypes as $categories) {
            $keywords[] = implode(', ', $categories);
        }

        return implode(', ', $keywords);
    }
}
