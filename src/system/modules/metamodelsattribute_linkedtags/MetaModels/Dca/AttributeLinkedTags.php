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

namespace MetaModels\Dca;

use MetaModels\Factory;

/**
 * Supplementary class for handling DCA information for select attributes.
 *
 * @package	   MetaModels
 * @subpackage AttributeTags
 */
class AttributeLinkedTags
{

	/**
	 * @var TableMetaModelsAttributeLinkedTags
	 */
	protected static $objInstance = null;

	/**
	 * Get the static instance.
	 *
	 * @static
	 * @return TableMetaModelsAttributeLinkedTags
	 */
	public static function getInstance()
	{
		if (self::$objInstance == null)
		{
			self::$objInstance = new self();
		}
		return self::$objInstance;
	}

    public function getMMNames()
    {
        $arrRetrun = array();
        $arrTables = Factory::getAllTables();

        foreach ($arrTables as $strMMName)
        {
            $objMM = Factory::byTableName($strMMName);
            if ($objMM->isTranslated())
            {
                $arrRetrun['Translated'][$strMMName] = sprintf('%s (%s)', $objMM->get('name'), $strMMName);
            }
            else
            {
                $arrRetrun['None Translated'][$strMMName] = sprintf('%s (%s)', $objMM->get('name'), $strMMName);;
            }
        }

        return $arrRetrun;
    }

	/**
     * @param DcGeneral\DC_General $objDC
     *
     * @return array
     */
    public function getColumnNames($objDC)
    {
        $arrRetrun   = array();
        $arrMMTables = Factory::getAllTables();
        $objModel    = $objDC->getEnvironment()->getCurrentModel();


        if (($objModel) && in_array($objModel->getProperty('mm_table'), $arrMMTables))
        {
            $objMM = Factory::byTableName($objModel->getProperty('mm_table'));

            foreach ($objMM->getAttributes() as $objAttribute)
            {
                $strName   = $objAttribute->getName();
                $strColumn = $objAttribute->getColName();
                $strType   = $objAttribute->get('type');

                $arrRetrun[$strColumn] = vsprintf("%s (%s - %s)", array($strName, $strColumn, $strType));
            }
        }

        return $arrRetrun;
    }
	
    /**
     * @param DcGeneral\DC_General $objDC
     *
     * @return array
     */
    public function getFilters($objDC)
    {
        $arrRetrun   = array();
        $arrMMTables = Factory::getAllTables();
        $objModel    = $objDC->getEnvironment()->getCurrentModel();

        if (($objModel) && in_array($objModel->getProperty('mm_table'), $arrMMTables))
        {
            $objMM = Factory::byTableName($objModel->getProperty('mm_table'));

            $objFilter = \Database::getInstance()
                ->prepare("SELECT id,name FROM tl_metamodel_filter WHERE pid=? ORDER BY name")
                ->execute($objMM->get('id'));

            while ($objFilter->next())
            {
                $arrRetrun[$objFilter->id] = $objFilter->name;
            }
        }

        return $arrRetrun;
    }

}