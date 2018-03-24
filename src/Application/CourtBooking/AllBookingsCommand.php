<?php

namespace Application\CourtBooking;

/**
 *
 * @author <dvaqueiro at boardfy dot com>
 */
class AllBookingsCommand
{
    private $orderBy;
    private $limit;

    function __construct($limit, $orderBy)
    {
        $this->limit = $limit;
        $this->orderBy = $orderBy;
    }

    function getLimit()
    {
        return $this->limit;
    }

    function getOrderBy()
    {
        return $this->orderBy;
    }

}