<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserModel;
use App\Http\Requests\registUserRequest;
use App\Http\Requests\ChengePassRequest;

class UserController extends Controller
{
    protected $UserModel;

    public function __construct(){
      $this->UserModel = new UserModel;
    }

    public function getUser(){
      $result = $this->UserModel->getUser();
      $message = "" ;
      $datas = $result['datas'];
//dd($prefs);
       return view('user',compact('datas','message'));
    }

    public function registUser(registUserRequest $inputs){
      $account = $inputs['registAccount'];
      $name = $inputs['registName'];
      $password = $inputs['registPassword'];

      //権限入力が無い場合は一般で登録する。
      if($inputs['registpermission']==""){
        $permission = '一般';
      }else{
        $permission = $inputs['registpermission'];
      }

      $result = $this->UserModel->searchUser($account);

      if($result['errorCode']=='0'){
        //検索してヒットしたらエラーメッセージを返す。
        \Session::flash('registErrorMessage','社員番号：'.$account.'  氏名：'.$name.'はすでに登録されてるみたい。');
        return redirect()->back()->withInput();
      }else{
        //検索してヒットしなかったら新規登録
        $result = $this->UserModel->registUser($account,$name,$password,$permission);
        \Session::flash('message', '社員番号：'.$account.'  氏名：'.$name.'のユーザー登録が完了しました。');

        return redirect(url('/usermastar'));
      }

    }

    public function editUser(Request $inputs){
      $account = $inputs['account'];
      $name = $inputs['editUserName'];
      $permission = $inputs['editUserPermission'];
      $delete = $inputs['editUserDelete'];
      $recId = $inputs['RecId'];

      $result = $this->UserModel->editUser($name, $permission, $delete, $recId);

      if($result['errorCode']!='0'){
        \Session::flash('registErrorMessage',$result['message']);
        return redirect()->back()->withInput();
      }else{
        \Session::flash('message', '社員番号：'.$account.'  氏名：'.$name.'の編集が完了しました。');
        return redirect(url('/usermastar'));
      }
    }

    public function deleteUser($recId){

      $delete = '削除';

      $result = $this->UserModel->delUser($recId,$delete);

      if($result['errorCode']!='0'){
        \Session::flash('registErrorMessage',$result['message']);
        return redirect()->back()->withInput();
      }else{
        \Session::flash('message', '削除処理が完了しました。');
        return redirect(url('/usermastar'));
      }

    }

    public function changePass(ChengePassRequest $inputs){
      $pass = $inputs['newpass'];
      $id = $inputs['RecId'];

      $old1 = $inputs['pass'];
      $old2 = $inputs['password'];
      $checkpass = $inputs['checkpass'];

      if($pass==$checkpass and $old1==$old2){
        $result = $this->UserModel->changePass($pass,$id);

        if($result['errorCode']!='0'){
          \Session::flash('message',$result['message']);
          return redirect()->back()->withInput();
        }else{
          //パスワード変更後userセッションに新しい値を入れ直す

          $user = array( 'id' => $result['アカウント'],
                         'name'=> $result['氏名'],
                         'auth' => $result['権限'],
                         'memo_p' => $result['メッセージ'],
                         'password'=> $result['パスワード'],
                         'RecId' => $result['RecId']);
          $uName = $result['氏名'];
          \Session::put('user',$user);
          \Session::put('Authenticated',true);

          \Session::flash('completeMessage', 'パスワード変更処理が完了しました。');

          return redirect('./mypage');
        }
      }else{
        \Session::flash('message','入力に誤りがあります。');
        return redirect()->back()->withInput();
      }
    }

}
