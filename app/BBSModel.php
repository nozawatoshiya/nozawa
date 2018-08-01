<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FXModel;

class BBSModel extends FXModel
{
  protected $Layout;
  protected $dateBBS;
  protected $categoryBBS;
  protected $contentBBS;

  public function __construct(){
    $this->Layout ='掲示板';
    $this->dateBBS ='日付';
    $this->categoryBBS ='区分';
    $this->contentBBS ='内容';
  }
  public function getBBS($date_range){
    $Params = array( $this->dateBBS => $date_range);
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
