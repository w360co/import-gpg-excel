<?php

namespace W360\ImportGpgExcel\Imports;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeImport;
use W360\ImportGpgExcel\Contracts\ToRow;
use W360\ImportGpgExcel\Events\Processing;
use W360\ImportGpgExcel\Models\Import;

class GpgImport implements
    WithStartRow,
    WithEvents,
    ShouldQueue,
    WithChunkReading,
    WithBatchInserts,
    ToCollection,
    ToRow,
    WithHeadingRow
{

    use Importable {
        getConsoleOutput as traitGetConsoleOutput;
    }


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
            }
        ];
    }

    /**
     * @return void
     */
    private function event($report)
    {
        if (!is_array($report)) {
            return;
        }
        Processing::dispatch($this->gpgImport->name, $report);
    }

    /**
     * @param $message
     * @param bool $skip
     * @return false
     * @throws Exception
     */
    public function exception($message, $skip = true)
    {
        if(!empty($this->gpgImport->storage)) {
            Storage::disk($this->gpgImport->storage)->append(
                $this->gpgImport->storage . DIRECTORY_SEPARATOR . $this->gpgImport->report,
                $message
            );
        }

        if($skip){
            return false;
        }else{
            throw new Exception($message);
        }
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
        return 100;
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        if(method_exists($this, 'rows')){
            $report = $this->rows($rows);
            $this->event($report);
        }
    }

    /**
     * function get rows in gpg encrypted xlsx file
     *
     * @uses
     * public function rows(Collection $rows){
     *     foreach($rows as $row)
     *     {
     *         echo $row['column_name'];
     *     }
     * }
     *
     * @param Collection $rows
     * @return int[]
     * @throws Exception
     */
    public function rows(Collection $rows){
        $success = 0;
        $errors = 0;
        foreach($rows as $row)
        {
            $rowInsert = $this->row($row);
            if($rowInsert){
                $success += 1;
            }else{
                $errors += 1;
            }
        }
        return ['success' => $success, 'errors' => $errors];
    }

    /**
     * function get row in gpg encrypted xlsx file
     *
     * @uses
     * public function row($row){
     *   $row['column_name'];
     * }
     *
     * @param array $row
     * @return bool
     * @throws Exception
     */
    public function row(array $row): bool
    {
        return $this->exception('unread row', false);
    }
}