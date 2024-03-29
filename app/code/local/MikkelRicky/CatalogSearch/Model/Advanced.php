<?php
class MikkelRicky_CatalogSearch_Model_Advanced extends Mage_CatalogSearch_Model_Advanced {
	/**
	 * Add advanced search filters to product collection
	 *
	 * @param   array $values
	 * @return  Mage_CatalogSearch_Model_Advanced
	 */
	public function addFilters($values) {
		$attributes = $this->getAttributes();
		$allConditions = array();
		$filteredAttributes = array();
		$indexFilters = Mage::getModel('catalogindex/indexer')->buildEntityFilter(
																																							$attributes,
																																							$values,
																																							$filteredAttributes,
																																							$this->getProductCollection()
																																							);

		foreach ($indexFilters as $filter) {
			$this->getProductCollection()->addFieldToFilter('entity_id', array('in'=>new Zend_Db_Expr($filter)));
		}

		$priceFilters = Mage::getModel('catalogindex/indexer')->buildEntityPriceFilter(
																																									 $attributes,
																																									 $values,
																																									 $filteredAttributes,
																																									 $this->getProductCollection()
																																									 );

		foreach ($priceFilters as $code=>$filter) {
			$this->getProductCollection()->getSelect()->joinInner(
																														array("_price_filter_{$code}"=>$filter),
																														"`_price_filter_{$code}`.`entity_id` = `e`.`entity_id`",
																														array()
																														);
		}

		foreach ($attributes as $attribute) {
			$code      = $attribute->getAttributeCode();
			$condition = false;

			if (isset($values[$code])) {
				$value = $values[$code];

				if (is_array($value)) {
					if ((isset($value['from']) && strlen($value['from']) > 0)
							|| (isset($value['to']) && strlen($value['to']) > 0)) {
						$condition = $value;
					}
					elseif ($attribute->getBackend()->getType() == 'varchar') {
						$condition = array('in_set'=>$value);
					}
					elseif (!isset($value['from']) && !isset($value['to'])) {
						$condition = array('in'=>$value);
					}
				} else {
					if (strlen($value)>0) {
						if (in_array($attribute->getBackend()->getType(), array('varchar', 'text'))) {
							$condition = array('like'=>'%'.$value.'%');
						} elseif ($attribute->getFrontendInput() == 'boolean') {
							$condition = array('in' => array('0','1'));
						} else {
							$condition = $value;
						}
					}
				}
			}

			if (false !== $condition) {
				$this->_addSearchCriteria($attribute, $value);

				if (in_array($code, $filteredAttributes))
					continue;

				$table = $attribute->getBackend()->getTable();
				$attributeId = $attribute->getId();
				if ($attribute->getBackendType() == 'static'){
					$attributeId = $attribute->getAttributeCode();
					$condition = array('like'=>"%{$condition}%");
				}

				$allConditions[$table][$attributeId] = $condition;
			}
		}

		if ($allConditions) {
			$this->getProductCollection()->addFieldsToFilter($allConditions);
		} else if (!count($filteredAttributes)) {
			Mage::throwException(Mage::helper('catalogsearch')->__('You have to specify at least one search term'));
		}

		return $this;
	}
}
