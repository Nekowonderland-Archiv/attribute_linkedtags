<?php

namespace MetaModels\DcGeneral\Events\MetaModels;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use MetaModels\Attribute\LinkedTags\LinkedTags;
use MetaModels\DcGeneral\Data\Model;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BackendSubscriber
 */
class BackendSubscriber implements EventSubscriberInterface
{
	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents()
	{
		return array
		(
			GetPropertyOptionsEvent::NAME => 'getPropertyOptions'
		);
	}

	/**
	 * Retrieve the property options.
	 *
	 * @param GetPropertyOptionsEvent $event The event
	 *
	 * @return void
	 */
	public function getPropertyOptions(GetPropertyOptionsEvent $event)
	{
		if (substr($event->getModel()->getProviderName(), 0, 3) !== 'mm_') {
			return;
		}

		/** @var Model $model */
		$model     = $event->getModel();
		$item      = $model->getItem();
		$attribute = $item->getMetaModel()->getAttribute($event->getPropertyName());

		if (!($attribute instanceof LinkedTags)) {
			return;
		}

		$event->setOptions($attribute->getFilterOptions(null, false));
	}
}