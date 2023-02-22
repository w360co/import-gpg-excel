<?php

namespace W360\ImportGpgExcel\Listeners;



use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use W360\ImportGpgExcel\Events\Decrypting;
use W360\ImportGpgExcel\Events\Importing;


class FileDecrypter
{


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param Decrypting $event
     */
    public function handle(Decrypting $event)
    {
        if ($event->import) {
            $realPath = Storage::disk($event->import->storage)->path($event->import->storage . '/' . $event->import->name);
            if (file_exists($realPath)) {
                $ext = pathinfo($realPath, PATHINFO_EXTENSION);
                $extOut = strtolower(config('gnupg.extension_output', 'XLSX'));
                $filepathOut = str_replace(".$ext", ".$extOut", $realPath);
                if($this->decryptGpg($realPath, $filepathOut, '@wells.123')){
                    if(file_exists($filepathOut)) {
                       event(new Importing($event->import));
                    }
                }
            }
        }
    }


    /**
     * @param $input
     * @param $output
     * @param $passphrase
     * @return string
     */
    private function decryptGpg($input, $output): string
    {
        if($this->importGpg()) {
            $process = new Process([
                'gpg',
                '--pinentry-mode',
                'loopback',
                '--passphrase=' . config('gnupg.secret_passphrase'),
                '-o',
                $output,
                '--yes',
                '--no-tty',
                '--skip-verify',
                '-d',
                $input
            ]);

            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            return $process->isSuccessful();
        } else {
            return false;
        }
    }


    /**
     * @return string
     */
    private function importGpg(): string
    {
        $process = new Process([
            'gpg',
            '--import',
            config('gnupg.private_key')
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->isSuccessful();

    }
}