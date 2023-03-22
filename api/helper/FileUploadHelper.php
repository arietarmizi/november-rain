<?php

namespace api\helper;

use api\components\FileUpload;
use Yii;

class FileUploadHelper
{

    private $allowed_files;
    private $file_size;
    const IMAGE = 'image';
    const FILE  = 'file';

    /** File Upload
     * @param string $file_name
     * @param string $save_path
     * @param string $ready_path
     * @param string $type
     * @param bool   $required
     * @return FileUpload
     */
    public static function fileUpload($file_name, $save_path, $ready_path, $type, $required = false)
    {

        $image = new FileUpload($file_name, $save_path, $ready_path, $required);
        if ($type == self::FILE) {
            $allowed_files = ['image/jpeg', 'image/gif', 'image/png'];
            $file_size     = 8388608;
        } else {
            $allowed_files = ['image/jpeg', 'image/gif', 'image/png'];
            $file_size     = 8388608;
        }
        $dir = realpath(Yii::$app->basePath);
        if (in_array($image->file_type, $allowed_files)
            && $image->file_size < $file_size) {
            $filename = $file_name . '_' . md5(uniqid(time()) . time() . '_' . date('YmdHis')) . '.' . $image->file_extension;
            $file     = $dir . $image->save_path . $filename;
            if (move_uploaded_file($image->file_tmp_name, $file)) {
                $image->file = $image->ready_path . $filename;
                $image->isSuccess = true;
                $image->setError(['message' => 'file_upload_success']);
            } else {
                $image->setError(['message' => 'error_try_again']);
            }
        } else {
            $image->setError(['message' => 'file_should_be_no_more_than_given_size']);
        }
        return $image;
    }


    /** Delete File
     * @param string $ready_file
     */
    public static function deleteImage($ready_file)
    {
        $dir = realpath(Yii::$app->basePath);
        if (strpos($ready_file, 'api') === false) {
            if (is_file($dir . '/web/' . $ready_file)) {
                unlink($dir . '/web/' . $ready_file);
            }
        }
    }
}