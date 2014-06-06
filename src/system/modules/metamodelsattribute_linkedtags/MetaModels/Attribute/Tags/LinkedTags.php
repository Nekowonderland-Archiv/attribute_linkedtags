<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage AttributeTags
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Attribute\Tags;

use MetaModels\Attribute\AbstractHybrid as MetaModelAttributeHybrid;
use MetaModels\Filter\Rules\StaticIdList;
use MetaModels\Filter\Setting\Factory as FilterSettingFactory;
use MetaModels\Render\Template as MetaModelTemplate;
use MetaModels\Factory as MetaModelFactory;

/**
 * This is the MetaModelAttribute class for handling tag attributes.
 *
 * @package    MetaModels
 * @subpackage AttributeTags
 */
class LinkedTags extends MetaModelAttributeHybrid
{

	/**
	 * when rendered via a template, this returns the values to be stored in the template.
	 */
	protected function prepareTemplate(MetaModelTemplate $objTemplate, $arrRowData, $objSettings = null)
	{
		parent::prepareTemplate($objTemplate, $arrRowData, $objSettings);
		$objTemplate->displayedValue = $this->get('mm_displayedValue');
	}

	/**
	 * Determine the column to be used for alias.
	 * This is either the configured alias column or the id, if
	 * an alias column is absent.
	 *
	 * @return string the name of the column.
	 */
	public function getAliasCol()
	{
		$strColNameAlias = $this->get('tag_alias');
		if (!$strColNameAlias)
		{
			$strColNameAlias = $this->get('tag_id');
		}
		return $strColNameAlias;
	}

	/////////////////////////////////////////////////////////////////
	// interface IMetaModelAttribute
	/////////////////////////////////////////////////////////////////

	/**
	 * {@inheritdoc}
	 */
	public function getAttributeSettingNames()
	{
		return array_merge(parent::getAttributeSettingNames(), array(
			'mm_table',
			'mm_displayedValue',
			'mm_sorting',
			'mm_filter',
			'mandatory',
			'filterable',
			'searchable',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFieldDefinition($arrOverrides = array())
	{
		// TODO: add tree support here.
		$arrFieldDef = parent::getFieldDefinition($arrOverrides);

		// If tag as wizard is true, change the input type.
		if ($arrOverrides['tag_as_wizard'] == true)
		{
			$arrFieldDef['inputType'] = 'checkboxWizard';
		}
		else
		{
			$arrFieldDef['inputType'] = 'checkbox';
		}

		$arrFieldDef['options']						 = $this->getFilterOptions(NULL, false);
		$arrFieldDef['eval']['includeBlankOption']	 = true;
		$arrFieldDef['eval']['multiple']			 = true;
		return $arrFieldDef;
	}

	/**
	 * {@inheritdoc}
	 */
	public function valueToWidget($varValue)
	{
		$arrReturn = array();

		if (!is_array($varValue) || empty($varValue))
		{
			return $arrReturn;
		}

		foreach ($varValue as $mixItem)
		{
			if(is_array($mixItem) && isset($mixItem['id']))
			{
				$arrReturn[] = $mixItem['id'];
			}
			elseif(!is_array($mixItem))
			{
				$arrReturn[] = $mixItem;
			}
		}

		return $arrReturn;
	}

	/**
	 * {@inheritdoc}
	 */
	public function widgetToValue($varValue, $intId)
	{
		return $varValue;
	}

	/**
	 * {@inheritdoc}
	 *
	 * Fetch filter options from foreign table.
	 *
	 */
	public function getFilterOptions($arrIds, $usedOnly, &$arrCount = null)
	{
		$strMMName			 = $this->get('mm_table');
		$strDisplayedValue	 = $this->get('mm_displayedValue');
		$strSortingValue	 = $this->get('mm_sorting') ? $this->get('mm_sorting') : 'id';
		$intFilterId		 = $this->get('mm_filter');

		$arrReturn = array();

		if ($strMMName && $strDisplayedValue)
		{
			// Change language.
			if (TL_MODE == 'BE')
			{
				$strCurrentLanguage		 = $GLOBALS['TL_LANGUAGE'];
				$GLOBALS['TL_LANGUAGE']	 = $this->getMetaModel()->getActiveLanguage();
			}

			$objMetaModel = MetaModelFactory::byTableName($strMMName);
			$objFilter = $objMetaModel->getEmptyFilter();

			// Set Filter and co.
			$objFilterSettings = FilterSettingFactory::byId($intFilterId);
			if ($objFilterSettings)
			{
				$arrValues			 = $_GET;
				$arrPresets			 = deserialize($this->metamodel_filterparams, true);
				$arrPresetNames		 = $objFilterSettings->getParameters();
				$arrFEFilterParams	 = array_keys($objFilterSettings->getParameterFilterNames());

				$arrProcessed = array();

				// We have to use all the preset values we want first.
				foreach ($arrPresets as $strPresetName => $arrPreset)
				{
					if (in_array($strPresetName, $arrPresetNames))
					{
						$arrProcessed[$strPresetName] = $arrPreset['value'];
					}
				}

				// now we have to use all FE filter params, that are either:
				// * not contained within the presets
				// * or are overridable.
				foreach ($arrFEFilterParams as $strParameter)
				{
					// unknown parameter? - next please
					if (!array_key_exists($strParameter, $arrValues))
					{
						continue;
					}

					// not a preset or allowed to override? - use value
					if ((!array_key_exists($strParameter, $arrPresets)) || $arrPresets[$strParameter]['use_get'])
					{
						$arrProcessed[$strParameter] = $arrValues[$strParameter];
					}
				}
				
				$objFilterSettings->addRules($objFilter, $arrProcessed);
			}
			
			$objItems = $objMetaModel->findByFilter($objFilter, $strSortingValue);

			// Reset language.
			if (TL_MODE == 'BE')
			{
				$GLOBALS['TL_LANGUAGE'] = $strCurrentLanguage;
			}

			foreach ($objItems as $objItem)
			{
				$arrItem = $objItem->parseValue();

				$strValue	 = $arrItem['text'][$strDisplayedValue];
				$strAlias	 = $objItem->get('id');

				$arrReturn[$strAlias] = $strValue;
			}
		}

		return $arrReturn;
	}

	/**
	 * {@inheritdoc}
	 */
	public function searchFor($strPattern)
	{
//		$objFilterRule = new MetaModelFilterRuleTags($this, $strPattern);
//		return $objFilterRule->getMatchingIds();

		return array();
	}

	/////////////////////////////////////////////////////////////////
	// interface IMetaModelAttributeComplex
	/////////////////////////////////////////////////////////////////

	public function getDataFor($arrIds)
	{
		$arrReturn = array();

		$strMetaModelTableName	 = $this->getMetaModel()->getTableName();
		$strMetaModelTableNameId = $strMetaModelTableName . '_id';

		$strMMName			 = $this->get('mm_table');
		$strDisplayedValue	 = $this->get('mm_displayedValue');

		if ($strMMName && $strDisplayedValue)
		{
			$objDB		 = \Database::getInstance();
			$objValue	 = $objDB->prepare(sprintf('
					SELECT *
					FROM tl_metamodel_tag_relation			
					WHERE item_id IN (%1$s) AND att_id=?
					ORDER BY tl_metamodel_tag_relation.value_sorting', implode(',', $arrIds) // 1
					))
					->execute($this->get('id'));

			$arrKnownValues	 = array();
			$arrItemId		 = array();

			while ($objValue->next())
			{
				$arrKnownValues[]				 = $objValue->value_id;
				$arrItemId[$objValue->value_id]	 = $objValue->item_id;
			}

			// Change language.
			if (TL_MODE == 'BE')
			{
				$strCurrentLanguage		 = $GLOBALS['TL_LANGUAGE'];
				$GLOBALS['TL_LANGUAGE']	 = $this->getMetaModel()->getActiveLanguage();
			}

			// Get data from MM.
			$objMetaModel = MetaModelFactory::byTableName($strMMName);

			$objFilter	 = $objMetaModel->getEmptyFilter();
			$objFilter->addFilterRule(new StaticIdList($arrKnownValues));
			$objItems	 = $objMetaModel->findByFilter($objFilter);

			// Reset language.
			if (TL_MODE == 'BE')
			{
				$GLOBALS['TL_LANGUAGE'] = $strCurrentLanguage;
			}

			foreach ($objItems as $objItem)
			{
				$objMMID	 = $objItem->get('id');
				$mixID		 = $arrItemId[$objMMID];
				$arrValues	 = $objItem->parseValue();

				$arrReturn[$mixID][] = array_merge(array(
					'id'		 => $arrValues['raw']['id'],
					'pid'		 => $arrValues['raw']['pid'],
					'sorting'	 => $arrValues['raw']['sorting'],
					'tstamp'	 => $arrValues['raw']['tstamp'],
						), $arrValues['text']);
			}
		}

		return $arrReturn;
	}

	public function setDataFor($arrValues)
	{
		$objDB		 = \Database::getInstance();
		$arrItemIds	 = array_map('intval', array_keys($arrValues));
		sort($arrItemIds);

		// load all existing tags for all items to be updated, keep the ordering to item Id
		// so we can benefit from the batch deletion and insert algorithm.
		$objExistingTagIds = $objDB->prepare(sprintf('
				SELECT * FROM tl_metamodel_tag_relation
				WHERE
				att_id=?
				AND item_id IN (%1$s)
				ORDER BY item_id ASC
				', implode(',', $arrItemIds)))
				->execute($this->get('id'));



		// now loop over all items and update the values for them.
		// NOTE: we can not loop over the original array, as the item ids are not neccessarily
		// sorted ascending by item id.
		$arrSQLInsertValues = array();
		foreach ($arrItemIds as $intItemId)
		{
			$arrTags = $arrValues[$intItemId];

			if (is_null($intItemId) || empty($arrTags))
			{
				$arrTagIds = array();
			}
			else
			{
				$arrTagIds = array_map('intval', $arrTags);
			}

			$arrThisExisting = array();

			// determine existing tags for this item.
			if (($objExistingTagIds->item_id == $intItemId))
			{
				$arrThisExisting[] = $objExistingTagIds->value_id;
			}
			while ($objExistingTagIds->next() && ($objExistingTagIds->item_id == $intItemId))
			{
				$arrThisExisting[] = $objExistingTagIds->value_id;
			}

			// first pass, delete all not mentioned anymore.
			$arrValuesToRemove = array_diff($arrThisExisting, $arrTagIds);
			if ($arrValuesToRemove)
			{
				$objDB->prepare(sprintf('
				DELETE FROM tl_metamodel_tag_relation
				WHERE
				att_id=?
				AND item_id=?
				AND value_id IN (%s)
				', implode(',', $arrValuesToRemove)))
						->execute($this->get('id'), $intItemId);
			}
			// second pass, add all new values in a row.
			$arrValuesToAdd = array_diff($arrTagIds, $arrThisExisting);
			if ($arrValuesToAdd)
			{
				foreach ($arrValuesToAdd as $intValueId)
				{
					$arrSQLInsertValues[] = sprintf('(%s,%s,%s,%s)', $this->get('id'), $intItemId, 0, $intValueId);
				}
			}
			// Third pass, update all sorting values.
			$arrValuesToUpdate = array_diff($arrTagIds, $arrValuesToAdd);
			if ($arrValuesToUpdate)
			{
				foreach ($arrValuesToUpdate as $intValueId)
				{
					$objDB->prepare('
						UPDATE tl_metamodel_tag_relation
						SET value_sorting = 0
						WHERE
						att_id=?
						AND item_id=?
						AND value_id=?')
							->execute($this->get('id'), $intItemId, $intValueId);
				}
			}
		}

		if ($arrSQLInsertValues)
		{
			$objDB->execute('INSERT INTO tl_metamodel_tag_relation (att_id, item_id, value_sorting, value_id) VALUES ' . implode(',', $arrSQLInsertValues));
		}
	}

	public function unsetDataFor($arrIds)
	{
		if ($arrIds)
		{
			if (!is_array($arrIds))
				throw new \Exception('MetaModelAttributeTags::unsetDataFor() invalid parameter given! Array of ids is needed.', 1);
			$objDB = \Database::getInstance();
			$objDB->prepare(sprintf('
				DELETE FROM tl_metamodel_tag_relation
				WHERE
				att_id=?
				AND item_id IN (%s)', implode(',', $arrIds)))->execute($this->get('id'));
		}
	}

}
