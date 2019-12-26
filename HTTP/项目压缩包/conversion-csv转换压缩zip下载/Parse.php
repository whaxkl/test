<?php

class Parse
{

    public function deal($files,$keyword,$desc_num,$tag_num,$num = 0)
    {
        $data = $this->read_csv($files);
        $keywords = $this->keyword_csv($keyword);
        $array = [];
        foreach ($data as $k => $v) {
            if(isset($array[$v['Handle']]))
            {
                $array[$v['Handle']]['Image Src'] = $array[$v['Handle']]['Image Src'].','.$v['Image Src'];
            }else{
                if(empty($array)){
                    $array[$v['Handle']] = $v;
                }else{
                    $make = 1;
                    foreach ($array as $keyd=>$valued)
                    {
                        if(stripos($keyd,substr($v['Handle'],0,-3))!==false){
                            $make = 0;
                            break;
                        }
                    }
                    if($make == 1){
                        $array[$v['Handle']] = $v;
                    }
                }
            }
        }
        $new_data = array(array());
        foreach ($array as $k =>$v)
        {
            $v['Body (HTML)'] = strip_tags($v['Body (HTML)']);
            $v['Published'] = 'true' ? 1 : '';
            $key = array_rand($keywords);
            $str = ',';
            //Title插入关键词
            if($num != 0)
            {
                if($num == 2) {
                    if(strpos($v['Title'],$str) !== false)
                    {
                        $Title = $this->start($v['Title'],$keywords[$key]);
                    }else{
                        $Title = $v['Title'];
                    }
                } elseif($num == 3) {
                    $Title = $v['Title'].','.$keywords[$key];
                }elseif($num == 1) {
                    $Title = $keywords[$key].','.$v['Title'];
                }
            }else{
                $Title = $v['Title'];
            }

            if(!empty($v))
            {
                $new_data[$k]['Type'] = 'simple';
                $new_data[$k]['SKU'] = '';
                $new_data[$k]['Name'] = $Title;
                $new_data[$k]['Published'] = $v['Published'];
                $new_data[$k]['Is featured?'] = 0;
                $new_data[$k]['Visibility in catalog'] = 'visible';
                $new_data[$k]['Short description'] = '';
                //判断关键词个数
                if($desc_num != '')
                {
                    if($desc_num > 1 && strlen($v['Body (HTML)']) > 30)
                    {
                        $description = array_rand($keywords,$desc_num);
                        foreach ($description as $vo)
                        {
                            $desc[] = $keywords[$vo];
                        }
                        //子Body (HTML)组加入随机关键字
                        $content = $this->insert($v['Body (HTML)'],$desc);
                        unset($desc);
                    }elseif($desc_num == 1 || strlen($v['Body (HTML)']) <= 30){
                        $description = array_rand($keywords,$desc_num);
                        if(count($description) > 1){
                            $rand = rand(0,count($description)-1);
                            $desc[] = $keywords[$description[$rand]];
                        }else {
                            $desc[] = $keywords[$description];
                        }
                        //子Body (HTML)组加入随机关键字
                        $content = $this->inserts($v['Body (HTML)'],$desc);
                        unset($desc);
                    }
                } else{
                    $content = $v['Body (HTML)'];
                }
                $new_data[$k]['Description'] = $content;
                $new_data[$k]['Date sale price starts'] = '';
                $new_data[$k]['Date sale price ends'] = '';
                $new_data[$k]['Tax status'] = 'taxable';
                $new_data[$k]['Tax class'] = '';
                $new_data[$k]['In stock?'] = 1;
                $new_data[$k]['Stock'] = '';
                $new_data[$k]['Low stock amount'] = '';
                $new_data[$k]['Backorders allowed?'] = 0;
                $new_data[$k]['Sold individually?'] = 0;
                $new_data[$k]['Weight (g)'] = $v['Variant Grams'];
                $new_data[$k]['Length (cm)'] = '';
                $new_data[$k]['Width (cm)'] = '';
                $new_data[$k]['Height (cm)'] = '';
                $new_data[$k]['Allow customer reviews?'] = 1;
                $new_data[$k]['Purchase note'] = '';
                $new_data[$k]['Sale price'] = '';
                $new_data[$k]['Regular price'] = $v['Variant Price'];
                $new_data[$k]['Categories'] = 'shopify';
                $tags = array_rand($keywords,$tag_num);
                if($tag_num > 1)
                {
                    if(count($tags)>1)
                    {
                        foreach ($tags as $key=>$vo)
                        {
                            $tags[$key] = $keywords[$vo];
                        }
                    }
                    $Tags = implode(',',$tags);
                }else{
                    $Tags = $keywords[$tags];
                }
                $new_data[$k]['Tags'] = $Tags;
                $new_data[$k]['Shipping class'] = '';
                $new_data[$k]['Images'] = $v['Image Src'];
                $new_data[$k]['Download limit'] = '';
                $new_data[$k]['Download expiry days'] = '';
                $new_data[$k]['Parent'] = '';
                $new_data[$k]['Grouped products'] = '';
                $new_data[$k]['Upsells'] = '';
                $new_data[$k]['Cross-sells'] = '';
                $new_data[$k]['External URL'] = '';
                $new_data[$k]['Button text'] = '';
                $new_data[$k]['Position'] = 0;
            }
        }
        return array_values($this->array_remove_by_key($new_data,0));
    }

    public function import($param,$numbers,$filenames)
    {
        $columns = [
            'Type', 'SKU', 'Name', 'Published','Is featured?','Visibility in catalog',
            'Short description', 'Description', 'Date sale price starts', 'Date sale price ends',
            'Tax status', 'Tax class', 'In stock?', 'Stock', 'Low stock amount', 'Backorders allowed?',
            'Sold individually?','Weight (g)','Length (cm)', 'Width (cm)', 'Height (cm)',
            'Allow customer reviews?', 'Purchase note', 'Sale price', 'Regular price', 'Categories',
            'Tags', 'Shipping class', 'Images', 'Download limit', 'Download expiry days',
            'Parent', 'Grouped products', 'Upsells', 'Cross-sells',	'External URL',
            'Button text', 'Position'
        ];
        // 下面以分页形式导出
        // 总记录数
        $countUser = count($param);
        // 总页数
        $pages = ceil($countUser / $numbers);
        $fileArr = [];
        for ($i = 0; $i < $pages; $i++) {
            if ($i == 0) {
                // 打开一个临时文件
                $filename = dirname(__DIR__) . '/' . $filenames. '-'. 'processed'. '-' . $i . '.csv';
                $fileArr[] = $filename;
                // 打开一个文件句柄
                $fp = fopen($filename, 'w');
                // 把变量从UTF-8转成GBK编码
                mb_convert_variables('GBK', 'UTF-8', $columns);
                fputcsv($fp, $columns);

            }else{
                fclose($fp);
                // 重新打开一个新文件
                // 打开一个临时文件
                $filename = dirname(__DIR__) . '/' . $filenames. '-'. 'processed'. '-' . $i . '.csv';
                $fileArr[] = $filename;
                // 打开一个文件句柄
                $fp = fopen($filename, 'w');
                // 把变量从UTF-8转成GBK编码
                mb_convert_variables('GBK', 'UTF-8', $columns);
                fputcsv($fp, $columns);
            }
            $imports = array_slice($param,$i*$numbers,$numbers);
            foreach ($imports as $user) {
                mb_convert_variables('GBK', 'UTF-8', $user);
                fputcsv($fp, $user);
            }
            if ($i == ($pages - 1)) {
                // 如果是最后一页，执行完就关闭文件
                fclose($fp);
            }
        }
        // 压缩打包
        $zip = new \ZipArchive();
        $zipName = $filenames. '-'. 'processed'. '-' .$pages . '.zip';
        $a=$zip->open($zipName, \ZipArchive::CREATE);
	//var_dump($a);die;
        foreach ($fileArr as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        foreach ($fileArr as $file) {
            unlink($file);
        }
	header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=' . basename($zipName));
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: ' . filesize($zipName));
        @readfile($zipName);
        @unlink($zipName);
        /*header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length:" . filesize( $zipName));
        header("Content-Disposition: attachment; filename=" . basename($zipName));
        @readfile($zipName);//下载到本地
        @unlink($zipName);//删除服务器上生成的这个压缩文件*/
	/*$fp = fopen($zipName, "r+") or die('打开文件错误');   //下载文件必须要将文件先打开。写入内存
        $file_size = filesize($zipName);
        //返回的文件流
        Header("Content-type:application/octet-stream");
        //按照字节格式返回
        Header("Accept-Ranges:bytes");
        //返回文件大小
        Header("Accept-Length:" . $file_size);
        //弹出客户端对话框，对应的文件名
        Header("Content-Disposition:attachment;filename=" . basename($zipName));
        //防止服务器瞬间压力增大，分段读取
        $buffer = 1024;
        while (!feof($fp)) {
            $file_data = fread($fp, $buffer);
            echo $file_data;
        }
        fclose($fp);
        //@unlink($zipName);
        exit;*/

    }


    /**
     * @param $arr
     * @param $key
     * @return mixed
     * 删除二维数组指定key
     */
    private function array_remove_by_key($arr, $key){
        if(!array_key_exists($key, $arr)){
            return $arr;
        }
        $keys = array_keys($arr);
        $index = array_search($key, $keys);
        if($index !== FALSE){
            array_splice($arr, $index, 1);
        }
        return $arr;
    }

    /**
     * @param $file
     * @return array
     * 读取csv文件并取出数据
     */
    function read_csv($file)
    {
        setlocale(LC_ALL, 'zh_CN');//linux系统下生效
        $data = null;//返回的文件数据行
        if (!is_file($file) && !file_exists($file)) {
            die('文件错误');
        }
        $cvs_file = fopen($file, 'r'); //开始读取csv文件数据
        $i = 0;//记录cvs的行
        while ($file_data = fgetcsv($cvs_file)) {
            $i++;
            if ($i == 1) {
                $title = $file_data;
                continue;
            }
            if ($file_data[0] != '') {
                $data[$i] = $file_data;
            }
	    if(count($title)==count($data[$i])){
                $new_data[] = array_combine($title,$data[$i]);
            }
        }
        //数组中的key替换为表头
        //array_combine 替换数组的key
        /*foreach ($data as $key => $v)
        {
	    //var_dump($title,"<h1>asdasf</h1>",$v);die;
            $new_data[] = array_combine($title,$v);
	    //var_dump($new_data);die;
	    //if(count($title)!=count($v)){
                //var_dump($title,"<h1>asdasf</h1>",$v,$i);die;
            //}
        }*/
        fclose($cvs_file);
        return $new_data;
    }

    function keyword_csv($file)
    {
        $data = null;//返回的文件数据行
        $cvs_file = fopen($file, 'r'); //开始读取csv文件数据
        $i = 0;//记录cvs的行
        while ($file_data = fgetcsv($cvs_file)) {
            $i++;
            if ($file_data[0] != '') {
                $data[] = $file_data[0];
            }
        }
        fclose($cvs_file);
        return $data;
    }

    function start($str,$keys){

        $str_arr = explode(",",$str);
        for($i=0; $i<count($str_arr); $i++){
            $str_arr2[] = $str_arr[$i];
        }
        $count = count($str_arr2);
        if($count > 1)
        {
            $rand = rand(0,$count-1);
            $str_arr2[$rand] = $keys.','.$str_arr2[$rand];
        }
        return implode(",",$str_arr2);
    }

    //为随机子Body (HTML)组值加入随机多个关键字
    function insert($str,$keys){
        //字符串转数组
        $str_arr = explode(",",$str);
        for($i=0; $i<count($str_arr); $i++){
            $str_arr2[] = $str_arr[$i];
        }
        $count = count($str_arr2);
        $keys_count = count($keys);
        if($count > 1 && $count >= $keys_count)
        {
            //随机子Body (HTML)加入随机关键字
            foreach($keys as $v)
            {
                $rand = rand(0,$count-1);
                $str_arr2[$rand] = $v.','.$str_arr2[$rand];
            }
        }
        else
        {
            $rand = rand(0,$count-1);
            $str_arr2[$rand] = implode(',',$keys).','.$str_arr2[$rand];
        }
        return implode(",",$str_arr2);
    }

    //为随机子Body (HTML)组值随机加入一个关键字
    function inserts($str,$keys){

        $str_arr = explode(",",$str);
        for($i=0; $i<count($str_arr); $i++){
            $str_arr2[] = $str_arr[$i];
        }
        $count = count($str_arr2);
        $rand = rand(0,$count-1);
        $str_arr2[$rand] = implode(',',$keys).','.$str_arr2[$rand];
        return implode(",",$str_arr2);
    }

}