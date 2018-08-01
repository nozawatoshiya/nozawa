<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Larafm;

class Test extends Larafm
{
    function __construct(){
      Larafm::setLayout("勤怠");
      Larafm::setDate(['日付']);
      return $this;
    }
}
