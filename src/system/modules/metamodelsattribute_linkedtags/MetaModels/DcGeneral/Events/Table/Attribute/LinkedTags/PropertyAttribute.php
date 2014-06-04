<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage Core
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\DcGeneral\Events\Table\Attribute\LinkedTags;

use ContaoCommunityAlliance\Contao\EventDispatcher\Event\CreateEventDispatcherEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\Factory\Event\BuildDataDefinitionEvent;
use MetaModels\DcGeneral\Events\BaseSubscriber;
use MetaModels\Factory;

/**
 * Handle events for tl_metamodel_attribute.alias_fields.attr_id.
 */
class PropertyAttribute
	extends BaseSubscriber
{
	/**
	 * Register all listeners to handle creation of a data container.
	 *
	 * @param CreateEventDispatcherEvent $event The event.
	 *
	 * @return void
	 */
	public static function registerEvents(CreateEventDispatcherEvent $event)
	{
		$dispatcher = $event->getEventDispatcher();

		self::registerBuildDataDefinitionFor(
			'tl_metamodel_attribute',
			$dispatcher,
			__CLASS__ . '::registerTableMetaModelAttributeEvents'
		);
	}

	/**
	 * Register the events for table tl_metamodel_attribute.
	 *
	 * @param BuildDataDefinitionEvent $event The event being processed.
	 *
	 * @return void
	 */
	public static function registerTableMetaModelAttributeEvents(BuildDataDefinitionEvent $event)
	{
		static $registered;
		if ($registered)
		{
			return;
		}
		$registered = true;
		$dispatcher = $event->getDispatcher();

		self::registerListeners(
			array(
				GetPropertyOptionsEvent::NAME => __CLASS__ . '::getMMNames',
			),
			$dispatcher,
			array('tl_metamodel_attribute', 'mm_table')
		);

		self::registerListeners(
			array(
				GetPropertyOptionsEvent::NAME => __CLASS__ . '::getColumnNames',
			),
			$dispatcher,
			array('tl_metamodel_attribute', 'mm_displayedValue')
		);

		self::registerListeners(
			array(
				GetPropertyOptionsEvent::NAME => __CLASS__ . '::getColumnNames',
			),
			$dispatcher,
			array('tl_metamodel_attribute', 'mm_sorting')
		);

		self::registerListeners(
			array(
				GetPropertyOptionsEvent::NAME => __CLASS__ . '::getFilters',
			),
			$dispatcher,
			array('tl_metamodel_attribute', 'mm_filter')
		);
	}

	//---------------------------------------------------------------------------------------

	/**
	 * Return a list with all metamodels.
	 *
	 * @param GetPropertyOptionsEvent $event
	 *
	 * @return void
	 */
	public static function getMMNames(GetPropertyOptionsEvent $event)
	{
		$result = array();
		$tables = Factory::getAllTables();

		foreach ($tables as $metamodelnames)
		{
			$objMM = Factory::byTableName($metamodelnames);
			if ($objMM->isTranslated())
			{
				$result['Translated'][$metamodelnames] = sprintf('%s (%s)', $objMM->get('name'), $metamodelnames);
			}
			else
			{
				$result['None Translated'][$metamodelnames] = sprintf('%s (%s)', $objMM->get('name'), $metamodelnames);
			}
		}

		$event->setOptions($result);
	}

	/**
	 * Get a list with all attributes of the current metamodels.
	 *
	 * @param GetPropertyOptionsEvent $event
	 *
	 * @return void
	 */
	public static function getColumnNames(GetPropertyOptionsEvent $event)
	{
		$result           = array();
		$model            = $event->getModel();
		$metaModelsNames  = Factory::getAllTables();
		$currentMetaModel = $model->getProperty('mm_table');

		if (!empty($currentMetaModel) && in_array($currentMetaModel, $metaModelsNames))
		{
			$metaModel = Factory::byTableName($currentMetaModel);

			foreach ($metaModel->getAttributes() as $attribute)
			{
				$strName   = $attribute->getName();
				$strColumn = $attribute->getColName();
				$strType   = $attribute->get('type');

				$result[$strColumn] = vsprintf("%s (%s - %s)", array($strName, $strColumn, $strType));
			}

			$event->setOptions($result);
		}
	}

	/**
	 * Get a list with all filter of the current metamodels.
	 *
	 * @param GetPropertyOptionsEvent $event
	 *
	 * @return void
	 */
	public static function getFilters(GetPropertyOptionsEvent $event)
	{
		$result           = array();
		$model            = $event->getModel();
		$metaModelsNames  = Factory::getAllTables();
		$currentMetaModel = $model->getProperty('mm_table');

		if (!empty($currentMetaModel) && in_array($currentMetaModel, $metaModelsNames))
		{
			$metaModel = Factory::byTableName($currentMetaModel);

			$objFilter = \Database::getInstance()
				->prepare('SELECT id,name FROM tl_metamodel_filter WHERE pid=? ORDER BY name')
				->execute($metaModel->get('id'));

			while ($objFilter->next())
			{
				$result[$objFilter->id] = $objFilter->name;
			}

			$event->setOptions($result);
		}
	}
}
