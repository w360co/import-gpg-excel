<?php

namespace W360\ImportGpgExcel\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use W360\ImportGpgExcel\Models\Import;

class Importing
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Import
     */
    public $import;

    /**
     * @param Import $import
     */
    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    /**
     * @return array
     */
    public function broadcastOn(): array
    {
        return [];
    }
}