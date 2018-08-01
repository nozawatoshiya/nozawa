      <div class="navbar navbar-inverse" role="navigation">
        <div class="container">
          <div class="font">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
              <a class="navbar-brand" href="{{url('/logout')}}"><font color="white">勤怠管理システム</font></a>
            </div>
            @if(Session::has('user'))
            <div class="collapse navbar-collapse">
              <ul class="nav navbar-nav">
                <!--<li class="active"><a href="{{url('/mypage')}}">Home</a></li>-->
                <li><a href="{{url('/mypage')}}">Home</a></li>
                <li><a href="{{url('/archives')}}">過去データ</a></li>
                @if(session('user.auth')=="管理者")
                <li><a href="{{url('/edit')}}">勤怠修正</a></li>
                <li><a href="{{url('/usermastar')}}">ユーザー管理</a></li>
                @endif
                <li><a href="{{url('/help')}}">お問い合わせ</a></li>
              </ul>
              <p class="navbar-text navbar-right"><a href="{{url('/logout')}}" class="navbar-link">ログアウト</a></p>
              <p class="navbar-text navbar-right">ようこそ{{session('user.name')}}さん</p>
            </div>

            @endif
          </div>
          <!--/.nav-collapse -->
        </div>
      </div>
