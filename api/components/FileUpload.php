<?php


namespace api\components;

class FileUpload
{
    public $error     = [];
    public $isSuccess = false;
    public $file;
    public $ready_path;
    public $file_type;
    public $file_size;
    public $file_extension;
    public $file_tmp_name;

    public $file_name;
    public $save_path;

    public function __construct($file, $save_path, $ready_path, $required = false)
    {
        if (!isset($_FILES[$file])) {
            if ($required) {
                $this->error = ['message' => $file . ' is required'];
            }
            return $this;
        }
        $this->save_path      = $save_path;
        $this->ready_path     = $ready_path;
        $this->file_type      = strtolower($_FILES[$file]['type']);
        $this->file_name      = $_FILES[$file]['name'];
        $this->file_tmp_name  = $_FILES[$file]['tmp_name'];
        $this->file_size      = $_FILES[$file]['size'];
        $this->file_extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function setError($error)
    {
        if (empty($this->error)) {
            $this->error = $error;
        }
    }
}