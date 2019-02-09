<?php

class String_Con
{
// class - Start

	// ���ĺ� ����
	function is_alpha($char){
		$char=ord($char);

		if($char>=0x61 && $char<=0x7a){
			return 1; # �ҹ��ڸ� �ǹ�
		}

		if($char>=0x41 && $char<=0x5a){
			return 2; # �빮�ڸ� �ǹ�
		}
	}


	// �ѱ� ����
	function is_hangul($char){
		# Ư�� ���ڰ� �ѱ��� ������(0xA1A1 - 0xFEFE)�� �ִ��� �˻�
		$char=ord($char);

		if($char>=0xa1 && $char<=0xfe){
			return 1;
		}
	}


	// ���ڿ� �ڸ���
	function cut_string($string, $length){
		if(strlen($string)<=$length&&!eregi("^[a-z]+$", $string)){
			return $string;
		}

		for($i=$length; $i>=1; $i--) {
			# ���������� �ѱ� byte���� ����.
			if($this->is_hangul($string[$i-1])){
				$hangul++;
			}else{
				break;
			}
		}

		if($hangul){
			# byte���� Ȧ���̸�, �ѱ��� ù��° ����Ʈ�̴�.
			# �ѱ��� ù��° ����Ʈ�� �� ������ ���� ���� ���� ������ ���̸� ��
			# ����Ʈ ����
			if($hangul%2){
				$length--;
			}
			$string=chop(substr($string,0,$length));
		}else{ # ���ڿ��� ���� �ѱ��� �ƴ� ���
			for($i=1; $i<=$length; $i++){
				# �빮���� ������ ���
				if($this->is_alpha($string[$i-1])==2){
					$alpha++;
				}
				# ������ �ѱ��� ��Ÿ�� ��ġ ���
				if($this->is_hangul($string[$i-1])){
					$last_han=$i;
				}
			}
				# ������ ���̷� ���ڿ��� �ڸ��� ���ڿ� ���� ���� ���ڸ� ������
				# �빮���� ���̴� 1.3���� ����Ѵ�. ���ڿ� �������� ���� ���ڿ���
				# ������ ��ü ���̺��� ũ�� �ʰ��� ��ŭ ����.
			$capitals=intval($alpha*0.5);
			if(($length-$last_han)<=$capitals){
				$capitals=0;
			}
			$string=chop(substr($string,0,$length-$capitals));
		}
		$string=$string."..";
		return $string;
	}


	// HTML Tag ���� �Լ�
	function delete_tag($text){
		$src=array("/\n/i","/<html.*<body[^>]*>/i","/<\/body.*<\/html>.*/i",
					"/<\/*(div|span|layer|body|html|head|meta|input|select|option|form)[^>]*>/i",
					"/<(style|script|title).*<\/(style|script|title)>/i",
					"/<\/*(script|style|title|xmp)>/i","/<(\\?|%)/i","/(\\?|%)>/i",
					"/#\^--ENTER--\^#/i");
		$tar=array("#^--ENTER--^#","","","","","","&lt;\\1","\\1&gt;","\n");
		$text=chop(preg_replace($src,$tar,$text));
		return $text;
	}

  // ��ũ ,����������, map �� ����
  function delete_link($text){
   $text = preg_replace('/<IFRAME(.*?)>(.*?)<\\/IFRAME>/i', '$2', $text);
   $text = preg_replace('/<(.*?)href=(.*?)>/i', '', $text);
   $text = str_replace("</a>",'', $text);
   $text = str_replace("</A>",'', $text);
   return $text;
 }

	// ���ڿ� ���� �Լ�
	function clear_text($text){
	 $text=eregi_replace("(#|'|\\\\)", "\\\\1", $text);
		$text=eregi_replace("(\r*)\n", "<BR>", $text);
		$text=eregi_replace("\"", "&quot;", $text);
		$text=eregi_replace("\'", "&#039;" , $text);
		$text=eregi_replace(" ", "&nbsp;", $text);
		return $text;
	}


	// html����� ���� ��� IE���� �ѱ� ������ ���� �ʴ� ���� ǥ���� ������ ���� ����
	function ugly_han($text,$html=0){
		if(!$html){	$text=preg_replace("/&amp;(#|amp)/i","&\\1",$text);	}
		$text=str_replace("&amp;","&",$text);
		return $text;
	}

 function url_decoder($text){
   $text = urldecode($text);
   return $text;
 }

	// ���ޱ� ��ȣ ��ȯ
	function change_gal($text){
		$text=str_replace("<table>","&lt;table&gt;",$text);
		$text=str_replace("</table>","&lt;/table&gt;",$text);
		$text=str_replace("<td>","&lt;td&gt;",$text);
		$text=str_replace("</td>","&lt;/td&gt;",$text);
		$text=str_replace("<tr>","&lt;tr&gt;",$text);
		$text=str_replace("</tr>","&lt;/tr&gt;",$text);
		return $text;
	}

	// ��¥ ��� ����
	function ext_date($time,$type){

		$year	=	substr($time,0,4);
		$syear	=	substr($time,2,2);
		$month	=	substr($time,5,2);
		$day		=	substr($time,8,2);
		$hour	=	substr($time,11,2);
		$min		=	substr($time,14,2);
		$sec		=	substr($time,17,2);

		switch($type){
		case "all" :
			$date=$year."-".$month."-".$day." ".$hour.":".$min.":".$sec;
			return $date;
			break;
		case "ymdm" :
			$date=$year."-".$month."-".$day;
			return $date;
			break;
		case "ymds" :
			$date=$year."/".$month."/".$day;
			return $date;
			break;
		case "kymd" :
			$date=$year."��".$month."��".$day."��";
			return $date;
			break;
		case "mds" :
			$date=$month."/".$day;
			return $date;
			break;
		case "mdm" :
			$date=$month."-".$day;
			return $date;
			break;
		case "mdsh" :
			$date=$month."/".$day." ".$hour.":".$min.":".$sec;
			return $date;
			break;
		case "ymdhi" :
			$date=$year."-".$month."-".$day." ".$hour.":".$min;
			return $date;
			break;
  case "ymdms" :
			$date=$syear.".".$month.".".$day;
			return $date;
		break;
		default  :
			return "error! �ɼ��� �� �������ּ���.";
			break;
		}
	}

//-----------------------------------------
//   ���̺� ���� ����
//-----------------------------------------
function Guard_table($text,$section,$Img_widmax){

  $text = str_replace("<br />","",$text);
  $text = $this->change_gal($text);

  // �̹��� �ױ� �߰� ����
  if($section=="img"){
    return urlencode( preg_replace("/<img /","<img style=\"cursor:hand\" onload=\"resizeImgWidth(this,$Img_widmax);\" onclick=\"poporigin(this);\" " , trim($text) ) );
  }
  // �Ϲ� ����
  else{
    return urlencode($text);
  }

// function - End
}


// class - End
}

?>