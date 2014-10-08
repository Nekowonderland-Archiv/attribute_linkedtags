<?php

namespace MetaModels\Attribute\LinkedTags;

use MetaModels\Attribute\AbstractAttributeTypeFactory;
use MetaModels\Attribute\Events\CreateAttributeFactoryEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AttributeTypeFactory extends AbstractAttributeTypeFactory implements EventSubscriberInterface
{
	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			CreateAttributeFactoryEvent::NAME => 'registerLegacyAttributeFactoryEvents'
		);
	}

	/**
	 * Register all legacy factories and all types defined via the legacy array as a factory.
	 *
	 * @param CreateAttributeFactoryEvent $event The event.
	 *
	 * @return void
	 */
	public static function registerLegacyAttributeFactoryEvents(CreateAttributeFactoryEvent $event)
	{
		$factory = $event->getFactory();
		$factory->addTypeFactory(new static());
	}

	/**
	 * Create a new instance.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->typeName  = 'linkedtags';
		$this->typeClass = 'MetaModels\Attribute\LinkedTags\LinkedTags';
	}
}
