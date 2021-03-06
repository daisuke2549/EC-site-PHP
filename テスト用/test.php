<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
<?php

public function formalDate($dt) {
	$msg = '';  // 正規化メッセージ

	// 全角->半角
	$ret = mb_convert_kana($dt, 'a');
	// '/' を '-' に変換
	//複数文字列の置換(str_replace)
	$ret = str_replace('/', '-', $ret);
	// '.' を '-' に変換
	//sourceをSourceに、destinationをDestinationに置換したい場合、以下のように一括で置換できる。
	$ret = str_replace('.', '-', $ret);

	//数字のみが入力されている場合はハイフンで区切る


    //preg_matchではある文字列から正規表現で指定したパターンにマッチした文字列を検索することができます。
	if (preg_match("/^[0-9]+$/", $ret)) {
		//年月日全てが入力されている状態(数字8桁のみ) 
		//substr — 文字列の一部分を返す
		//strlen = 文字の長さを取得
		if(strlen($ret) == 8){
			$ret = substr($ret,0,4) . '-' . substr($ret,4,2) .  '-' . substr($ret,-2);
		}
		//年月のみ入力されている状態(数字6桁のみ)
		if(strlen($ret) == 6){
			$ret = substr($ret,0,4) . '-' . substr($ret,4,2);
		}
	}

	// 時刻部分の削除
	// strpos関数は検索文字列が見つかったら、見つかった文字列の最初の位置を取得します。
	$pos = strpos($dt, 'T');
	if ($pos) {
		$ret = substr($ret, 0, $pos);
	}

	// 月 or 年の00をカット  2010-01 2011-00-00
	$len = strlen($ret);
	if ($len === 10 && substr($ret, 8, 2) === '00') {
		$ret = substr($ret, 0, 7);
		$msg = '日の00をカットしました。';
		$len = strlen($ret);
	}
	if ($len === 7 && substr($ret, 5, 2) === '00') {
		$ret = substr($ret, 0, 4);
		if (empty($msg)) {
			$msg = '月の00をカットしました。';
		} else {
			$msg = '月日の00をカットしました。';
		}
	}

	$convert_tmp = $ret;  //年号変換ができなかった時のリターン値
	$g = mb_substr($ret, 0, 2, 'UTF-8');

	if ($g == '明治' || $g == '大正' || $g == '昭和' || $g == '平成') {
		if (strpos($ret, '-')) return $ret; // 平成28-10-23 の形式は変換しない

		$nen = mb_strpos($ret, '元年');
		if ($nen) {
			$ret = str_replace('元年', '1年', $ret);
		}

		$ret = str_replace($g, '', $ret);
		$y = strtok($ret, '年月日');
		$m = strtok('年月日');
		$d = strtok('年月日');

		//年号が入っている場合のymdチェック
		//年号が入っている場合、ymd全てに数値が入力されていなければ$retの変換を行わない。
		//"昭和9年度"のように不正な値が入力された場合にこれ以降の処理ができなくなるため。
		if(!is_numeric($y) || !is_numeric($m) || !is_numeric($d)){
			return $convert_tmp;
		}

		if ($g === '平成') $y += 1988;
		elseif ($g === '昭和') $y += 1925;
		elseif ($g === '大正') $y += 1911;
		elseif ($g === '明治') $y += 1868;

		if (!empty($d)) {
			$ret = $y . '-' . $m . '-' . $d;
		} elseif (empty($m)) {
			$ret = $y;
		} else {
			$ret = $y . '-' . $m;
		}
	}

	return array($ret, $msg);
}

</body>
</html>
