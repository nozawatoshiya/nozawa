<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FXModel;

class FAModel extends FXModel
{
  public function __construct(){
    $this->Layout ='問い合わせ';
  }

  public function registFA($id,$category,$title,$content){
    $Params = array('アカウント'=>$id,
                   '区分'=>$category,
                   '題名'=>$title,
                   '内容'=>$content);
    $result = $this->createData($Params);

    return $result;
  }
}
