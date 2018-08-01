<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FXModel;

class BBSAModel extends FXModel
{
  public function __construct(){
    $this->Layout ='掲示板_個人';
    $this->dateBBS ='日付';
    $this->id ='アカウント';
  }
  public function getBBS($date_range,$id){
    $Params = array( $this->dateBBS => $date_range,
                     $this->id=> $id);
    $result=$this->readDataNoOpt($Params);
    if($result['errorCode']!=0){
      switch($result['errorCode']){
        case '401':
          $result["message"]='メッセージはありませんよ。';
          break;
        default:
          $result["message"]=$result['errorCode'] . '' . $this->getErrorDescription($result['errorCode']);
          break;
      }
    }
    return $result;
  }
}
