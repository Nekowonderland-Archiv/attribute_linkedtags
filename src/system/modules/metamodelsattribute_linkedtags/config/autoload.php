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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'MetaModels\Attribute\Tags\LinkedTags'                                     => 'system/modules/metamodelsattribute_linkedtags/MetaModels/Attribute/Tags/LinkedTags.php',
	'MetaModels\DcGeneral\Events\Table\Attribute\LinkedTags\PropertyAttribute' => 'system/modules/metamodelsattribute_linkedtags/MetaModels/DcGeneral/Events/Table/Attribute/LinkedTags/PropertyAttribute.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mm_attr_linkedtags' => 'system/modules/metamodelsattribute_linkedtags/templates',
));
