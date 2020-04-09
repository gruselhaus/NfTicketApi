<?php

namespace Shopware\Components\Api\Resource;

use Shopware\Components\Api\Exception as ApiException;
use SwagTicketSystem\Models\Ticket\Support as SupportModel;

/**
 * Class Ticket
 *
 * @package Shopware\Components\Api\Resource
 */
class Ticket extends Resource
{
    public function getRepository()
    {
        /**
         * @return \SwagTicketSystem\Models\Ticket\Repository
         */
        return $this->getManager()->getRepository(SupportModel::class);
    }

    /**
     * Create new Ticket
     *
     * @param array $params
     * @return SupportModel
     * @throws ApiException\ValidationException
     */
    public function create(array $params)
    {
        /** @var SupportModel $ticket */
        $ticket = new SupportModel();

        $ticket->fromArray($params);

        $violations = $this->getManager()->validate($ticket);

        /**
         * Handle Violation Errors
         */
        if ($violations->count() > 0) {
            throw new ApiException\ValidationException($violations);
        }

        $this->getManager()->persist($ticket);
        $this->flush();

        return $ticket;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param array $criteria
     * @param array $orderBy
     * @return array
     */
    public function getList($offset = 0, $limit = 25, array $criteria = [], array $orderBy = [])
    {
        $builder = $this->getRepository()->createQueryBuilder('ticket');

        $builder->addFilter($criteria)
            ->addOrderBy($orderBy)
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        $query = $builder->getQuery();
        $query->setHydrationMode($this->resultMode);

        $paginator = $this->getManager()->createPaginator($query);

        //returns the total count of the query
        $totalResult = $paginator->count();

        //returns the Banner data
        $ticket = $paginator->getIterator()->getArrayCopy();

        return ['total' => $totalResult, 'data' => $ticket];
    }

    /**
     * Delete Existing Ticket
     *
     * @param $id
     * @return null|object
     * @throws ApiException\NotFoundException
     * @throws ApiException\ParameterMissingException
     */
    public function delete($id)
    {
        $this->checkPrivilege('delete');

        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

        $ticket = $this->getRepository()->find($id);

        if (!$ticket) {
            throw new ApiException\NotFoundException("Ticket by id $id not found");
        }

        $this->getManager()->remove($ticket);
        $this->flush();
    }

    /**
     * Get One Ticket Information
     *
     * @param $id
     * @return mixed
     * @throws ApiException\NotFoundException
     * @throws ApiException\ParameterMissingException
     */
    public function getOne($id)
    {
        $this->checkPrivilege('read');

        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

        $builder = $this->getRepository()
            ->createQueryBuilder('ticket')
            ->select('ticket')
            ->where('ticket.id = ?1')
            ->setParameter(1, $id);

        /** @var SupportModel $ticket */
        $ticket = $builder->getQuery()->getOneOrNullResult($this->getResultMode());

        if (!$ticket) {
            throw new ApiException\NotFoundException("Ticket by id $id not found");
        }

        return $ticket;
    }

    /**
     * @param $id
     * @param array $params
     * @return null|object
     * @throws ApiException\ValidationException
     * @throws ApiException\NotFoundException
     * @throws ApiException\ParameterMissingException
     */
    public function update($id, array $params)
    {
        $this->checkPrivilege('update');

        if (empty($id)) {
            throw new ApiException\ParameterMissingException();
        }

        /** @var $ticket SupportModel */
        $builder = $this->getRepository()
            ->createQueryBuilder('ticket')
            ->select('ticket')
            ->where('ticket.id = ?1')
            ->setParameter(1, $id);

        /** @var SupportModel $ticket */
        $ticket = $builder->getQuery()->getOneOrNullResult(self::HYDRATE_OBJECT);

        if (!$ticket) {
            throw new ApiException\NotFoundException("Ticket by id $id not found");
        }

        $ticket->fromArray($params);

        $violations = $this->getManager()->validate($ticket);
        if ($violations->count() > 0) {
            throw new ApiException\ValidationException($violations);
        }

        $this->flush();

        return $ticket;
    }
}
