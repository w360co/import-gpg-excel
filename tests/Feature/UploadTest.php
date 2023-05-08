<?php

namespace W360\ImportGpgExcel\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use W360\ImportGpgExcel\Facades\ImportGPG;
use W360\ImportGpgExcel\Imports\UsersImport;
use W360\ImportGpgExcel\Models\Import;
use W360\ImportGpgExcel\Tests\TestCase;

class UploadTest extends TestCase
{

    /**
     * @test
     */
    public function save_image_in_database(){
        factory(Import::class)->create();
        $this->assertCount(1, Import::all(), 'Database Images Is Empty');
    }

    /**
     * @test
     */
    public function upload_and_create_new_file_to_storage(){

       $storage = 'local';
       Storage::fake($storage);

       $filename =  realpath(__DIR__ . '/files/mock.xlsx.gpg');
       if(file_exists($filename)) {
           $upload = $this->getUploadedFile($filename);

           $file = ImportGPG::create($upload, $storage, UsersImport::class);

           $this->assertEquals(UsersImport::class, $file->model_type, 'No save model type');
           $this->assertNotEmpty($file->name, 'No save image name');

           $import = Import::where('id', $file->id)->first();
           $this->assertEquals('3', $import->processed_rows);
           $this->assertEquals('3', $import->total_rows);


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