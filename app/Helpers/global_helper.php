<?php

function getData()
{
    $session = \Config\Services::session();
    if (!empty($session->get('CRM'))) {
        return $session->get('CRM');
    } else {
        return false;
    }
}

function ass_arr($str)
{
    $str = trim($str, ",");
    $mai_array = explode(";", $str);
    // print_r($str);
    $array = explode("@@", $str);

    $chunks = array_chunk($array, 2);
    $even = array_column($chunks, 0);
    $odd  = array_column($chunks, 1);
    return array_combine($even, $odd);
}



function totalRecCount($totalPage, $recCount)
{
    ?>
    <p class="pull-right" style="line-height:30px; margin:0; padding:0 10px;"><span>Total Pages:</span>
        <?php echo $totalPage; ?><span style="padding-left:15px;">Total Records:</span> <?php echo $recCount; ?></p>
    <?php
}

function noRecShow($pageNoAr = array(10, 25, 50, 100, 200, 500))
{
    foreach ($pageNoAr as $val) {
        echo '<option value="' . $val . '">' . $val . '</option>';
    }
}

function getStringOfAmount($num = 1)
{
    $ones = array(
        0 => "ZERO",
        1 => "ONE",
        2 => "TWO",
        3 => "THREE",
        4 => "FOUR",
        5 => "FIVE",
        6 => "SIX",
        7 => "SEVEN",
        8 => "EIGHT",
        9 => "NINE",
        10 => "TEN",
        11 => "ELEVEN",
        12 => "TWELVE",
        13 => "THIRTEEN",
        14 => "FOURTEEN",
        15 => "FIFTEEN",
        16 => "SIXTEEN",
        17 => "SEVENTEEN",
        18 => "EIGHTEEN",
        19 => "NINETEEN",
        "014" => "FOURTEEN"
    );
    $tens = array(
        0 => "ZERO",
        1 => "TEN",
        2 => "TWENTY",
        3 => "THIRTY",
        4 => "FORTY",
        5 => "FIFTY",
        6 => "SIXTY",
        7 => "SEVENTY",
        8 => "EIGHTY",
        9 => "NINETY"
    );
    $hundreds = array(
        "HUNDRED",
        "THOUSAND",
        "MILLION",
        "BILLION",
        "TRILLION",
        "QUARDRILLION"
    ); /*limit t quadrillion */
    $num = number_format($num, 2, ".", ",");
    $num_arr = explode(".", $num);
    $wholenum = $num_arr[0];
    $decnum = $num_arr[1];
    $whole_arr = array_reverse(explode(",", $wholenum));
    krsort($whole_arr, 1);
    $rettxt = "";
    foreach ($whole_arr as $key => $i) {

        while (substr($i, 0, 1) == "0")
            $i = substr($i, 1, 5);
        if ($i < 20) {
            /* echo "getting:".$i; */
            $rettxt .= $ones[$i];
        } elseif ($i < 100) {
            if (substr($i, 0, 1) != "0")  $rettxt .= $tens[substr($i, 0, 1)];
            if (substr($i, 1, 1) != "0") $rettxt .= " " . $ones[substr($i, 1, 1)];
        } else {
            if (substr($i, 0, 1) != "0") $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
            if (substr($i, 1, 1) != "0") $rettxt .= " " . $tens[substr($i, 1, 1)];
            if (substr($i, 2, 1) != "0") $rettxt .= " " . $ones[substr($i, 2, 1)];
        }
        if ($key > 0) {
            $rettxt .= " " . $hundreds[$key] . " ";
        }
    }

    if ($decnum > 0) {
        $rettxt .= " and ";
        if ($decnum < 20) {
            if (isset($ones[$decnum])) {
                $rettxt .= $ones[$decnum];
                $rettxt .= " CENTS";
            } else {
                // Handle the case when the index does not exist
                // For example, you can assign a default value or display an error message.
            }
        } elseif ($decnum < 100) {
            $tensDigit = substr($decnum, 0, 1);
            $onesDigit = substr($decnum, 1, 1);

            if (isset($tens[$tensDigit])) {
                $rettxt .= $tens[$tensDigit];
            } else {
                // Handle the case when the index does not exist
                // For example, you can assign a default value or display an error message.
            }

            if (isset($ones[$onesDigit]) && $ones[$onesDigit] != 'ZERO') {
                $rettxt .= " " . $ones[$onesDigit];
            }

            $rettxt .= " CENTS";
        }
    }
    return $rettxt;
}

function convertNum($num)
{
    $num = (int) $num;    // make sure it's an integer

    if ($num < 0)
        return 'negative' . convertTri(-$num, 0);

    if ($num == 0)
        return 'Zero';
    return convertTri($num, 0);
}
function convertTri($num, $tri)
{
    global $ones, $tens, $triplets, $count;
    $test = $num;
    $count++;
    // chunk the number, ...rxyy
    // init the output string
    $str = '';
    // to display hundred & digits
    if ($count == 1) {
        $r = (int) ($num / 1000);
        $x = ($num / 100) % 10;
        $y = $num % 100;
        // do hundreds
        if ($x > 0) {
            $str = $ones[$x] . ' Hundred';
            // do ones and tens
            $str .= commonloop($y, ' and ', '');
        } else if ($r > 0) {
            // do ones and tens
            $str .= commonloop($y, ' and ', '');
        } else {
            // do ones and tens
            $str .= commonloop($y);
        }
    }
    // To display lakh and thousands
    else if ($count == 2) {
        $r = (int) ($num / 10000);
        $x = ($num / 100) % 100;
        $y = $num % 100;
        $str .= commonloop($x, '', ' Lakh ');
        $str .= commonloop($y);
        if ($str != '')
            $str .= $triplets[$tri];
    }
    // to display till hundred crore
    else if ($count == 3) {
        $r = (int) ($num / 1000);
        $x = ($num / 100) % 10;
        $y = $num % 100;
        // do hundreds
        if ($x > 0) {
            $str = $ones[$x] . ' Hundred';
            // do ones and tens
            $str .= commonloop($y, ' and ', ' Crore ');
        } else if ($r > 0) {
            // do ones and tens
            $str .= commonloop($y, ' and ', ' Crore ');
        } else {
            // do ones and tens
            $str .= commonloop($y);
        }
    } else {
        $r = (int) ($num / 1000);
    }
    // add triplet modifier only if there
    // is some output to be modified...
    // continue recursing?
    if ($r > 0)
        return convertTri($r, $tri + 1) . $str;
    else
        return $str;
}

function commonloop($val, $str1 = '', $str2 = '')
{
    global $ones, $tens;
    $string = '';
    if ($val == 0)
        $string .= $ones[$val];
    else if ($val < 20)
        $string .= $str1 . $ones[$val] . $str2;
    else
        $string .= $str1 . $tens[(int) ($val / 10)] . $ones[$val % 10] . $str2;
    return $string;
}



?>