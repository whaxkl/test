<?php
global $wpdb;
//获取当前文件所在目录
$path=str_replace("\\","/",dirname(__FILE__));
//echo __FILE__;echo __SL__;
//获取wordpress所在目录
$root=substr($path,0,-37);
//引用wp-config.php文件，获取数据库信息
require($root."/wp-config.php");
@$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(!$conn){
    echo "<script>alert('Products to Heavy Failure');</script>";
    echo "<script>history.back();</script>";
    exit;
}
if(!empty($_POST)) {
    $products_to_heavy_all = $_POST['products_to_heavy_all'];
    if ($products_to_heavy_all == 'products_to_heavy_all') {
        $array = [];
        $array_one = [];
        $sql = "select * from wp_posts where ID>9 and post_type='product'";
        $result = $conn->query($sql);
        $data_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $data = array_reverse($data_data);

        $sql = "select * from wp_posts where ID>9";
        $result = $conn->query($sql);
        $data_all = mysqli_fetch_all($result, MYSQLI_ASSOC);
        arsort($data_all);//array_reverse($data_all);
        $data_all_id=array_column($data,'ID');
        $num=0;
        $count_num=0;
        $suspicious_id=[];
        foreach ($data_all as $key => $val) {
            if ($val['post_type']!='product' && $val['post_mime_type']=='image/jpeg') {
                $count_num++;
            }
            if (in_array($val['ID'],$data_all_id)) {
                if($count_num-$num==0 && $num>0){
		    //var_dump($data_all[$key],$data_all[$id],$id);die;
                    $suspicious_id[]=$data_all[$id];
                    $num=$count_num;
                }else{
                    $num=1;
                    $count_num=1;
                }
            }
	    if ($val['post_type']=='product') {
                $id=$key;
            }
        }
	$len=count($suspicious_id);
	//var_dump($suspicious_id,'<h1>asdfasdfasf</h1>');
	//if($len>0){
	    //unset($suspicious_id[$len-1]);
	//}
	$array_keys=array_column($suspicious_id,'ID');
        foreach ($data_data as $k => $v) {
            $id = $v['ID'];
            $sql = "SELECT * FROM `wp_wc_product_meta_lookup` where product_id={$id}";
            $result = $conn->query($sql);
            $product_price_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $min_price = $product_price_data[0]['min_price'];
            $max_price = $product_price_data[0]['max_price'];
            if (empty($array)) {
                $array[$id] = ['post_title' => $v['post_title'], 'min_price' => $min_price, 'max_price' => $max_price];
            } else {//34
                $make = 1;
                foreach ($array as $keyd => $valued) {
                    if (stripos($valued['post_title'], substr($v['post_title'], 0, -3)) !== false
                        && strlen($valued['post_title']) - strlen(substr($v['post_title'], 0, -3)) < 4
                        && $min_price == $valued['min_price'] && $max_price == $valued['max_price'] || in_array($id,$array_keys)) {
                        $make = 0;
                        break;
                    }
                }
                if ($make == 1) {
                    $array[$id]=['post_title' => $v['post_title'], 'min_price' => $min_price, 'max_price' => $max_price];
                } else {
                    $array_one[$id]=['post_title' => $v['post_title'], 'min_price' => $min_price, 'max_price' => $max_price];
                }
            }
        }//var_dump($array_keys,"<h1>sasfasf</h1>",$array,"<h1>sasfasf</h1>",$array_one);die;//&& in_array($keyd,$array_keys)
        if (empty($array_one)) {
            echo "<script>alert('Products To Heavy Successfully');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $array_one = array_keys($array_one);
        $str = implode(',', $array_one);
        $str = '(' . $str . ')';
        $str_all = '(';
        $mark = 0;
        foreach ($data_all as $key => $val) {
            if (in_array($val['ID'], $array_one)) {
                $str_all .= $val['ID'] . ',';
                $mark = 1;
                continue;
            }
            if ($val['post_type'] == 'product') {
                $mark = 0;
            }
            if ($mark == 1 && $val['post_type'] == 'attachment') {
                $str_all .= $val['ID'] . ',';
            }
        }
        $str_all = substr($str_all, 0, -1);
        if ($str_all != '') {
            $str_all .= ')';
            foreach ($array_one as $keys => $values) {
                $count_sql = "select term_taxonomy_id from wp_term_relationships where object_id={$values} and term_taxonomy_id <> 2";
                $id = mysqli_query($conn, $count_sql);
                $id_data = mysqli_fetch_all($id, MYSQLI_ASSOC);
                foreach ($id_data as $ke => $ve) {
                    $count_id = $ve['term_taxonomy_id'];
                    $count_id_sql = "update wp_term_taxonomy set count=count-1 where term_taxonomy_id={$count_id}";
                    mysqli_query($conn, $count_id_sql);
                }
            }
            $del_sql = "delete from wp_posts where ID in {$str_all} and ID>9";
            mysqli_query($conn, $del_sql);
            $del_relationships_sql = "delete from wp_term_relationships where object_id in {$str} and object_id>1";
            mysqli_query($conn, $del_relationships_sql);
        }
        echo "<script>alert('Products To Heavy Successfully');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
}else{
    echo "<script>alert('Products to Heavy Failure');</script>";
    echo "<script>history.back();</script>";
    exit;
}
