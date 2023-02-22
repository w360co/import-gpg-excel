<?php

namespace W360\ImportGpgExcel\Imports;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeImport;
use W360\ImportGpgExcel\Events\Processing;
use W360\ImportGpgExcel\Models\Import;
use W360\ImportGpgExcel\Models\User;

class UsersImport implements ToModel,
    WithStartRow,
    WithEvents,
    WithProgressBar,
    WithBatchInserts
{
    use Importable;

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
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new User([
            'name'     => $row[0],
            'email'    => $row[1],
            'password' => Hash::make($row[2]),
        ]);
    }

    /**
     * @return \Closure[]
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $this->import->total_rows = Arr::first($event->getReader()->getTotalRows());
                $this->import->save();
                $this->event();
            }
        ];
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

    /**
     * @return void
     */
    private function event()
    {
        $percent = ($this->import->processed_Rows / $this->import->total_rows) * 100;

        if ($percent === $this->prevPercent) {
            return;
        }

        $this->prevPercent = $percent;

        if ($percent > 100) {
            $percent = 100;
        }

        $this->import->percent = $percent;

        Processing::dispatch($percent,$this->import->name);
    }
}