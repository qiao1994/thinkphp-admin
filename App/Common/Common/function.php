<?php
//116旅游网定制功能
//发送通知
function notice($send_user_id = 1, $receive_user_id, $title, $content) {
	$model = D('Notice');
	unset($data);
	$data['send_user_id'] = $send_user_id;
	$data['receive_user_id'] = $receive_user_id;
	$data['title'] = $title;
	$data['content'] = $content;
	$model->create($data);
	$model->add();
}


/**
+----------------------------------------------------------
 * Export Excel | 2013.08.23
 * Author:HongPing <hongping626@qq.com>
+----------------------------------------------------------
 * @param $expTitle     string File name
+----------------------------------------------------------
 * @param $expCellName  array  Column name
+----------------------------------------------------------
 * @param $expTableData array  Table data
+----------------------------------------------------------
 */
function exportExcel($expTitle, $expCellName, $expTableData) {
    ob_end_clean();
	$xlsTitle = iconv('utf-8', 'gb2312', $expTitle); //文件名称
	$fileName = $_SESSION['loginAccount'] . date('_YmdHis'); //or $xlsTitle 文件名称可根据自己情况设定
	$cellNum = count($expCellName);
	$dataNum = count($expTableData);
	vendor("PHPExcel.PHPExcel");
	$objPHPExcel = new PHPExcel();
	$cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

	$objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1'); //合并单元格
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle . '  导出时间:' . date('Y-m-d H:i:s'));
	for ($i = 0; $i < $cellNum; $i++) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
	}
	// Miscellaneous glyphs, UTF-8
	for ($i = 0; $i < $dataNum; $i++) {
		for ($j = 0; $j < $cellNum; $j++) {
			$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]]);
		}
	}

	header('pragma:public');
	header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
	header("Content-Disposition:attachment;filename=$fileName.xls"); //attachment新窗口打印inline本窗口打印
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}

/**
+----------------------------------------------------------
 * Import Excel | 2013.08.23
 * Author:HongPing <hongping626@qq.com>
+----------------------------------------------------------
 * @param  $file   upload file $_FILES
+----------------------------------------------------------
 * @return array   array("error","message")
+----------------------------------------------------------
 */
function importExecl($file) {
	if (!file_exists($file)) {
		return array("error" => 0, 'message' => 'file not found!');
	}
	Vendor("PHPExcel.PHPExcel.IOFactory");
	$objReader = PHPExcel_IOFactory::createReader('Excel5');
	try {
		$PHPReader = $objReader->load($file);
	} catch (Exception $e) {}
	if (!isset($PHPReader)) {
		return array("error" => 0, 'message' => 'read error!');
	}

	$allWorksheets = $PHPReader->getAllSheets();
	$i = 0;
	foreach ($allWorksheets as $objWorksheet) {
		$sheetname = $objWorksheet->getTitle();
		$allRow = $objWorksheet->getHighestRow(); //how many rows
		$highestColumn = $objWorksheet->getHighestColumn(); //how many columns
		$allColumn = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$array[$i]["Title"] = $sheetname;
		$array[$i]["Cols"] = $allColumn;
		$array[$i]["Rows"] = $allRow;
		$arr = array();
		$isMergeCell = array();
		foreach ($objWorksheet->getMergeCells() as $cells) {
			foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
				$isMergeCell[$cellReference] = true;
			}
		}
		for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
			$row = array();
			for ($currentColumn = 0; $currentColumn < $allColumn; $currentColumn++) {
				;
				$cell = $objWorksheet->getCellByColumnAndRow($currentColumn, $currentRow);
				$afCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn + 1);
				$bfCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn - 1);
				$col = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
				$address = $col . $currentRow;
				$value = $objWorksheet->getCell($address)->getValue();
				if (substr($value, 0, 1) == '=') {
					return array("error" => 0, 'message' => 'can not use the formula!');
					exit;
				}
				if ($cell->getDataType() == PHPExcel_Cell_DataType::TYPE_NUMERIC) {
					//$cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();
					$cellstyleformat = $cell->getStyle($cell->getCoordinate())->getNumberFormat();
					$formatcode = $cellstyleformat->getFormatCode();
					if (preg_match('/^([$[A-Z]*-[0-9A-F]*])*[hmsdy]/i', $formatcode)) {
						$value = gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));
					} else {
						$value = PHPExcel_Style_NumberFormat::toFormattedString($value, $formatcode);
					}
				}
				if ($isMergeCell[$col . $currentRow] && $isMergeCell[$afCol . $currentRow] && !empty($value)) {
					$temp = $value;
				} elseif ($isMergeCell[$col . $currentRow] && $isMergeCell[$col . ($currentRow - 1)] && empty($value)) {
					$value = $arr[$currentRow - 1][$currentColumn];
				} elseif ($isMergeCell[$col . $currentRow] && $isMergeCell[$bfCol . $currentRow] && empty($value)) {
					$value = $temp;
				}
				$row[$currentColumn] = $value;
			}
			$arr[$currentRow] = $row;
		}
		$array[$i]["Content"] = $arr;
		$i++;
	}
	// spl_autoload_register(array('Think','autoload'));//must, resolve ThinkPHP and PHPExcel conflicts
	unset($objWorksheet);
	unset($PHPReader);
	unset($PHPExcel);
	unlink($file);
	return array("error" => 1, "data" => $array);
}

/**
 * 邮件发送函数
 */
function sendMail($to, $title, $content) {
	Vendor('PHPMailer.PHPMailerAutoload');
	$mail = new PHPMailer(); //实例化
	$mail->IsSMTP(); // 启用SMTP
	$mail->Host = C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
	$mail->Port = C('MAIL_PORT'); //smtp服务器的端口号
	$mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
	$mail->Username = C('MAIL_USERNAME'); //你的邮箱名
	$mail->Password = C('MAIL_PASSWORD'); //邮箱密码
	$mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
	$mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
	$mail->AddAddress($to, "尊敬的客户");
	$mail->WordWrap = 50; //设置每行字符长度
	$mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
	$mail->CharSet = C('MAIL_CHARSET'); //设置邮件编码
	$mail->Subject = $title; //邮件主题
	$mail->Body = $content; //邮件内容
	// $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
	return ($mail->Send());
}

/**
 * 短信发送函数
 */
function sendSms($mobile = '', $passcode = '') {
    $apikey = C('YUNPIAN_MSG_API_KEY');
    $text = '【116旅游网】您好！您此次的验证码是'.$passcode.'。工作人员不会向您索要验证码，请不要告诉任何人！';
    $post_data = array('text' => $text, 'apikey' => $apikey, 'mobile' => $mobile);
    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 15 * 60, // 超时时间（单位:s）
        ),  
    );  
    $context = stream_context_create($options);
    $result = file_get_contents('https://sms.yunpian.com/v2/sms/single_send.json', false, $context);
    dump($result);
    return $result;
}

/**
 * 功能：生成二维码
 * @param string $qr_data     手机扫描后要跳转的网址
 * @param string $qr_level    默认纠错比例 分为L、M、Q、H四个等级，H代表最高纠错能力
 * @param string $qr_size     二维码图大小，1－10可选，数字越大图片尺寸越大
 * @param string $save_path   图片存储路径
 * @param string $save_prefix 图片名称前缀
 */
function createQRcode($save_path,$qr_data='PHP QR Code :)',$qr_level='L',$qr_size=4,$save_prefix='qrcode'){
    if(!isset($save_path)) return '';
    //设置生成png图片的路径
    $PNG_TEMP_DIR = & $save_path;
    //导入二维码核心程序
    vendor('PHPQRcode.class#phpqrcode');
    //检测并创建生成文件夹
    if (!file_exists($PNG_TEMP_DIR)){
        mkdir($PNG_TEMP_DIR);
    }
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    if (isset($qr_level) && in_array($qr_level, array('L','M','Q','H'))){
        $errorCorrectionLevel = & $qr_level;
    }
    $matrixPointSize = 4;
    if (isset($qr_size)){
        $matrixPointSize = & min(max((int)$qr_size, 1), 10);
    }
    if (isset($qr_data)) {
        if (trim($qr_data) == ''){
            die('data cannot be empty!');
        }
        //生成文件名 文件路径+图片名字前缀+md5(名称)+.png
        $filename = $PNG_TEMP_DIR.$save_prefix.md5($qr_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        //开始生成
        QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    } else {
        //默认生成
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    }
    if(file_exists($PNG_TEMP_DIR.basename($filename)))
        return basename($filename);
    else
        return FALSE;
}
