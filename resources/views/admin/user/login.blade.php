@extends('admin/dashboard')

@section('CSS')
    <!-- iCheck -->
    {{ Html::style('/adminlte/plugins/iCheck/all.css') }}
@stop

@section('content')
    
    <div class="login-box">
        <div class="login-logo">
          <a href="{{ url('/') }}"><b>Admin</b>LTE</a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>

            {!! Form::open(['url' => 'authenticate', 'method' => 'post', 'class' => 'panel']) !!}
            
            <div class="form-group {!! $errors->has('fail') ? 'has-error' : '' !!}">
                {!! $errors->first('fail', '<small class="help-block">:message</small>') !!}
            </div>
            
            <div class="form-group has-feedback">
              <input name="email" type="email" class="form-control" placeholder="Email">
              <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
              {!! $errors->first('email', '<small class="help-block">:message</small>') !!}
            </div>
          
            <div class="form-group has-feedback">
              <input name="password" type="password" class="form-control" placeholder="Password">
              <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              {!! $errors->first('password', '<small class="help-block">:message</small>') !!}
            </div>
          
            <div class="row">
              <div class="col-xs-8">
                <div class="checkbox icheck">
                  <label>
                      <input name="remember" type="checkbox" class="minimal"> Remember Me
                  </label>
                </div>
              </div>
              <!-- /.col -->
              <div class="col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
              </div>
              <!-- /.col -->
            </div>
          
          {!! Form::close() !!}

          <a href="#">I forgot my password</a><br>
          <a href="register.html" class="text-center">Register a new membership</a>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
@stop

@section('JS')
    <!-- iCheck 2 -->
    {{ Html::script('/adminlte/plugins/iCheck/icheck.min.js') }}
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
              });
        } );
    </script>
@endsection