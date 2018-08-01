<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FXModel;

class UserModel extends FXModel
{
  protected $Layout;

  public function __construct(){
    $this->Layout ='ユーザー';
  }

  public function getUser(){
    $all = '*';
    $Params=array('アカウント' => $all);
    $result = $this->readDataNoOpt($Params);

    return $result;

  }

  public function searchUser($account){
    $Params=array('アカウント' => $account);
    $result = $this->readData($Params);

    return $result;

  }

  public function registUser($account,$name,$password,$permission){
    $Params = array('アカウント'=>$account,
                    '氏名'=>$name,
                    'パスワード'=>$password,
                    '権限'=>$permission);
    $result = $this->createData($Params);
  }

  public function editUser($name, $permission, $delete, $recId){
    $Param = array('氏名'=>$name,
                   '権限'=>$permission,
                   'フラグ_削除'=>$delete);
    $id = $recId;
    $result = $this->updateData($Param,$id);

    return $result;
  }

  public function delUser($recId,$delete){
    $Param = array('フラグ_削除'=>$delete);
    $id = $recId;

    $result = $this->updateData($Param,$id);

    return $result;

  }

  public function changePass($pass,$id){
    $Param = array('パスワード'=>$pass);

    $result = $this->updateData($Param,$id);

    return $result;
  }
}
