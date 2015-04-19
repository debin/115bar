<?php

/**
 * zip打包下载辅助类
 * @author ldb
 * @package mxcommon_lib
 */
class ZipHelper extends Core_Singleton {

    /**
     * zip打包下载
     *
     * @param  string $file 下载的文件名称
     * @param  array $upload_arr  保存的上传字段
     * @return
     *
     * @author ldb
     * @date(2014-12-19)
     */
    public static function zipDownload($file,array $upload_arr) {
        // 临时下载目录
        $tmp_path = Core_ConfigUpload::getUploadAbsPath().Core_UploadHelper::getUploadTempPath();
        if(!is_dir($tmp_path)){
            Core_UploadHelper::mkDirs($tmp_path);
        }

        $file_name = $tmp_path.$file;
        $zip = new ZipArchive;
        if ($zip->open($file_name,ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) === TRUE) {
            $name_list = array();
            foreach ($upload_arr as $key => $upload) {
                $path = Core_ConfigUpload::getUploadAbsPath().$upload['url'];
                $name = $upload['name'];
                $name = iconv('UTF-8', 'GBK//IGNORE', $name);

                // 多个同名文件处理
                $i         = 1;
                $file_info = pathinfo($name);
                $extension = !empty($file_info['extension'])?$file_info['extension']:'helloworld';
                while(in_array($name,$name_list)){
                    $name = $file_info['filename'].'('.$i++.').'.$extension;
                }
                array_push($name_list, $name);
                // --------------------------

                $zip->addFile($path, $name);
            }
            $zip->close();
            // Wait 2sec. for Creat Zip File
            usleep(2000000);
        } else {
            // echo '无法打开文件，或 者文件创建失败:'.$file_name;
            throw new Exception('无法打开文件，或 者文件创建失败:'.$file_name);
            return;
        }
        if( !file_exists($file_name)){
            // echo "无法找到文件:".$file_name; //即使创建，仍有可能失败。。。。
            throw new Exception("无法找到文件:".$file_name);
            return;
        }
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.basename($file_name)); //文件名
        header("Content-Type: application/zip"); //zip格式的
        header("Content-Transfer-Encoding: binary");    //告诉浏览 器，这是二进制文件
        header('Content-Length: '. filesize($file_name));    //告诉浏览 器，文件大小
        @readfile($file_name);
        // unlink($file_name);
        return;
    }

}