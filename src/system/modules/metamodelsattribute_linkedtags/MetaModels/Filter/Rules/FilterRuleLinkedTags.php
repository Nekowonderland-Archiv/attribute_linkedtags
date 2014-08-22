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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Rules;

use MetaModels\Attribute\Tags\LinkedTags;
use MetaModels\Attribute\Tags\Tags;
use MetaModels\Filter\FilterRule;

/**
 * This is the MetaModelFilterRule class for handling select fields.
 *
 * @package	   MetaModels
 * @subpackage AttributeTags
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class FilterRuleLinkedTags extends FilterRule
{
	/**
	 * The attribute to filter.
	 *
	 * @var Tags
	 */
	protected $objAttribute;

	/**
	 * The filter value.
	 *
	 * @var string
	 */
	protected $value;

	/**
	 * {@inheritDoc}
	 */
	public function __construct(LinkedTags $objAttribute, $strValue)
	{
		parent::__construct();

		$this->objAttribute = $objAttribute;
		$this->value        = $strValue;
	}

	/**
	 * Ensure the value is either a proper id or array od ids - converts aliases to ids.
	 *
	 * @return array
	 */
	public function sanitizeValue()
	{
		$strTableNameId  = $this->objAttribute->get('mm_table');
		$strColNameId    = 'id';

		$arrValues = is_array($this->value) ? $this->value : explode(',', $this->value);

		$objDB = \Database::getInstance();

		$objSelectIds = $objDB
			->prepare(
				sprintf('SELECT %1$s FROM %2$s WHERE %1$s IN (%3$s)',
					$strColNameId,
					$strTableNameId,
					implode(',', array_fill(0, count($arrValues), '?'))
				)
			)
			->execute($arrValues);

		$arrValues = $objSelectIds->fetchEach($strColNameId);

		return $arrValues;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMatchingIds()
	{
		$arrValues = $this->sanitizeValue();

		// Get out when no values are available.
		if (!$arrValues)
		{
			return array();
		}

		$objMatches = \Database::getInstance()
			->prepare('
				SELECT item_id as id
				FROM tl_metamodel_tag_relation
				WHERE value_id IN (' . implode(',', $arrValues) . ')
				AND att_id = ?'
			)
			->execute($this->objAttribute->get('id'));

		return $objMatches->fetchEach('id');
	}
}
