<?php

namespace W360\ImportGpgExcel\Contracts;

use Exception;
use Illuminate\Support\Collection;

interface ToRow
{

    /**
     *
     * @param array | Collection $row
     * @return mixed
     * @throws Exception
     */
    public function row($row): bool;
}