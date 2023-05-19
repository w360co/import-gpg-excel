<?php

namespace W360\ImportGpgExcel\Contracts;

use Exception;

interface ToRow
{

    /**
     *
     * @param array $row
     * @return mixed
     * @throws Exception
     */
    public function row(array $row): bool;
}