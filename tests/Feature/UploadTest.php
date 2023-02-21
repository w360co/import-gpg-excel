<?php

namespace W360\ImportGpgExcel\Tests\Feature;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use W360\ImportGpgExcel\Facades\ImportGPG;
use W360\ImportGpgExcel\Models\Import;
use W360\ImportGpgExcel\Models\User;
use W360\ImportGpgExcel\Tests\TestCase;

class UploadTest extends TestCase
{

    use DatabaseMigrations, RefreshDatabase;

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

       $storage = 'files';
       Storage::fake($storage);

       $upload = UploadedFile::fake()->createWithContent('test.xlsx.gpg',
           file_get_contents(__DIR__."/files/test.xlsx.gpg")
       );
       $file = ImportGPG::create($upload, $storage, User::class);

       $this->assertEquals(User::class, $file->model_type,'No save model type' );
       $this->assertNotEmpty($file->name,'No save image name' );

       Storage::disk($file->storage)->assertExists($file->storage."/".$file->name);

    }


}