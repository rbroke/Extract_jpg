<?php

//ini_set( 'display_errors', 1 );
//error_reporting( E_ALL );
// HHVM BUG 無法偵測影片超過  max_post_size 問題，但此可以由前端操控（form）
// 其實有一個困惑，為什麼一定要移到 temp folder 才能編輯？$_FILES的生命到底有多長？的生命到底有多長？


// 主要  function extract_jpg( $target, $dist, $begin_time, $segment, $frames, $jpgsize )
// 範例           extract_jpg( $target, $store, "00:00:00", "00:00:10", 25, "320x240" )
$fileName = $_FILES["file"]["name"];
$tempName = $_FILES["file"]["tmp_name"];
$fileKey = explode( ".", $fileName );
$type = end( $fileKey );
$tempPath = "temp/";
$outPath = "frames/";

// 可調變的選項
$store = "rbroke";
$begin_time = "00:00:00";
$segment = "00:00:10";
$frames = 25;
$jpgsize = "320x240";
//var_dump($_FILES);


function alert( $result ){
    
    echo "<meta http-equiv='content-type' content='text/html; charset=UTF-8'>";
    echo "<SCRIPT LANGUAGE='javascript'>";
    echo "alert('$result');";
    echo "history.back();";
    echo "</SCRIPT>";
    exit;

}


function check_file( $fileName ){

    global $type;
    
    $allowExts = array( "mkv", "mpeg", "mp4", "m4v" );

    if( $_FILES["file"]["size"]==0 ){
        
        $result = "未選擇檔案。( 或是超過伺服器 max_post_size )";
        
        alert($result);

    }elseif( !in_array( $type, $allowExts )){
        
        $response = array(
                "result" => false,
                "status" => "Error",
                "message" => "File type error!",
        );
        
        return $response;
        
    }else{
        
        
        global $tempPath;
        check_folder( $tempPath );
        
        $response = array(
                "result" => true,
                "status" => "Success",
                "message" => "Success upload",
        );
        
        return $response;
        
    }
};


function check_folder( $folder )
{
    
    if( !file_exists( $folder ) ){
        
        mkdir( $folder, 0755, true );
    
    }
};


function extract_jpg( $target, $dist, $begin_time, $segment, $frames, $jpgsize )
{
    
    global $outPath;
    $Path = $outPath . "/" . $dist;
    check_folder( $Path );
    
    $cmd = "cd $Path && ffmpeg -i ../../$target -ss $begin_time -t $segment -r $frames -s $jpgsize $dist.'%4d.jpg' ";   
//    $cmd = "ffmpeg -i $target -ss $begin_time -t $segment -r $frames -s $jpgsizeb -an $Path/$dist%4d.jpgs ";   

    shell_exec( $cmd );
    
    // 弔詭的無效！！！？ 只好用蠢方法...先移到 folder 再切割
//    foreach (glob("*.jpg") as $filename) {
//        move_uploaded_file( $filename, $Path );
//        echo "$filename size " . filesize($filename) . "\n<pre>" ;
//    }
    
    $response = array(
                "result" => true,
                "status" => "Success",
                "message" => "處理完畢",
    );
    
    return $response;
    
};


function save_temp_file( $tempPath )
{
    
    global $type, $tempName;
    
    $move_result = $tempPath . rand() . "." . $type;
    
    move_uploaded_file( $tempName, $move_result );
    
    return $move_result;

}


if ( check_file( $fileName )["result"] && $_FILES['file']['error'] === UPLOAD_ERR_OK ) { 
    
    $target = save_temp_file( $tempPath );
    
    $result = extract_jpg( $target, $store, $begin_time, $segment, $frames, $jpgsize )["message"];
    
    
} else { 

    $result = check_file( $fileName )["message"];

} 

alert( $result );


?>