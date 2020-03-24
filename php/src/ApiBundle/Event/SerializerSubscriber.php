<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/13/16
 * Time: 2:45 PM
 */

namespace ApiBundle\Event;

use ApiBundle\Entity\BasePathAware;
use ApiBundle\Responses\AbstractResponse;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class SerializerSubscriber implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
		);
	}

	public function onPreSerialize(ObjectEvent $event)
	{
		if ($event->getObject() instanceof BasePathAware) {
			$event->getObject()->setBasePath('my_path');
		}

	}
}