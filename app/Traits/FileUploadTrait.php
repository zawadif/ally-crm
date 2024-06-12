<?php

namespace App\Traits;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\File; 

/**
 * This trait was built to easily upload files based on a path set in your model
*/
trait FileUploadTrait
{

    /**
     * The method responsible to upload the file to the right location
     *
     * @param UploadedFile $file File to be uploaded
     * @param String $path the path where the file will be uploaded... if path is not set, getBaseUploadFolderPath() will be called
     * @return String The file path in the filesystem... Save it to the database
     */
    public function uploadFile(UploadedFile $file, $path = null)
    {
        $path = $this->prepareFilePath($path);
        return $this->uploadToS3($file, $path);
    }
    /**
     * It will prepare the file path(the base path for the file) based on the environment
     *  Dev and production envs have some differences, plz check the code below
     *
     * @param String $filePath
     * @return String
     */
    private function prepareFilePath($filePath) {

        $filePath = rtrim($filePath, '/');
        return $filePath;
    }

    /**
     * It will upload a file to the s3 bucket defined in the env file
     *
     * @param UploadedFile $file
     * @param String $filePath
     * @return String
     */
    private function uploadToS3($file, $filePath) {
        return $file->store($filePath, 's3');
    }
   /**
     * It will generate a URL for your model attribute
     *
     * @param String $attribute The attribute of your model that has a upload file path
     * @return String A url for your file. Use this method in your views
     */
    public function getfileUrl($path) {
        $imagePath=Storage::disk('s3')->url($path);
        return $imagePath;
    }

    /**
     * It will delete the upload file related to an attribute
     *
     * @param Array $something The attribute or an array of attributes of your model that has a upload file path
     * @return void
     */
    public function deleteFile($path) {

        $storage=Storage::disk('s3')->exists($path);
        if($storage){
          Storage::disk('s3')->delete($path);
        }

    }

    public function storeImageInPublic($file){
        $fileName =  time().'.'. $file->getClientOriginalExtension();
        $file->move('img/avatar/',$fileName);
        return $fileName;
    }

    public function deleteImageFromPublic($avatar)
    {
        if(File::exists('img/avatar/'.$avatar)){
            File::delete('img/avatar/'.$avatar);
        }
    }

}
