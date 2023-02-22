<?php

namespace W360\ImportGpgExcel\Imports;

use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use W360\ImportGpgExcel\Events\Deleting;
use W360\ImportGpgExcel\Events\Processing;
use W360\ImportGpgExcel\Models\Import;

class GpgImport implements
    WithStartRow,
    WithEvents,
    ShouldQueue,
    WithChunkReading,
    WithBatchInserts
{

    use Importable {
        getConsoleOutput as traitGetConsoleOutput;
    }

    use RemembersRowNumber;

    /**
     * @var
     */
    public $prevPercent;

    /**
     * @var Import
     */
    public $gpgImport;

    /**
     * @param Import $gpgImport
     */
    public function __construct(Import $gpgImport)
    {
        $this->gpgImport = $gpgImport;
    }

    /**
     * @return \Closure[]
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $this->gpgImport->total_rows = Arr::first($event->getReader()->getTotalRows()) - ($this->startRow() - 1);
                $this->gpgImport->state = 'processing';
                $this->gpgImport->save();
                $this->event();
            },
            ImportFailed::class => function(ImportFailed $event) {
                $this->gpgImport->failed_rows += 1;
                $this->gpgImport->processed_rows -= 1;
                $this->gpgImport->state = 'failed';
                $this->gpgImport->save();
                $this->event();
            }
        ];
    }

    /**
     * @return void
     */
    private function event()
    {
        $batchSize = $this->batchSize();
        if( ($this->gpgImport->processed_rows + $batchSize) >= $this->gpgImport->total_rows ){
            $this->gpgImport->processed_rows = $this->gpgImport->total_rows;
        }else{
            $this->gpgImport->processed_rows +=  $batchSize;
        }

        $percent = ($this->gpgImport->processed_rows / $this->gpgImport->total_rows) * 100;
        if ($percent === $this->prevPercent) {
            return;
        }

        $this->prevPercent = $percent;
        if ($percent >= 100) {
            $percent = 100;
            $this->gpgImport->state = 'completed';
            event(new Deleting($this->gpgImport));
        }
        $this->gpgImport->percent = $percent;
        $this->gpgImport->save();

        Processing::dispatch($percent,$this->gpgImport->name);
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
    * @return int
    */
    public function chunkSize(): int
    {
        return 1000;
    }


    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }


}