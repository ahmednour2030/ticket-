<?php

namespace App\Traits;

trait FileUploadTrait {

    /**
     * @param $file
     * @param null $folder
     * @return string
     */
    public function UploadFile($file, $folder = null): string
    {
        // $filename   = $image->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName =  time().'_file.'.$extension;

        if(is_null($folder)){
            $file->storeAs('public/files', $fileName);
            return $fileName;
        }

        $file->storeAs('public/files/' . $folder, $fileName);
      //  $image->storeAs('public/images/' . $folder, $fileName);


        return $fileName;
    }
}
