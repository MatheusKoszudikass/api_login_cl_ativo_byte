<?php

namespace App\Service\Util;

use App\Util\ResultOperation;

class ResultOperationService
{
    /**
     * Creates a ResultOperation object with success flag set to true.
     *
     * @param string $message Optional message to be returned.
     * @param array $data Optional data to be returned.
     *
     * @return ResultOperation A ResultOperation object with success set to true.
     */
    public function createSuccess(string $message = '', array $data = []): ResultOperation
    {
        return new ResultOperation(true, $message, $data);
    }

    /**
     * Creates a ResultOperation object with success flag set to false.
     *
     * @param string $message Message to be returned when the operation fails.
     *
     * @return ResultOperation A ResultOperation object with success set to false.
     */
    public function createFailure(string $message): ResultOperation
    {
        return new ResultOperation(false, $message);
    }
}
