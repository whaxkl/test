<?php
header("Content-type: text/html; charset=utf-8");
//header("Content-Type: text/html;charset=gb2312");
//获取当前文件所在目录
$root=$_SERVER['DOCUMENT_ROOT'];
require($root."/wp-config.php");
@$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(!$conn){
    echo "<script>alert('商品tag标签添加失败');</script>";
    echo "<script>history.back();</script>";
    exit;
}
if(!empty($_POST)) {
    $tag_all = $_POST['tag_all'];
    if ($tag_all == 'tag_all') {
        require 'vendor/autoload.php';
        $file = $_FILES['file'];
        if(empty($file['tmp_name'])){
            echo "<script>alert('上传文件不能为空');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
	$num = $_POST['num'];
	if(empty($num) || floor($num)<=0){
            echo "<script>alert('关键字数量必须大于0');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        move_uploaded_file($file["tmp_name"],
            $file["name"]);
        $csv = new ParseCsv\Csv($file['name']);
        $data = $csv->data;
	if(empty($data)){
            echo "<script>alert('上传文件不能为空');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
	$data = array_column($data,'tag');
	if(empty($data)){
            echo "<script>alert('上传文件不能为空');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
	/*$rand=array_rand($data,$num);
        $keywords=[];
	if(count($rand)==1){
	    $keywords[]=$data[$rand];
	}else{
	    foreach($rand as $ker=>$var){
                $keywords[]=$data[$var];
            }
	}
        $category_sql = "select * from wp_terms";
        $result = $conn->query($category_sql);
        $category= mysqli_fetch_all($result, MYSQLI_ASSOC);
        arsort($category);
        $name=array_column($category,'name');
        $category_id=[];
        $new_keywords=$keywords;
        foreach($category as $kei=>$vai){
            if(in_array($vai['name'],$keywords)){
                $category_id[]=$vai['term_id'];
                $new_keywords=array_diff($new_keywords,[$vai['name']]);
            }
        }
        foreach($new_keywords as $kew=>$vaw){
            $sql = "insert into wp_terms(name,slug,term_group) values('{$vaw}','{$vaw}',0)";
	    $result=$conn->query($sql);
            //$result=mysqli_query($conn,$sql);
            if($result){
                $id=mysqli_insert_id($conn);//获取插入id
		$sql = "insert into wp_termmeta(term_id,meta_key,meta_value) values({$id},'product_count_product_tag',0)";
                $result=$conn->query($sql);
                $sql = "insert into wp_term_taxonomy(term_id,taxonomy,description) values({$id},'product_tag','')";
                $result=$conn->query($sql);
		$category_id[]=$id;
            }
        }*/
        $sql = "select * from wp_posts where ID>9 and post_type='product'";
        $result = $conn->query($sql);
        $data_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        arsort($data_data);
        foreach($data_data as $kee=>$vae){
	    $rand=array_rand($data,$num);
            $keywords=[];
            if(count($rand)==1){
                $keywords[]=$data[$rand];
            }else{
                foreach($rand as $ker=>$var){
                    $keywords[]=$data[$var];
                }
            }
            $category_sql = "select * from wp_terms";
            $result = $conn->query($category_sql);
            $category= mysqli_fetch_all($result, MYSQLI_ASSOC);
            arsort($category);
            $name=array_column($category,'name');
            $category_id=[];
            $new_keywords=$keywords;
            foreach($category as $kei=>$vai){
                if(in_array($vai['name'],$keywords)){
                    $category_id[]=$vai['term_id'];
                    $new_keywords=array_diff($new_keywords,[$vai['name']]);
                }
            }
            foreach($new_keywords as $kew=>$vaw){
                $sql = "insert into wp_terms(name,slug,term_group) values('{$vaw}','{$vaw}',0)";
                $result=$conn->query($sql);
                //$result=mysqli_query($conn,$sql);
                if($result){
                    $id=mysqli_insert_id($conn);//获取插入id
                    $sql = "insert into wp_termmeta(term_id,meta_key,meta_value) values({$id},'product_count_product_tag',0)";
                    $result=$conn->query($sql);
                    $sql = "insert into wp_term_taxonomy(term_id,taxonomy,description) values({$id},'product_tag','')";
                    $result=$conn->query($sql);
                    $category_id[]=$id;
                }
            }

            $sql = "select term_taxonomy_id from wp_term_relationships where object_id={$vae['ID']} and term_taxonomy_id <> 2";
            $result = $conn->query($sql);
            $object_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if(empty($object_data)){
                $term_taxonomy_id=[];
            }else{
                $term_taxonomy_id=array_column($category,'term_taxonomy_id');
            }
            foreach($category_id as $ket=>$vat){
                if(!in_array($vat,$term_taxonomy_id)){
		    $insert = "insert into wp_term_relationships(object_id,term_taxonomy_id,term_order) values({$vae['ID']},{$vat},0)";
                    mysqli_query($conn, $insert);
                    $count_id_sql = "update wp_term_taxonomy set count=count+1 where term_id={$vat}";
                    mysqli_query($conn, $count_id_sql);
                    $termmeta_id_sql = "update wp_termmeta set meta_value=meta_value+1 where term_id={$vat}";
                    mysqli_query($conn, $termmeta_id_sql);
                }
            }
        }
	@unlink($file["name"]);
        echo "<script>alert('商品tag标签添加成功ok');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
}else{
    echo "<script>alert('商品tag标签添加失败');</script>";
    echo "<script>history.back();</script>";
    exit;
}
