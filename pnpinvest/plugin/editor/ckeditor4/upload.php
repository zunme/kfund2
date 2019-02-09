<?php
include_once('../../../../mari_path.php');
include_once('../../../config.php');
$ym = date('ym', MARI_SERVER_TIME);

$data_dir = MARI_DATA_PATH.'/editor/'.$ym;
$data_url = MARI_DATA_URL.'/editor/'.$ym;

@mkdir($data_dir, MARI_DIR_PERMISSION);
@chmod($data_dir, MARI_DIR_PERMISSION);
 
// 마이크로 타임을 얻어 계산 형식으로 만듦
function get_microtime()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

// 업로드 DIALOG 에서 전송된 값
$funcNum = $_GET['CKEditorFuncNum'] ;
$CKEditor = $_GET['CKEditor'] ;
$langCode = $_GET['langCode'] ;
 
if(isset($_FILES['upload']['tmp_name'])) {
    $file = $_FILES['upload']['name'];
    $pos = strrpos($file, '.');
    $filename  = substr($file, 0, $pos);
    $extension = substr($file, $pos, strlen($file) - $pos);

    if (!preg_match("/\.(jpe?g|gif|png)$/i", $extension)) {
        echo '이미지 파일만 가능합니다.';
        return false;
    }

    // 윈도우에서 한글파일명으로 업로드 되지 않는 오류 해결
    $file_name = sprintf('%u', ip2long($_SERVER['REMOTE_ADDR'])).'_'.get_microtime().$extension;
    $save_dir = sprintf('%s/%s', $data_dir, $file_name);
    $save_url = sprintf('%s/%s', $data_url, $file_name);
 
    if (move_uploaded_file($_FILES["upload"]["tmp_name"],$save_dir))
        echo "<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '$save_url', '업로드완료');</script>";
}
?>