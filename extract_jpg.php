<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

$fileName = $_FILES["file"]["name"];
$tempName = $_FILES["file"]["tmp_name"];
$fileKey = explode(".", $fileName);
$extension = end($fileKey);
$videoPath = "upload/";
//var_dump($_FILES);

function alert($result){
    
    echo "<meta http-equiv='content-type' content='text/html; charset=UTF-8'>";
    echo "<SCRIPT LANGUAGE='javascript'>";
    echo "alert('$result');";
    echo "history.back();";
    echo "</SCRIPT>";
    exit;

}


function check_file(){
    
    global $extension;

    $allowExts = array("mkv", "mpeg", "mp4", "m4v");

    if($_FILES["file"]["size"]==0){
        
        $result = "未選擇檔案";
        alert($result);
        
    }elseif(!in_array($extension, $allowExts)){
        
        $response = array(
                "result" => false,
                "status" => "Error",
                "message" => "File type error!",
        );
        
        return $response;
        
    }else{
        
        check_folder();
        
        $response = array(
                "result" => true,
                "status" => "Success",
                "message" => "Success upload",
        );
        
        return $response;
        
    }
};


function check_folder()
{
    global $videoPath;
    
    if(!file_exists($videoPath)){
        
        mkdir($videoPath, 0755, true);
    
    }
};


function extract_jpg($out)
{
    
//暫時寫死切割時間與片段以及輸出frames數
    global $fileName;    
    
    $begin_time = "00:00:00";
    $segment = "00:00:00.20";
    $target = $out;
    $frames = "25";
    $interval = 5;
    $size = '320x240';
    $image = $fileName . ".%4d.jpg";
    
    $cmd = "ffmpeg -i $target -ss $begin_time -t $segment -r 15 -s $size $image";   
    
    shell_exec($cmd);
    
    $response = array(
                "result" => true,
                "status" => "Success",
                "message" => "處理完畢",
    );
    
    return $response;
    
};


function move_file()
{
    
    global $extension, $videoPath, $tempName;
    
    $out = $videoPath . rand() . "." . $extension;
    
    move_uploaded_file($tempName, $out);
    
    return $out;

}


if (check_file()["result"] && $_FILES['file']['error'] === UPLOAD_ERR_OK) { 
    
    $out = move_file();
    
    alert(extract_jpg($out)["message"]);
    
} else { 
    
    alert(check_file()["message"]);

} 


?>