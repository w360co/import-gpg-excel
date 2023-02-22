<?php

namespace W360\ImportGpgExcel\Imports;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use W360\ImportGpgExcel\Events\Processing;
use W360\ImportGpgExcel\Models\Import;

class GpgImport implements
    WithStartRow,
    WithEvents,
    WithProgressBar,
    WithBatchInserts
{

    use Importable {
        getConsoleOutput as traitGetConsoleOutput;
    }

    /**
     * @var
     */
    public $prevPercent;

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
     * @return OutputStyle
     */
    public function getConsoleOutput(): OutputStyle
    {
        $batchSize = $this->batchSize();
        if(($this->import->processed_rows + $batchSize) >= $this->import->total_rows){
            $this->import->processed_rows = $this->import->total_rows;
        }else{
            $this->import->processed_rows += $batchSize;
        }
        $this->import->save();
        $this->event();
        return $this->traitGetConsoleOutput();
    }

    /**
     * @return \Closure[]
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $this->import->total_rows = Arr::first($event->getReader()->getTotalRows()) - ($this->startRow() - 1);
                $this->import->state = 'processing';
                $this->import->save();
                $this->event();
            },
            ImportFailed::class => function(ImportFailed $event) {
                $this->import->failed_rows += 1;
                $this->import->processed_rows -= 1;
                $this->import->state = 'failed';
                $this->import->save();
                $this->event();
            }
        ];
    }

    /**
     * @return void
     */
    private function event()
    {
        $percent = ($this->import->processed_rows / $this->import->total_rows) * 100;
        if ($percent === $this->prevPercent) {
            return;
        }

        $this->prevPercent = $percent;
        if ($percent >= 100) {
            $percent = 100;
            $this->import->state = 'completed';
        }
        $this->import->percent = $percent;
        $this->import->save();

        Processing::dispatch($percent,$this->import->name);
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
    public function batchSize(): int
    {
        return 1000;
    }

}