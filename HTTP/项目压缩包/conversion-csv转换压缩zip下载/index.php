<?php
    require 'Parse.php';
    $import = new Parse();
    $file = $_FILES['file'];
    $keyword = $_FILES['keyword'];
    $tags_num = $_POST['tags'] ? $_POST['tags'] : '';
    $desc_num = !empty($_POST['desc']) ? $_POST['desc'] : '';
    $title = $_POST['title'];
    if(!empty($_POST['num']))
    {
        $num = $_POST['num'];
    }else{
        echo '<script>alert("每页数量不能为空！");</script>';
        echo '<script>history.go(-1);</script>';
    }
    $filename = explode('.',$file['name'],2);
    $data = $import->deal($file['tmp_name'],$keyword['tmp_name'],$desc_num,$tags_num,$title);
    $result = $import->import($data,$num,$filename[0]);
