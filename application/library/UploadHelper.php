<?php

/**
 * 文件上传辅助类
 * @author ldb
 * @package mxcommon_lib
 */
class UploadHelper {

	/**
	 * 最大上传大小，10Ｍ
	 * @var integer
	 */
	public static $max_size = 10485760;

    /**
     * 上传路径 模板
     */
    public static function getUploadTemplatePath() {
        $dir =  Core_ConfigUpload::$upload_template_path[CONFIG_ENV] . Yaf_Session::getInstance()->user . "/";
        return $dir.date("Ym")."/";
    }

    /**
     * 上传路径 文档
     */
    public static function getUploadDocPath() {
        $dir = Core_ConfigUpload::$upload_doc_path[CONFIG_ENV] . Yaf_Session::getInstance()->user . "/";
        return $dir.date("Ym")."/";
    }

    /**
     * 上传路径 临时
     */
    public static function getUploadTempPath() {
        return Core_ConfigUpload::$upload_temp_path[CONFIG_ENV] . Yaf_Session::getInstance()->user . "/";
    }


	/**
	 * 创建目录
	 * @param  string $dir 目录路径
	 * @return bool
	 */
	public static function mkDirs($dir) {
        if (!is_dir($dir)) {
            if (!self::mkDirs(dirname($dir))) {
                return false;
            }
            if (!mkdir($dir, 0777)) {
                return false;
            }
            chmod($dir, 0777);
        }
        return true;
    }

    /**
     * 删除一天之前的文件
     *
     * @param  string $dir 原文件完整路径
     * @return bool
     *
     */
    public static function deldir($dir) {
        $dir = realpath($dir);
        if (in_array($dir,array('/',false)) ) {
            return false;
        }

        //先删除目录下的文件：
        $dh  = @opendir($dir);
        $now = time();
        while ( false !==($file=@readdir($dh)) ){
            if( $file != '.' && $file != '..' ) {
                $fullpath    = $dir."/".$file;
                $update_time = filectime($fullpath);
                if ( $now-$update_time < 60*60*24 ) {
                    continue;
                }
                if(!is_dir($fullpath)) {
                    @unlink($fullpath);
                } else {
                    @deldir($fullpath);
                }
            }
        }
        @closedir($dh);
        return true;
    }

    /**
     * 移动文件
     *
     * @param  string $oldname 原文件完整路径
     * @param  string $newpath 要移动到的文件目录
     * @param  string $newname 新文件名
     * @return bool
     *
     * @author ldb
     */
    public static function rename($oldname,$newpath,$newname=null){
        if(!is_dir($newpath)){
            Core_UploadHelper::mkDirs($newpath);
        }
        if(!$newname){
            $file_info = pathinfo($oldname);
            $newname   = $file_info['basename'];
        }
        return rename($oldname, $newpath.$newname);
    }
}