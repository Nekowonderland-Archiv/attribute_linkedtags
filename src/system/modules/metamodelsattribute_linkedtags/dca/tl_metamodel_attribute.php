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
 * @author     Christian de la Haye <service@delahaye.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * Table tl_metamodel_attribute 
 */

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['metapalettes']['linkedtags extends _simpleattribute_'] = array
(
	'+display' => array('mm_table after description', 'mm_displayedValue', 'mm_retunedValue', 'mm_sorting', 'mm_filter', 'mm_filterparams')
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['mm_table'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_table'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => array('MetaModels\Dca\AttributeLinkedTags', 'getMMNames'),
    'eval'             => array
    (
        'includeBlankOption' => true,
        'doNotSaveEmpty'     => true,
        'alwaysSave'         => true,
        'submitOnChange'     => true,
        'tl_class'           => 'w50',
        'chosen'             => 'true'
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['mm_sorting'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_sorting'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => array('MetaModels\Dca\AttributeLinkedTags', 'getColumnNames'),
    'eval'             => array
    (
        'includeBlankOption' => true,
        'doNotSaveEmpty' => true,
        'alwaysSave' => true,
        'submitOnChange'=> true,
        'tl_class' => 'w50',
        'chosen' => 'true'
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['mm_displayedValue'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_displayedValue'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => array('MetaModels\Dca\AttributeLinkedTags', 'getColumnNames'),
    'eval'             => array
    (
        'includeBlankOption' => true,
        'doNotSaveEmpty'     => true,
        'alwaysSave'         => true,
        'submitOnChange'     => true,
        'tl_class'           => 'w50',
        'chosen'             => 'true'
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['mm_filter'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_filterSql'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => array('MetaModels\Dca\AttributeLinkedTags', 'getFilters'),
    'eval'             => array
    (
        'includeBlankOption' => true,
        'alwaysSave'         => true,
        'submitOnChange'     => true,
        'tl_class'           => 'w50',
        'chosen'             => 'true'
    ),
);

$GLOBALS['TL_DCA']['tl_metamodel_attribute']['fields']['mm_filterparams'] = array
	(
	'label'		 => &$GLOBALS['TL_LANG']['tl_content']['mm_filterparams'],
	'exclude'	 => true,
	'inputType'	 => 'mm_subdca',
	'eval'		 => array
		(
		'tl_class'	 => 'clr m12',
		'flagfields' => array
			(
			'use_get' => array
				(
				'label'		 => &$GLOBALS['TL_LANG']['tl_content']['metamodel_filterparams_use_get'],
				'inputType'	 => 'checkbox'
			),
		),
	)
);
