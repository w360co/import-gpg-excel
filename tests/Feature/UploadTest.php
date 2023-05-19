<?php

namespace W360\ImportGpgExcel\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Jobs\QueueImport;
use W360\ImportGpgExcel\Events\Processing;
use W360\ImportGpgExcel\Facades\ImportGPG;
use W360\ImportGpgExcel\Imports\UsersImport;
use W360\ImportGpgExcel\Models\Import;
use W360\ImportGpgExcel\Tests\TestCase;
use W360\ImportGpgExcel\Traits\HasStorage;

class UploadTest extends TestCase
{
    use HasStorage;

    /**
     * @test
     */
    public function save_image_in_database(){
        Import::truncate();
        factory(Import::class)->create();
        $this->assertCount(1, Import::all(), 'Database Images Is Empty');
    }

    /**
     * @test
     */
    public function upload_and_create_new_file_to_storage(){

       $storage = 'imports';
       Storage::fake($this->getDriver($storage));
       Queue::fake();

       $filename =  realpath(__DIR__ . '/files/mock.xlsx.gpg');
       if(file_exists($filename)) {
           $upload = $this->getUploadedFile($filename);

           $file = ImportGPG::create($upload, $storage, UsersImport::class);

           $this->assertEquals(UsersImport::class, $file->model_type, 'No save model type');
           $this->assertNotEmpty($file->name, 'No save image name');

           $import = Import::where('id', $file->id)->first();

           $this->assertEquals('3', $import->total_rows);
           Queue::assertPushed(QueueImport::class);

       }else{
           $this->assertTrue(false, 'file test no fond PATH:'. $filename);
       }
    }


    /**
     * @param $path
     * @param int|null $error
     * @param bool $test
     * @return UploadedFile
     */
    private function getUploadedFile( $path, int $error = null, bool $test = false)
    {
        $name = File::name( $path );
        $extension = File::extension( $path );
        $originalName = $name . '.' . $extension;
        $mimeType = File::mimeType( $path );
        return new UploadedFile( $path, $originalName, $mimeType, $error, $test );
    }


}