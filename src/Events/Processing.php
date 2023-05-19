<?php

namespace W360\ImportGpgExcel\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use W360\ImportGpgExcel\Models\Import;

class Processing implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * Percent of processing
     *
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $report;

    /**
     * @param $name
     * @param $report
     */
    public function __construct($name, $report)
    {
        if (empty($name) || !is_array($report)) {
            throw new \InvalidArgumentException('No support report to file');
        }
        $this->name = $name;
        $this->report = json_encode($report);
        $model = Import::where('name', $name)->where('state', 'processing')->first();
        if($model && $model->total_rows > 0){
            $model->processed_rows += $report['success'] ?? 0;
            $model->failed_rows += $report['errors'] ?? 0;
            $model->percent = (($model->processed_rows + $model->failed_rows) / $model->total_rows) * 100;
            if ($model->percent >= 100) {
                $model->state = 'completed';
            }
            $model->save();
            $this->report = $model->percent;
            $this->name = $model->id."-".$model->name;
            if ($model->percent >= 100) {
                event(new Deleting($model));
            }
        }
    }

    public function broadcastOn()
    {
        return new Channel('processing.' . $this->name);
    }
}
