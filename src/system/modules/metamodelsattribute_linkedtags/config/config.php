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

/**
 * Basic settings.
 */
$GLOBALS['METAMODELS']['attributes']['linkedtags'] = array
(
	'class' => 'MetaModels\Attribute\Tags\LinkedTags',
	'image' => 'system/modules/metamodelsattribute_linkedtags/html/tags.png'
);

/**
 * Events.
 */
$GLOBALS['TL_EVENTS'][\ContaoCommunityAlliance\Contao\EventDispatcher\Event\CreateEventDispatcherEvent::NAME][] =
	'MetaModels\DcGeneral\Events\Table\Attribute\LinkedTags\PropertyAttribute::registerEvents';

/**
 * Add to the filter list.
 */
$GLOBALS['METAMODELS']['filters']['select']['attr_filter'][] = 'linkedtags';
$GLOBALS['METAMODELS']['filters']['tags']['attr_filter'][]   = 'linkedtags';