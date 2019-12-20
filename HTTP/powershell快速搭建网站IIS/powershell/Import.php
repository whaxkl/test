<?php
    require 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Reader;
    use PhpOffice\PhpSpreadsheet\Worksheet;
    use PhpOffice\PhpSpreadsheet\IOFactory;

class Import
{
    public function imports($file)
    {



        $file_size = $file['size'];

        //限制上传表格类型
        $fileExtendName = substr(strrchr($file["name"], '.'), 1);
        //application/vnd.ms-excel  为xls文件类型
        if ($fileExtendName != 'csv') {
            echo '<script>alert("必须为excel表格，且必须为xlsx格式！");</script>';
            echo '<script>history.go(-1);</script>';
            exit();
        }

        if (is_uploaded_file($file['tmp_name'])) {
            // 有Xls和Xlsx格式两种
            $objReader = IOFactory::createReader('Csv');

            $filename = $file['tmp_name'];
            $objPHPExcel = $objReader->load($filename);  //$filename可以是上传的表格，或者是指定的表格
            $spreadsheet = new Spreadsheet();
            $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
            $highestRow = $sheet->getHighestRow();       // 取得总行数
            $highestColumn = $sheet->getHighestColumn();   // 取得总列数
            //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。

            for ($j = 2; $j <= $highestRow; $j++)
            {
                $data[$j - 2] = [
                    'ID' => $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue(),
                    'Type' => 'simple',
                    'SKU' => '',
                    'Name' => $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue(),
                    'Published' => $objPHPExcel->getActiveSheet()->getCell("H" . $j)->getValue(),
                    'Is featured?' => 0,
                    'Visibility in catalog' => 'visible',
                    'Short description' => '',
                    'Description' => $objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue(),
                    'Date sale price starts' => '',
                    'Date sale price ends' => '',
                    'Tax status' => 'taxable',
                    'Tax class' => '',
                    'In stock?' => 1,
                    'Stock' => '',
                    'Low stock amount' => '',
                    'Backorders allowed?' => 0,
                    'Sold individually?' => 0,
                    'Weight (g)' => $objPHPExcel->getActiveSheet()->getCell("P" . $j)->getValue(),
                    'Length (cm)' => '',
                    'Width (cm)' => '',
                    'Height (cm)' => '',
                    'Allow customer reviews?' => 1,
                    'Purchase note' => '',
                    'Sale price' => '',
                    'Regular price' => $objPHPExcel->getActiveSheet()->getCell("U" . $j)->getValue(),
                    'Categories' => 'shopify',
                    'Tags' => $objPHPExcel->getActiveSheet()->getCell("G" . $j)->getValue(),
                    'Shipping class' => '',
                    'Images' => $objPHPExcel->getActiveSheet()->getCell("Z" . $j)->getValue(),
                    'Download limit' => '',
                    'Download expiry days' => '',
                    'Parent' => '',
                    'Grouped products' => '',
                    'Upsells' => '',
                    'Cross-sells' => '',
                    'External URL' => '',
                    'Button text' => '',
                    'Position' => 0,
                ];
            }
        }
    }

    function excelBrowserExport($fileName, $fileType)
    {

        //文件名称校验
        if (!$fileName) {
            trigger_error('文件名不能为空', E_USER_ERROR);
        }

        //Excel文件类型校验
        $type = ['Excel2007', 'Xlsx', 'Excel5', 'xls'];
        if (!in_array($fileType, $type)) {
            trigger_error('未知文件类型', E_USER_ERROR);
        }

        if ($fileType == 'Excel2007' || $fileType == 'Xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
            header('Cache-Control: max-age=0');
        } else {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
            header('Cache-Control: max-age=0');
        }
    }
}