<?php

namespace W360\ImportGpgExcel\Contracts;

use Illuminate\Support\Collection;

interface ToRow
{

    /**
     * @param $row
     * @return mixed|null
     */
    public function row($row);
}