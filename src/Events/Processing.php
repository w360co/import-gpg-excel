<?php

namespace W360\ImportGpgExcel\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Processing implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * Percent of processing
     *
     * @var int
     */
    public $percent;

    /**
     * @var string
     */
    public $channelID;

    /**
     * @param int $percent
     * @param string $channelID
     */
    public function __construct($percent, $channelID)
    {
        if ($percent < 0 || $percent > 100) {
            throw new \InvalidArgumentException('Percent value out of range');
        }

        $this->percent = $percent;
        $this->channelID = $channelID;
    }

    public function broadcastOn()
    {
        return new Channel('processing.' . $this->channelID);
    }
}