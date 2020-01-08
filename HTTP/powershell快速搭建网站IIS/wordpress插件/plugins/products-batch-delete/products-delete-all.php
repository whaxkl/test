<?php
global $wpdb;
//获取当前文件所在目录
$path=str_replace("\\","/",dirname(__FILE__));
//获取wordpress所在目录
$root=substr($path,0,-41);
//引用wp-config.php文件，获取数据库信息
require($root."/wp-config.php");
@$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(!$conn){
    echo "<script>alert('Deleted Failure,Databases Connection Failure');</script>";
    echo "<script>history.back();</script>";
    exit;
}
if(!empty($_POST)){
    $delete_all=$_POST['delete_all'];
    $title_all=$_POST['title_all'];
    $category_all=$_POST['category_all'];
    $id_all=$_POST['id_all'];
    $move_title_all=$_POST['move_title_all'];
    $move_category_all=$_POST['move_category_all'];
    $sql="select * from wp_posts where ID>9";
    $result=$conn->query($sql);
    $data=mysqli_fetch_all($result,MYSQLI_ASSOC);
    arsort($data);//$data=array_reverse($data);
    if($delete_all=='delete_all'){
        $str='(';
        foreach($data as $key=>$val){
            if($val['post_type']=='product'){
                $str.=$val['ID'].',';
            }
            if($val['post_type']=='attachment'){
                $str.=$val['ID'].',';
            }
        }
        $str=substr($str,0,-1);
        if($str!=''){
            $str.=')';
            $count_sql="update wp_term_taxonomy set count=0 where taxonomy='product_cat'";
            mysqli_query($conn,$count_sql);
            $del_sql="delete from wp_posts where ID in $str and ID>9";
            mysqli_query($conn,$del_sql);
            $del_relationships_sql="delete from wp_term_relationships where object_id>1";
            mysqli_query($conn,$del_relationships_sql);
        }
        echo "<script>alert('Deleted Successfully');</script>";
    	echo "<script>history.back();</script>";
    	exit;
    }
    if($title_all=='title_all'){
        $title=$_POST['title'];
        if($title==''){
            echo "<script>alert('Title Empty');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
	foreach ($data as $keyb => $valb) {
            if($valb['post_parent']!=0){
                unset($data[$keyb]);
            }
        }
        $str='(';
        $sql="select * from wp_posts where ID>9 and post_title like '%".$title."%' and post_type='product'";
        $result=$conn->query($sql);
        $data_id=mysqli_fetch_all($result,MYSQLI_ASSOC);
	arsort($data_id);//$data_id=array_reverse($data_id);
        $data_id=array_column($data_id,'ID');
        $mark=0;
        foreach($data as $key=>$val){
            if(in_array($val['ID'],$data_id)){
                $str.=$val['ID'].',';
                $mark=1;
                continue;
            }
            if($val['post_type']=='product'){
                $mark=0;
            }
            if($mark==1 && $val['post_type']=='attachment'){
                $str.=$val['ID'].',';
            }
        }
        $str=substr($str,0,-1);
        if($str!=''){
            $str.=')';
            foreach($data_id as $keys=>$values){
                $count_sql="select term_taxonomy_id from wp_term_relationships where object_id={$values} and term_taxonomy_id <> 2";
                $id=mysqli_query($conn,$count_sql);
                $id_data=mysqli_fetch_all($id,MYSQLI_ASSOC);
                foreach($id_data as $k=>$v){
                    $count_id=$v['term_taxonomy_id'];
                    $count_id_sql="update wp_term_taxonomy set count=count-1 where term_taxonomy_id={$count_id}";
                    mysqli_query($conn,$count_id_sql);
                }
            }
	    $id_old=[];
            $str=substr($str,0,-1);
            foreach ($data_id as $keybs => $valbs) {
                $parent_sql = "select * from wp_posts where ID>9 and post_parent={$valbs}";
                $result = $conn->query($parent_sql);
                $parent_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                $parent_data = array_column($parent_data, 'ID');
                $id_old=array_merge($id_old,$parent_data);
            }
            if(!empty($id_old)){
                $id_old_str=implode(',',$id_old);
                $str.=','.$id_old_str.')';
            }
            $del_sql="delete from wp_posts where ID in {$str} and ID>9";
            mysqli_query($conn,$del_sql);
            $del_relationships_sql="delete from wp_term_relationships where object_id in {$str} and object_id>1";
            mysqli_query($conn,$del_relationships_sql);
        }
	echo "<script>alert('Deleted Successfully');</script>";
    	echo "<script>history.back();</script>";
    	exit;
    }
    if($category_all=='category_all'){
        $category=$_POST['category'];
        if($category==''){
            echo "<script>alert('Category Empty');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $category_sql="select term_id from wp_terms where `name` like '%".$category."%' order by term_id desc limit 1";
        $result=$conn->query($category_sql);
        if(!$result){
            echo "<script>alert('Category Not Find');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $category_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
        $term_id=$category_data[0]['term_id'];
        $wp_term_taxonomy_sql="select term_taxonomy_id from wp_term_taxonomy where term_id={$term_id}";
        $result_taxonomy=$conn->query($wp_term_taxonomy_sql);
        $term_taxonomy_data=mysqli_fetch_all($result_taxonomy,MYSQLI_ASSOC);
	arsort($term_taxonomy_data);//$term_taxonomy_data=array_reverse($term_taxonomy_data);
        $term_taxonomy_id=$term_taxonomy_data[0]['term_taxonomy_id'];
        $id_sql="select object_id from wp_term_relationships where term_taxonomy_id={$term_taxonomy_id}";
        $result_relationships=$conn->query($id_sql);
        $term_relationships_data=mysqli_fetch_all($result_relationships,MYSQLI_ASSOC);
	arsort($term_relationships_data);//$term_relationships_data=array_reverse($term_relationships_data);
	foreach ($data as $keyb => $valb) {
            if($valb['post_parent']!=0){
                unset($data[$keyb]);
            }
        }
        $str='(';
        $object_id=array_column($term_relationships_data,'object_id');
        foreach($data as $key=>$val){
            if(in_array($val['ID'],$object_id)){
                $str.=$val['ID'].',';
                $mark=1;
                continue;
            }
            if($val['post_type']=='product'){
                $mark=0;
            }
            if($mark==1 && $val['post_type']=='attachment'){
                $str.=$val['ID'].',';
            }
        }
        $str=substr($str,0,-1);
        if($str!=''){
            $str.=')';
	    $not_category_sql="select term_taxonomy_id from wp_term_relationships where term_taxonomy_id <> {$term_taxonomy_id} and term_taxonomy_id <> 2 and object_id in {$str}";
	    $result=$conn->query($not_category_sql);
            $data_id=mysqli_fetch_all($result,MYSQLI_ASSOC);
            $data_id=array_column($data_id,'term_taxonomy_id');
	    foreach($data_id as $keys=>$values){
		$del_not_category_sql="update wp_term_taxonomy set count=count-1 where term_taxonomy_id={$values}";
		mysqli_query($conn,$del_not_category_sql);
            }
	    $id_old=[];
            $str=substr($str,0,-1);
            foreach ($object_id as $keybs => $valbs) {
                $parent_sql = "select * from wp_posts where ID>9 and post_parent={$valbs}";
                $result = $conn->query($parent_sql);
                $parent_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                $parent_data = array_column($parent_data, 'ID');
                $id_old=array_merge($id_old,$parent_data);
            }
            if(!empty($id_old)){
                $id_old_str=implode(',',$id_old);
                $str.=','.$id_old_str.')';
            }
            $count_sql="update wp_term_taxonomy set count=0 where term_id={$term_id}";
            mysqli_query($conn,$count_sql);
            $del_sql="delete from wp_posts where ID in {$str} and ID>9";
            mysqli_query($conn,$del_sql);
            $del_relationships_sql="delete from wp_term_relationships where object_id in {$str} and object_id>1";
            mysqli_query($conn,$del_relationships_sql);
        }
	echo "<script>alert('Deleted Successfully');</script>";
    	echo "<script>history.back();</script>";
    	exit;
    }
    if($id_all=='id_all'){
        $id_one=$_POST['id_one'];
        $id_two=$_POST['id_two'];
        if($id_one=='' && $id_two==''){
            echo "<script>alert('ID Empty');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        if($id_one!='' && $id_two==''){
            $sql="select * from wp_posts where ID={$id_one} and ID>9 and post_type='product'";
        }else if($id_one=='' && $id_two!=''){
            $sql="select * from wp_posts where ID={$id_two} and ID>9 and post_type='product'";
        }else if($id_one!='' && $id_two!=''){
            if($id_one>$id_two){
                $sql="select * from wp_posts where ID between {$id_two} and {$id_one} and ID>9 and post_type='product'";
            }else if($id_one<$id_two){
                $sql="select * from wp_posts where ID between {$id_one} and {$id_two} and ID>9 and post_type='product'";
            }else{
                $sql="select * from wp_posts where ID={$id_one} and ID>9 and post_type='product'";
            }
        }
        $result=$conn->query($sql);
        $data_id=mysqli_fetch_all($result,MYSQLI_ASSOC);
        $data_id=array_column($data_id,'ID');
	foreach ($data as $keyb => $valb) {
            if($valb['post_parent']!=0){
                unset($data[$keyb]);
            }
        }
        $str='(';
        $mark=0;
        foreach($data as $key=>$val){
            if(in_array($val['ID'],$data_id)){
                $str.=$val['ID'].',';
                $mark=1;
                continue;
            }
            if($val['post_type']=='product'){
                $mark=0;
            }
            if($mark==1 && $val['post_type']=='attachment'){
                $str.=$val['ID'].',';
            }
        }
        $str=substr($str,0,-1);
        if($str!=''){
            $str.=')';
	    foreach($data_id as $keys=>$values){
                $count_sql="select term_taxonomy_id from wp_term_relationships where object_id={$values} and term_taxonomy_id <> 2";
                $id=mysqli_query($conn,$count_sql);
	        $id_data=mysqli_fetch_all($id,MYSQLI_ASSOC);
                foreach($id_data as $k=>$v){
                    $count_id=$v['term_taxonomy_id'];
                    $count_id_sql="update wp_term_taxonomy set count=count-1 where term_taxonomy_id={$count_id}";
                    mysqli_query($conn,$count_id_sql);
                }
            }
	    $id_old=[];
            $str=substr($str,0,-1);
            foreach ($data_id as $keybs => $valbs) {
                $parent_sql = "select * from wp_posts where ID>9 and post_parent={$valbs}";
                $result = $conn->query($parent_sql);
                $parent_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                $parent_data = array_column($parent_data, 'ID');
                $id_old=array_merge($id_old,$parent_data);
            }
            if(!empty($id_old)){
                $id_old_str=implode(',',$id_old);
                $str.=','.$id_old_str.')';
            }
            $del_sql="delete from wp_posts where ID in {$str} and ID>9";
            mysqli_query($conn,$del_sql);
            $del_relationships_sql="delete from wp_term_relationships where object_id in {$str} and object_id>1";
            mysqli_query($conn,$del_relationships_sql);
        }
	echo "<script>alert('Deleted Successfully');</script>";
    	echo "<script>history.back();</script>";
    	exit;
    }
    if($move_title_all=='move_title_all'){
        $move_title=$_POST['move_title'];
        $move_category=$_POST['move_category'];
        if($move_title=='' || $move_category==''){
            echo "<script>alert('Please Enter');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $sql="select * from wp_posts where ID>9 and post_title like '%".$move_title."%' and post_type='product'";
        $result=$conn->query($sql);
        $data_id=mysqli_fetch_all($result,MYSQLI_ASSOC);
        if(empty($data_id)){
            echo "<script>alert('Title Not Find');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        arsort($data_id);//$data_id=array_reverse($data_id);
        $data_id=array_column($data_id,'ID');

        $category_sql="select term_id from wp_terms where `name` like '%".$move_category."%' order by term_id desc limit 1";
        $result=$conn->query($category_sql);
        $category_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
        if(empty($category_data)){
            echo "<script>alert('Category Not Find');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $term_id=$category_data[0]['term_id'];
        $wp_term_taxonomy_sql="select term_taxonomy_id from wp_term_taxonomy where term_id={$term_id}";
        $result_taxonomy=$conn->query($wp_term_taxonomy_sql);
        $term_taxonomy_data=mysqli_fetch_all($result_taxonomy,MYSQLI_ASSOC);
        arsort($term_taxonomy_data);//$term_taxonomy_data=array_reverse($term_taxonomy_data);
        $term_taxonomy_id=$term_taxonomy_data[0]['term_taxonomy_id'];

        $str='(';
        foreach($data_id as $key=>$val){
            $str.=$val.',';
        }
        $str=substr($str,0,-1);
        if($str!=''){
            $str.=')';
            foreach($data_id as $keys=>$values){
                $count_sql="select term_taxonomy_id from wp_term_relationships where object_id={$values} and term_taxonomy_id <> 2";
                $id=mysqli_query($conn,$count_sql);
                $id_data=mysqli_fetch_all($id,MYSQLI_ASSOC);
                $id_data=array_column($id_data,'term_taxonomy_id');
                if(in_array($term_taxonomy_id,$id_data)){
                    continue;
                }
                $insert="insert into wp_term_relationships(object_id,term_taxonomy_id,term_order) values({$values},{$term_taxonomy_id},0)";
                mysqli_query($conn,$insert);
                $count_add_id_sql="update wp_term_taxonomy set count=count+1 where term_taxonomy_id={$term_taxonomy_id}";
                mysqli_query($conn,$count_add_id_sql);
            }
        }
        echo "<script>alert('Move Successfully');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
    if($move_category_all=='move_category_all'){
        $move_category_one=$_POST['move_category_one'];
        $move_category_two=$_POST['move_category_two'];
        if($move_category_one=='' || $move_category_two==''){
            echo "<script>alert('Please Enter');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $move_category_one_sql="select term_id from wp_terms where `name` like '%".$move_category_one."%' order by term_id desc limit 1";
        $result=$conn->query($move_category_one_sql);
        $category_one_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
        if(empty($category_one_data)){
            echo "<script>alert('Category One Not Find');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $move_category_two_sql="select term_id from wp_terms where `name` like '%".$move_category_two."%' order by term_id desc limit 1";
        $result=$conn->query($move_category_two_sql);
        $category_two_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
        if(empty($category_two_data)){
            echo "<script>alert('Category Two Not Find');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $category_two_id=$category_two_data[0]['term_id'];
        $category_one_id=$category_one_data[0]['term_id'];
        if($category_one_id==$category_two_id){
            echo "<script>alert('Move Successfully');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $wp_term_taxonomy_sql="select * from wp_term_taxonomy where term_id={$category_one_id}";
        $result_taxonomy=$conn->query($wp_term_taxonomy_sql);
        $term_taxonomy_data=mysqli_fetch_all($result_taxonomy,MYSQLI_ASSOC);
        $term_taxonomy_id=$term_taxonomy_data[0]['term_taxonomy_id'];
        $term_taxonomy_count=$term_taxonomy_data[0]['count'];
        $not_count_sql="select * from wp_term_relationships where term_taxonomy_id={$term_taxonomy_id} and term_taxonomy_id <> 2 and object_id>1";
        $result=$conn->query($not_count_sql);
        $not_count_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
        $str='(';
        foreach($not_count_data as $key=>$val){
            $str.=$val['object_id'].',';
        }
        $str=substr($str,0,-1);
        if($str!='') {
            $str .= ')';
            $del_not_count_sql="select * from wp_term_relationships where object_id in {$str} and term_taxonomy_id={$category_two_id} and term_taxonomy_id <> 2 and object_id>1";
            $result=$conn->query($del_not_count_sql);
            $del_not_count_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
            $del_count=count($del_not_count_data);
            $term_taxonomy_count=$term_taxonomy_count-$del_count;
            $move_category_one_del_sql="update wp_term_taxonomy set count=0 where term_taxonomy_id={$term_taxonomy_id}";
            mysqli_query($conn,$move_category_one_del_sql);
            $move_category_two_add_sql="update wp_term_taxonomy set count=count+{$term_taxonomy_count} where term_taxonomy_id={$category_two_id}";
            mysqli_query($conn,$move_category_two_add_sql);
            $del_sql="select * from wp_term_relationships where object_id in {$str} and term_taxonomy_id <> {$category_two_id} and term_taxonomy_id <> 2 and object_id>1";
            $result=$conn->query($del_sql);
            $update_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
            foreach($del_not_count_data as $ke=>$va){
                $object_id=$va['object_id'];
                $id=$va['term_taxonomy_id'];
                $update="delete from wp_term_relationships where term_taxonomy_id={$id} and object_id={$object_id} and term_taxonomy_id <> 2";
                mysqli_query($conn,$update);
            }
            foreach($update_data as $kee=>$vaa){
                $object_id=$vaa['object_id'];
                $id=$vaa['term_taxonomy_id'];
                $update="update wp_term_relationships set term_taxonomy_id={$category_two_id} where term_taxonomy_id={$id} and object_id={$object_id} and term_taxonomy_id <> 2";
                mysqli_query($conn,$update);
            }
        }
        echo "<script>alert('Move Successfully');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
}else{
    echo "<script>alert('Deleted Failure');</script>";
    echo "<script>history.back();</script>";
    exit;
}
