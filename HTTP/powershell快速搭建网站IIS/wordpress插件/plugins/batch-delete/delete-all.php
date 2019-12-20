<?php
global $wpdb;
//获取当前文件所在目录
define("__S__",str_replace("\\","/",dirname(__FILE__)));
//echo __FILE__;echo __S__;
//获取wordpress所在目录
define("__ROOT__",substr(__S__,0,-32));
//引用wp-config.php文件，获取数据库信息
require(__ROOT__."/wp-config.php");
@$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(!$conn){
    echo "<script>alert('操作失败');</script>";
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
    $data=array_reverse($data);
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
            $del_sql="delete from wp_posts where ID in $str and ID>9";
            mysqli_query($conn,$del_sql);
            $count_sql="update wp_term_taxonomy set count=0 where taxonomy='product_cat'";
            mysqli_query($conn,$count_sql);
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
        $str='(';
        $sql="select * from wp_posts where ID>9 and post_title like '%".$title."%' and post_type='product'";
        $result=$conn->query($sql);
        $data_id=mysqli_fetch_all($result,MYSQLI_ASSOC);
        $data_id=array_reverse($data_id);
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
            $del_sql="delete from wp_posts where ID in {$str} and ID>9";
            mysqli_query($conn,$del_sql);
            foreach($data_id as $keys=>$values){
                $count_sql="select term_taxonomy_id from wp_term_relationships where object_id={$values} and term_taxonomy_id <> 2";
                $id=mysqli_query($conn,$count_sql);
                $id_data=mysqli_fetch_all($id,MYSQLI_ASSOC);
                $count_id=$id_data[0]['term_taxonomy_id'];
                $count_id_sql="update wp_term_taxonomy set count=count-1 where term_taxonomy_id={$count_id}";
                mysqli_query($conn,$count_id_sql);
            }
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
        $category_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
        $term_id=$category_data[0]['term_id'];
        $wp_term_taxonomy_sql="select term_taxonomy_id from wp_term_taxonomy where term_id={$term_id}";
        $result_taxonomy=$conn->query($wp_term_taxonomy_sql);
        $term_taxonomy_data=mysqli_fetch_all($result_taxonomy,MYSQLI_ASSOC);
        $term_taxonomy_data=array_reverse($term_taxonomy_data);
        $term_taxonomy_id=$term_taxonomy_data[0]['term_taxonomy_id'];
        $id_sql="select object_id from wp_term_relationships where term_taxonomy_id={$term_taxonomy_id}";
        $result_relationships=$conn->query($id_sql);
        $term_relationships_data=mysqli_fetch_all($result_relationships,MYSQLI_ASSOC);
        $term_relationships_data=array_reverse($term_relationships_data);
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
            $del_sql="delete from wp_posts where ID in {$str} and ID>9";
            mysqli_query($conn,$del_sql);
            $count_sql="update wp_term_taxonomy set count=0 where term_id={$term_id}";
            mysqli_query($conn,$count_sql);
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
            $del_sql="delete from wp_posts where ID in {$str} and ID>9";
            mysqli_query($conn,$del_sql);
            foreach($data_id as $keys=>$values){
                $count_sql="select term_taxonomy_id from wp_term_relationships where object_id={$values} and term_taxonomy_id <> 2";
                $id=mysqli_query($conn,$count_sql);
                $id_data=mysqli_fetch_all($id,MYSQLI_ASSOC);
                $count_id=$id_data[0]['term_taxonomy_id'];
                $count_id_sql="update wp_term_taxonomy set count=count-1 where term_taxonomy_id={$count_id}";
                mysqli_query($conn,$count_id_sql);
            }
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
        $data_id=array_reverse($data_id);
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
        $term_taxonomy_data=array_reverse($term_taxonomy_data);
        $term_taxonomy_id=$term_taxonomy_data[0]['term_taxonomy_id'];

        $str='(';
        foreach($data_id as $key=>$val){
            $str.=$val.',';
        }
        /*foreach($data_id as $keys=>$values){
                    $count_sql="select term_taxonomy_id from wp_term_relationships where object_id={$values} and term_taxonomy_id <> 2";
                    $id=mysqli_query($conn,$count_sql);
                $id_data=mysqli_fetch_all($id,MYSQLI_ASSOC);
                    $count_id=$id_data[0]['term_taxonomy_id'];
                    $count_del_id_sql="update wp_term_taxonomy set count=count-1 where term_taxonomy_id=$count_id";
            $count_add_id_sql="update wp_term_taxonomy set count=count+1 where term_taxonomy_id=$term_taxonomy_id";
            echo $count_del_id_sql;echo $count_add_id_sql;die;
                    mysqli_query($conn,$count_del_id_sql);
            mysqli_query($conn,$count_add_id_sql);
                }*/
        $str=substr($str,0,-1);
        if($str!=''){
            $str.=')';
            foreach($data_id as $keys=>$values){
                $count_sql="select term_taxonomy_id from wp_term_relationships where object_id={$values} and term_taxonomy_id <> 2";
                $id=mysqli_query($conn,$count_sql);
                $id_data=mysqli_fetch_all($id,MYSQLI_ASSOC);
                $count_id=$id_data[0]['term_taxonomy_id'];
                $count_del_id_sql="update wp_term_taxonomy set count=count-1 where term_taxonomy_id=$count_id";
                mysqli_query($conn,$count_del_id_sql);
                $count_add_id_sql="update wp_term_taxonomy set count=count+1 where term_taxonomy_id=$term_taxonomy_id";
                mysqli_query($conn,$count_add_id_sql);
            }
            $move_sql="update wp_term_relationships set term_taxonomy_id={$term_taxonomy_id} where object_id in $str and term_taxonomy_id <> 2 and object_id>1";
            mysqli_query($conn,$move_sql);
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
        $category_one_id=$category_one_data[0]['term_id'];
        $wp_term_taxonomy_sql="select * from wp_term_taxonomy where term_id={$category_one_id}";
        $result_taxonomy=$conn->query($wp_term_taxonomy_sql);
        $term_taxonomy_data=mysqli_fetch_all($result_taxonomy,MYSQLI_ASSOC);
        $term_taxonomy_id=$term_taxonomy_data[0]['term_taxonomy_id'];
        $term_taxonomy_count=$term_taxonomy_data[0]['count'];
        $move_category_two_sql="select term_id from wp_terms where `name` like '%".$move_category_two."%' order by term_id desc limit 1";
        $result=$conn->query($move_category_two_sql);
        $category_two_data=mysqli_fetch_all($result,MYSQLI_ASSOC);
        if(empty($category_two_data)){
            echo "<script>alert('Category Two Not Find');</script>";
            echo "<script>history.back();</script>";
            exit;
        }
        $category_two_id=$category_two_data[0]['term_id'];
        $move_category_one_del_sql="update wp_term_taxonomy set count=count-{$term_taxonomy_count} where term_taxonomy_id={$term_taxonomy_id}";
        mysqli_query($conn,$move_category_one_del_sql);
        $move_category_two_add_sql="update wp_term_taxonomy set count=count+{$term_taxonomy_count} where term_taxonomy_id={$category_two_id}";
        mysqli_query($conn,$move_category_two_add_sql);
        $move_category_sql="update wp_term_relationships set term_taxonomy_id={$category_two_id} where term_taxonomy_id={$term_taxonomy_id}";
        mysqli_query($conn,$move_category_sql);
        echo "<script>alert('Move Successfully');</script>";
        echo "<script>history.back();</script>";
        exit;
    }
}else{
    echo "<script>alert('Deleted Failure');</script>";
    echo "<script>history.back();</script>";
    exit;
}
