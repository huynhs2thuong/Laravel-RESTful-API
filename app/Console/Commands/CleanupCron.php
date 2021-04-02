<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $debug_log_file_path;
    function debug_log_set_path($file_name){
        // global $this->debug_log_file_path;
        $this->debug_log_file_path = $file_name;
    }
    function debug_log_json_set_path($file_name){
        global $debug_log_json_file_path;
        $debug_log_json_file_path = $file_name;
    }
    function debug_log($value){
        $file_path = 'debug_log.txt';
        // global $this->debug_log_file_path;
        if(!empty($this->debug_log_file_path)){
            $file_path = $this->debug_log_file_path;
        }
        $file_path = public_path('/uploads/logs/NVof3vWOgy/'.$file_path);

        if(!empty($_COOKIE['debug_clear'])){
            @unlink($file_path);
        } else {
            // $content = getNow();
            $content = date("Y-m-d H:i:s");
            $content .= "\r\n";
            $content .= $value;
            $content .= "\r\n";
            $content .= "\r\n";
            
            @file_put_contents($file_path,$content, FILE_APPEND | LOCK_EX);
        }
    }
    function debug_log_from_config($value){
        $flag_debug_log = env('DEBUG_LOG');
        if (!empty($flag_debug_log)) {
            
            if(is_array($value)){
                $value = print_r($value, true);
            }
            $this->debug_log($value);
        }
    }
    
    function debug_log_json($value){
        $file_path = 'debug_log_json.json';
        global $debug_log_json_file_path;
        if(!empty($debug_log_json_file_path)){
            $file_path = $debug_log_json_file_path;
        }
        $file_path = 'web/upload/logs/'.$file_path;
        if(!empty($_COOKIE['debug_clear'])){
            @unlink($file_path);
        } else {
            @file_put_contents($file_path,$value, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // include '../../Helpers/common_helper.php';
        // $file_path = 'test';
        // $file_path = public_path('/uploads/logs/NVof3vWOgy/'.$file_path);
        // file_put_contents('D:/xampp7/htdocs/WALMART/flagsins/public/uploads/logs/NVof3vWOgy/test.txt', $file_path);
        // $flag = env('MAIL_MAILER');
        // file_put_contents('D:/xampp7/htdocs/WALMART/flagsins/public/uploads/logs/NVof3vWOgy/test.txt', $flag);
        $this->cleanup_downloaded_files();

        

        return 0;
    }
    private function deleteNonEmptyDir($dir) 
    {
        $result = is_dir($dir);
        if (is_dir($dir)) 
        {
            $objects = scandir($dir);

            foreach ($objects as $object) 
            {
                if ($object != "." && $object != "..") 
                {
                    if (filetype($dir . "/" . $object) == "dir")
                    {
                        $this->deleteNonEmptyDir($dir . "/" . $object); 
                    }
                    else
                    {
                        unlink($dir . "/" . $object);
                    }
                }
            }

            reset($objects);
            rmdir($dir);
        }
        return $result;
    }

    private function cleanup_downloaded_files(){
        $download_dir = public_path()."/image/download";
        $files_and_folders = scandir($download_dir);
        $file_mapping_arr = array();
        if(!empty($files_and_folders)){
            foreach($files_and_folders as $resource_filename){
                if(in_array($resource_filename, array('.','..'))){
                    continue;
                }
                
                $flag_delete = false;
                $resource_filepath = $download_dir.'/'.$resource_filename;
                if(file_exists($resource_filepath)){
                    $created_int = @filectime($resource_filepath);
                    $now_int = time();
                    $interval =  $now_int - $created_int;
                    if($interval >= 3600){
                        $flag_delete = true;
                        // chmod($resource_filepath,0777);
                        if($this->deleteNonEmptyDir($resource_filepath)){
                           
                        } else {
                            @unlink($resource_filepath);
                        }
                    }
                }
                $file_mapping_arr[] = array(
                    'resource_filepath' => $resource_filepath,
                    'created_int' => $created_int,
                    'interval' => $interval,
                    'flag_delete' => $flag_delete,
                );
            }
        }


        $this->debug_log_set_path('debug_log_cron.txt');
        $log_arr = array(
            'location' => __FILE__,
            'function' => 'cleanup_downloaded_files',
            'files_and_folders' => !empty($files_and_folders) ? $files_and_folders : '',
            'file_mapping_arr' => !empty($file_mapping_arr) ? $file_mapping_arr : '',
        );
        $this->debug_log_from_config($log_arr);



    }
}
