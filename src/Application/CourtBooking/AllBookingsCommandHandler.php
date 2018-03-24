<?php

namespace Application\CourtBooking;

use Domain\Model\ReservaRespository;

/**
 *
 * @author <dvaqueiro at boardfy dot com>
 */
class AllBookingsCommandHandler
{
    /**
     * @var ReservaRespository
     */
    private $reservaRepository;
    
    function __construct(ReservaRespository $reservaRepository)
    {
        $this->reservaRepository = $reservaRepository;
    }

    public function handle(AllBookingsCommand $command)
    {
        $limit = $command->getLimit();
        $orderBy = $command->getOrderBy();

        return $this->reservaRepository->findAll($limit, $orderBy);
    }
}