<?php

/**
 * Class Shopware_Controllers_Api_Ticket
 */
class Shopware_Controllers_Api_Ticket extends Shopware_Controllers_Api_Rest
{
    /**
     * @var Shopware\Components\Api\Resource\Ticket
     */
    protected $resource;

    public function init()
    {
        $this->resource = \Shopware\Components\Api\Manager::getResource('Ticket');
    }

    /**
     * GET Request on /api/Ticket
     */
    public function indexAction()
    {

        $limit = $this->Request()->getParam('limit', 1000);
        $offset = $this->Request()->getParam('start', 0);
        $sort = $this->Request()->getParam('sort', []);
        $filter = $this->Request()->getParam('filter', []);

        $result = $this->resource->getList($offset, $limit, $filter, $sort);

        $this->View()->assign(['success' => true, 'data' => $result]);
    }

    /**
     * Create new Ticket
     *
     * POST /api/Ticket
     */
    public function postAction()
    {
        $ticket = $this->resource->create($this->Request()->getPost());

        $location = $this->apiBaseUrl . 'Ticket/' . $ticket->getId();

        $data = [
            'id' => $ticket->getId(),
            'location' => $location,
        ];
        $this->View()->assign(['success' => true, 'data' => $data]);
        $this->Response()->setHeader('Location', $location);
    }

    /**
     * Get one Ticket
     *
     * GET /api/Ticket/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');
        /** @var \SwagTicketSystem\Models\Ticket\Support $ticket */
        $ticket = $this->resource->getOne($id);

        $this->View()->assign(['success' => true, 'data' => $ticket]);
    }

    /**
     * Update One Ticket
     *
     * PUT /api/Ticket/{id}
     */
    public function putAction()
    {
        $ticketId = $this->Request()->getParam('id');
        $params = $this->Request()->getPost();

        /** @var \SwagTicketSystem\Models\Ticket\Support $ticket */
        $ticket = $this->resource->update($ticketId, $params);

        $location = $this->apiBaseUrl . 'Ticket/' . $ticketId;
        $data = [
            'id' => $ticket->getId(),
            'location' => $location,
        ];

        $this->View()->assign(['success' => true, 'data' => $data]);
    }

    /**
     * Delete One Ticket
     *
     * DELETE /api/Ticket/{id}
     */
    public function deleteAction()
    {
        $ticketId = $this->Request()->getParam('id');

        $this->resource->delete($ticketId);

        $this->View()->assign(['success' => true]);
    }
}
