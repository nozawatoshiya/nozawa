<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FXModel;

class AuthModel extends FXModel
{
  protected $Layout;
  protected $idField;
  protected $passField;

  public function __construct(){
    $this->Layout ='ユーザー';
    $this->idField ='アカウント';
    $this->passField ='パスワード';
  }

  public function Authenticate($id,$pass){
    $Params = array( $this->idField => $id,
                    $this->passField=>$pass);
    $result=$this->readData($Params);
    if($result['errorCode']!=0){
      switch($result['errorCode']){
        case '401':
          $result["message"]='社員番号かパスワードに誤りがあります。';
          break;
        default:
          $result["message"]=$result['errorCode'] . '' . $this->getErrorDescription($result['errorCode']);
          break;
      }
    }
    return $result;
  }

}
