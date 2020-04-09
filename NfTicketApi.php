<?php

namespace NfTicketApi;

use Shopware\Components\Plugin;

class NfTicketApi extends Plugin
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Api' => 'onGetTicketApiController',
            'Enlight_Controller_Front_StartDispatch' => 'onEnlightControllerFrontStartDispatch',
        ];
    }

    /**
     * @return string
     */
    public function onGetTicketApiController()
    {
        return $this->getPath() . '/Controllers/Api/Ticket.php';
    }

    /**
     *
     */
    public function onEnlightControllerFrontStartDispatch()
    {
        $this->container->get('loader')->registerNamespace('Shopware\Components', $this->getPath() . '/Components/');
    }
}
