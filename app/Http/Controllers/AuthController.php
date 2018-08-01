<?php

namespace App\Http\Controllers;

use App\AuthModel;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;

class AuthController extends Controller
{
    protected $AuthModel;

    public function __construct(){
      $this->AuthModel = new AuthModel();
    }
    public function check(AuthRequest $inputs){
      $result = $this->AuthModel->Authenticate($inputs['ID'],$inputs['password']);

      if($result['errorCode']!=0){
        \Session::flash('Error',$result['message']);
        return back()->withInput();
      }elseif($result['errorCode']==0 and $result['フラグ_削除']=='削除'){
        \Session::flash('Error','アカウントは削除されています');
        return back()->withInput();
      }else{
        $user = array( 'id' => $result['アカウント'],
                       'name'=> $result['氏名'],
                       'auth' => $result['権限'],
                       'memo_p' => $result['メッセージ'],
                       'password'=> $result['パスワード'],
                       'RecId' => $result['RecId']);
        $uName = $result['氏名'];
        \Session::put('user',$user);
        \Session::put('Authenticated',true);

        return redirect('/mypage');
      }
    }

    public function logout(){
      \Session::flush();
      return redirect('/');
    }
}
