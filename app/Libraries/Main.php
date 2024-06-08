<?php 
namespace App\Libraries;

class Main
{

    protected $ApiModel=NULL;

    function __construct(){
		$this->ApiModel 	= model('App\Models\ApiModel', false);
	}

    static function pr($ar,$ex=0){
		echo '<pre>';
		print_r($ar); 
		echo '</pre>';
		if($ex==1){
			exit; 
		}
	}

    public function encrypt_decrypt($action, $string)
    {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'Task123';
        $secret_iv = '123456';
        // hash
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public function whereString($search, $tableAliasAr = array(), $operator = array(), $colmnName = array())
    {
        // $comp_db = $_ENV['database.default.database'];
        $whereAr[] = " 1=1";
        foreach ($search as $key => $fld) {
            $fld['name'] = trim($fld['name']);
            $fld['value'] = trim($fld['value']);
            if (in_array($fld['name'], array('list_type', 'list_id', 'pageNoH', 'recToShowH', 'orderByH', 'sortByH'))) {
                continue;
            }
            if (isset($tableAliasAr[$fld['name']]) && $tableAliasAr[$fld['name']] != '') $tableAlias = $tableAliasAr[$fld['name']];
            elseif (isset($tableAliasAr['*']) && $tableAliasAr['*'] != '') $tableAlias = $tableAliasAr['*'];
            else $tableAlias = '';
            if ($fld['value'] == '') {
                unset($_POST['search'][$key]);
            } else {
                $fld['name'] = ($fld['name']);
                $fld['value'] = ($fld['value']);
                $fld['name'] = ($fld['name']);
                $fld['value'] = ($fld['value']);
                if (isset($colmnName[$fld['name']]) && $colmnName[$fld['name']] != '') $fldName = $colmnName[$fld['name']];
                else $fldName =  $fld['name'];
                if (count($operator) > 0 && isset($operator[$fld['name']])) {
                    if ($operator[$fld['name']] == 'LIKE') {
                        $whereAr[] = $tableAlias . $fldName . " LIKE '%" . $fld['value'] . "%'";
                    } elseif ($operator[$fld['name']] == 'LIKELEFT') {
                        $whereAr[] = $tableAlias . $fldName . " LIKE '%" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'LIKERIGHT') {
                        $whereAr[] = $tableAlias . $fldName . " LIKE '" . $fld['value'] . "%'";
                    } elseif ($operator[$fld['name']] == 'GT') {
                        $whereAr[] = $tableAlias . $fldName . " > '" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'LT') {
                        $whereAr[] = $tableAlias . $fldName . " < '" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'GTE') {
                        $whereAr[] = $tableAlias . $fldName . " >= '" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'LTE') {
                        $whereAr[] = $tableAlias . $fldName . " <= '" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'IN') {
                        $whereAr[] = $tableAlias . $fldName . " IN (" . $fld['value'] . ")";
                    } elseif ($operator[$fld['name']] == 'NOTIN') {
                        $whereAr[] = $tableAlias . $fldName . " NOT IN (" . $fld['value'] . ")";
                    } elseif ($operator[$fld['name']] == 'NOT') {
                        $whereAr[] = $tableAlias . $fldName . " != '" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'DLIKE') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") LIKE '%" . $fld['value'] . "%'";
                    } elseif ($operator[$fld['name']] == 'DLIKELEFT') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") LIKE '%" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'DLIKERIGHT') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") LIKE '" . $fld['value'] . "%'";
                    } elseif ($operator[$fld['name']] == 'DGT') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") > '" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'DLT') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") < '" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'DGTE') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") >= '" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'DLTE') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") <= '" . $fld['value'] . "'";
                    } elseif ($operator[$fld['name']] == 'DIN') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") IN (" . $fld['value'] . ")";
                    } elseif ($operator[$fld['name']] == 'DNOTIN') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") NOT IN (" . $fld['value'] . ")";
                    } elseif ($operator[$fld['name']] == 'DNOT') {
                        $whereAr[] = "date(" . $tableAlias . $fldName . ") != '" . $fld['value'] . "'";
                    } else {
                        $whereAr[] = $tableAlias . $fldName . "='" . $fld['value'] . "'";
                    }
                }else {
                    $whereAr[] = $tableAlias . $fldName . "='" . $fld['value'] . "'";
                }
            }
        }
        return implode(' and ', $whereAr);
    }

}
