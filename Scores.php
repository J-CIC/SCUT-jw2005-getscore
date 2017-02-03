<?php
// ini_set("display_errors", "On");
// error_reporting(E_ALL | E_STRICT);
include('../model.php');
class Score
{
	function getImage($xh)
	{
		$verify_code_url = "http://110.65.10.231/CheckCode.aspx"; //验证码地址
		$curl = curl_init();	
		curl_setopt($curl, CURLOPT_URL, $verify_code_url);
		curl_setopt($curl, CURLOPT_TIMEOUT,20);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$img = curl_exec($curl);  //执行curl
		$tmp = curl_getinfo($curl);
		curl_close($curl);
		preg_match_all("/\((.*)\)/s",$tmp['redirect_url'],$match);
		if(isset($match[0][0]))
		{
			$curl = curl_init();	
			curl_setopt($curl, CURLOPT_URL, $tmp['redirect_url']);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_TIMEOUT,20);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$img = curl_exec($curl);  //执行curl
			$tmp = curl_getinfo($curl);
			curl_close($curl);
			$fp = fopen("images/".$xh.".jpg","w");  //文件名
			fwrite($fp,$img);  //写入文件 
			fclose($fp);
		}
		$url_c = $match[0][0];
		return $url_c;
	}
	function login($xh,$pw,$url_c,$code)
	{
		$url="http://110.65.10.231/".$url_c."/default2.aspx";  //教务处地址
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT,20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  //不自动输出数据，要echo才行
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //重要，抓取跳转后数据
		curl_setopt($ch, CURLOPT_REFERER, 'http://110.65.10.231/');  //重要，302跳转需要referer，可以在Request Headers找到 
		$result=curl_exec($ch);
		$tmp = curl_getinfo($ch);
		$curl_errno = curl_errno($ch);  
		$curl_error = curl_error($ch);  
		if($curl_errno >0){  
			$error = "cURL Error ($curl_errno): $curl_error\n";  
			$result = array(false,$error);
			json_encode($result);
			return false;
		}
		preg_match_all('/<input type="hidden" name="__VIEWSTATE" value="([^<>]+)" \/>/', $result, $view);
		$view = $view[1][0];
		$post=array(
			'__VIEWSTATE'=>$view,
			'TextBox1'=>$xh,
			'TextBox2'=>$pw,
			'TextBox3'=>$code,
			'RadioButtonList1'=>'%D1%A7%C9%FA',  //“学生”的gbk编码
			'Button1'=>'',
			'lbLanguage'=>'',
			'hidPdrs'=>'',
			'hidsc'=>''
		);
		$url="http://110.65.10.231/".$url_c."/default2.aspx";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT,5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  //不自动输出数据，要echo才行
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //重要，抓取跳转后数据
		curl_setopt($ch, CURLOPT_REFERER, 'http://110.65.10.231/');  //重要，302跳转需要referer，可以在Request Headers找到 
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);  //post提交数据
		$result=curl_exec($ch);
		// echo  iconv("GB2312","UTF-8//IGNORE",$result);
		$tmp = curl_getinfo($ch);
		$curl_errno = curl_errno($ch);  
		$curl_error = curl_error($ch);  
		if($curl_errno >0){  
			$error = "cURL Error ($curl_errno): $curl_error\n";  
			$result = array(false,$error);
			echo json_encode($result);
			return false;
		}
		preg_match_all( '/(?:\()(.*)(?:\))/i', $result, $check);
		if(isset($check[0][16]))
		{
			$check = explode("(",$check[0][16]);
			$check = explode(")",$check[1]);
			// echo iconv("GB2312","UTF-8//IGNORE",$check[0]);
			return -1;
		}
		curl_close($ch);
		return 1;
	}
	function get($xh,$url_c)
	{
		$url="http://110.65.10.231/".$url_c."/xscjcx.aspx?xh=".$xh."&gnmkdm=N121605";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT,40);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  //不自动输出数据，要echo才行
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //重要，抓取跳转后数据
		curl_setopt($ch, CURLOPT_REFERER, 'http://110.65.10.231/');  //重要，302跳转需要referer，可以在Request Headers找到 
		// curl_setopt($ch, CURLOPT_POSTFIELDS,$post);  //post提交数据
		$result=curl_exec($ch);
		$tmp = curl_getinfo($ch);
		$curl_errno = curl_errno($ch);  
		$curl_error = curl_error($ch);  
		if($curl_errno >0){  
			$error = "cURL Error ($curl_errno): $curl_error\n";  
			$result = array(false,$error);
			// echo json_encode($result);
			return false;
		}
		curl_close($ch);
		preg_match_all('/<input type="hidden" name="__VIEWSTATE" value="([^<>]+)" \/>/', $result, $view);
		$view = $view[1][0];
		$result = array(1,$view);
		return $result;
	}
	function getScore($xh,$view,$url_c)
	{
		$post=array(
			'__EVENTTARGET'=>'',
			'__EVENTARGUMENT'=>'',
			'__VIEWSTATE'=>$view,
			'hidLanguage'=>'',
			'ddlXN'=>'',  //当前学年
			'ddlXQ'=>'',  //当前学期
			'ddl_kcxz'=>'',
			// 'btn_xq'=>'%D1%A7%C6%DA%B3%C9%BC%A8'  //“学期成绩”的gbk编码，视情况而定
			// 'btn_xn'=>'%D1%A7%C4%EA%B3%C9%BC%A8' //"学年成绩"gbk编码
			'btn_zcj'=>'%C0%FA%C4%EA%B3%C9%BC%A8'
		);
		$url="http://110.65.10.231/".$url_c."/xscjcx.aspx?xh=".$xh."&gnmkdm=N121605";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT,40);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  //不自动输出数据，要echo才行
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //重要，抓取跳转后数据
		curl_setopt($ch, CURLOPT_REFERER, 'http://110.65.10.231/');  //重要，302跳转需要referer，可以在Request Headers找到 
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);  //post提交数据
		$result=curl_exec($ch);
		$tmp = curl_getinfo($ch);
		$curl_errno = curl_errno($ch);  
		$curl_error = curl_error($ch);  
		if($curl_errno >0){  
			$error = "cURL Error ($curl_errno): $curl_error\n";  
			$result = array(false,$error);
			echo json_encode($result);
			die();
			return false;
		}
		curl_close($ch);
		$content = $this->get_td_array($result);
		$count = count($content);
		$result = '';
		$j=0;
		if($count>4)
		{
			for($i=5;$content[$i][0]!='';$i++)
			{
				$result[$j]['name'] = $content[$i][3].'('.$content[$i][4].')';
				$result[$j]['score'] = $content[$i][8];
				$result[$j]['rank'] = $content[$i][15];
				$result[$j]['gpa'] = $content[$i][7];
				$result[$j]['point'] = $content[$i][6];
				$j++;
				// $result.="<span style='color:#6bae40'>".$content[$i][3].'('.$content[$i][4].')</span><br>--成绩:'.$content[$i][8].'<br>--排名:'.$content[$i][15].'<br>--绩点:'.$content[$i][7]."<br>--学分:".$content[$i][6]."<br>";
			}
		}
		$f_result = array(1,$result);
		return $f_result;
	}
	function get_td_array($table) {
		$arr = array("\t", "\n","\r");
		$arr2 = array("", "", "");
		$table=str_replace($arr,$arr2,$table);
		$table = iconv("GB2312","UTF-8//IGNORE",$table);
		$table = preg_replace("'<table[^>]*?>'si","",$table);
		$table = preg_replace("'<tr[^>]*?>'si","",$table);
		$table = preg_replace("'<td[^>]*?>'si","",$table);
		$table = str_replace("</tr>","{tr}",$table);
		$table = str_replace("</td>","{td}",$table);
		//去掉 HTML 标记
		$table = preg_replace("'<[/!]*?[^<>]*?>'si","",$table);
		//去掉空白字符
		$table = preg_replace("'([rn])[s]+'","",$table);
		$table = preg_replace('/&nbsp;/',"",$table);
		$table = str_replace(" ","",$table);
		$table = str_replace(" ","",$table);
		$table = explode('{tr}', $table);
		array_pop($table);
		foreach ($table as $key=>$tr) {
			$td = explode('{td}', $tr);
			array_pop($td);
			$td_array[] = $td;
		}
		return $td_array;
	}
}
if(isset($_POST['login']))
{
	$xh = $_POST['xh'];
	$pw = $_POST['pw'];
	$code = $_POST['code'];
	$url_c = $_POST['url_c'];
	$s = new Score();
	$url_c = $s->getImage($xh); 
	$res = $s->login($xh,$pw,$url_c,$code);
	$result = array($res,$url_c);
	echo json_encode($result);
}
if(isset($_POST['get']))
{
	$xh = $_POST['xh'];
	$url_c = $_POST['url_c'];
	$s = new Score();
	$result = $s->get($xh,$url_c);
	echo json_encode($result);
}
if(isset($_POST['getScore']))
{
	$xh = $_POST['xh'];
	$url_c = $_POST['url_c'];
	$view = $_POST['view'];
	$s = new Score();
	$res = $s->getScore($xh,$view,$url_c);
	echo json_encode($res);
}
?>