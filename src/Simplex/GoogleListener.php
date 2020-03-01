<?php

namespace Simplex;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GoogleListener implements EventSubscriberInterface {
    
    public function onResponse(ResponseEvent $event) {
        $response = $event->getResponse();
    
        if ($response->isRedirection()
            || ($response->headers->has('Content-Type')
            && strpos($response->header->get('Content-Type'), 'html'))
            || ($event->getRequest()->getRequestFormat() !== 'html')
        ) {
            return;
        }
    
        $response->setContent($response->getContent().'GA CODE');
    }

    public static function getSubscribedEvents() {
        return ['response' => 'onResponse'];
    }
}