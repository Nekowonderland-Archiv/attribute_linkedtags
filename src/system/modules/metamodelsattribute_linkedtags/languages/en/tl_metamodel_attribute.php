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
 * Legends
 */
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['display_legend'] = 'Display settings';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['typeOptions']['linkedtags'] = 'Linked tags';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_table']                  = array('Database table', 'Please select the database table.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_displayedValue']         = array('Table column', 'Please select the column.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_sorting']                = array('Tag sorting', 'Please select a entry for the tag sorting.');
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_filterparams']           = array('Filter parameters', 'Here you can choose a default value for the filter.');

/**
 * Selects
 */
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_sorting_headlines']['meta']    = 'Meta fields';
$GLOBALS['TL_LANG']['tl_metamodel_attribute']['mm_sorting_headlines']['default'] = 'MetaModels fields';
