<?php

namespace App\Services;

class FileService
{
    public function getDirFiles($dir)
    {
        $filenames = array();
        $dir = rtrim($dir, '/'); // удалим слэш на конце
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                chdir($dir);
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != '..') {
                        if (is_dir($file)) {
                            $arr = FileService::getDirFiles($file);
                            foreach ($arr as $value) {
                                $filenames[] = $dir . '/' . $value;
                            }
                        } else {
                            $filenames[] = $dir . '/' . $file;
                        }
                    }
                }
                chdir('../');
            }
            closedir($handle);
        }

        return $filenames;
    }
}
