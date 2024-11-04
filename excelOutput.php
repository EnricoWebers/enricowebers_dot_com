<?php 

    $req_url = 'https://v6.exchangerate-api.com/v6/76852436da9860dd554a1870/latest/EUR';
    $response_json = file_get_contents($req_url);

    $fxText = $response_json;
    $fxBase = substr($fxText, stripos($fxText,"base") + 7, 3);
    $fxDate = substr($fxText, stripos($fxText,"update_utc") + 17, 12);
    $fxText = substr($fxText, stripos($fxText,"rates") + 8);
    $fxText = str_replace('"','',$fxText);
    $fxText = str_replace('}','',$fxText);
    $fxBits = explode(',',$fxText);
    $i = 0;
    foreach ( $fxBits as $f ) {
      $fxBitSub = explode(':',$f);
      foreach ( $fxBitSub as $fs ) {
        if ( is_numeric($fs) ) {
          $fxRate[$i] = $fs;
        } else {
          $fxISO[$i] = $fs;
          $fxISO[$i] = substr($fxISO[$i],3,3);
        }
      }
      $i++;
    }

    $fxDateDD = substr($fxDate,1,2);
    $fxDateMM = substr($fxDate,4,3);
    switch ($fxDateMM) {
      case "Jan": $fxDateMM = "01"; break;
      case "Feb": $fxDateMM = "02"; break;
      case "Mar": $fxDateMM = "03"; break;
      case "Apr": $fxDateMM = "04"; break;
      case "May": $fxDateMM = "05"; break;
      case "Jun": $fxDateMM = "06"; break;
      case "Jul": $fxDateMM = "07"; break;
      case "Aug": $fxDateMM = "08"; break;
      case "Sep": $fxDateMM = "09"; break;
      case "Oct": $fxDateMM = "10"; break;
      case "Nov": $fxDateMM = "11"; break;
      case "Dec": $fxDateMM = "12"; break;
    }
    $fxDateYY = substr($fxDate,8,4);

    $ReMarkCurrencies = ['AED','ARS','AUD','BGN','BHD','BRL','CAD','CHF','CLP','CNY','COP','CZK','DKK','GBP','HKD','HRK','HUF','IDR','ILS','INR','ISK','JPY','KES','KRW','KWD','LKR','MXN','MYR','NGN','NOK','NZD','OMR','PEN','PHP','PKR','PLN','QAR','RON','RUB','SAR','SEK','SGD','THB','TRY','TWD','USD','ZAR'];

    $delimiter = ",";
    $filename = "Koerslijst giraal_nl ".$fxDateDD.$fxDateMM.$fxDateYY.".csv";

    $f = fopen('php://memory', 'w');

    $fields = array('Notering', $fxDateDD.'/'.$fxDateMM.'/'.$fxDateYY, 'Versie:','','       Bankpapier ','',' 1 EUR=');
    fputcsv($f, $fields, $delimiter);

    $fields = array('Muntsoort', '', 'Giraal','','klant ontvangt','klant betaalt');
    fputcsv($f, $fields, $delimiter);

    for ( $j = 0 ; $j < $i-1 ; $j++ ) {
      $lineData = array($fxISO[$j], 'Filler', $fxRate[$j],'','','','');
      if ( in_array($fxISO[$j],$ReMarkCurrencies) ) {
        fputcsv($f, $lineData, $delimiter);
      }
    }

    fseek($f, 0);

    header('Content-type: text/csv');
    header('Content-disposition: attachment; filename="'.$filename.'";');

    fpassthru($f);


?>    
