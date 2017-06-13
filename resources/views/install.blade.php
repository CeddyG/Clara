@extends('admin/dashboard')

@section('CSS')
    <!-- iCheck -->
    <link href="{{ asset('adminlte/plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')
    {!! BootForm::open()->action( url('install') )->post() !!}
    <div class="row">
        <div class="col-sm-6">
            <div class="box box-info">
                <div class="box-body">
                    <fieldset class="col-sm-12">
                        <legend class="box-title">Base de données</legend>
                        {!! BootForm::text('Host', 'host')->defaultValue('localhost')->required()->value(env('DB_HOST', '')) !!}
                        {!! BootForm::text('Driver', 'driver')->required()->value(env('DB_CONNECTION', 'mysql')) !!}
                        {!! BootForm::text('Port', 'port')->required()->value(env('DB_POST', '3306')) !!}
                        {!! BootForm::text('Database', 'database')->required()->value(env('DB_DATABASE', '')) !!}
                        {!! BootForm::text('Username', 'username')->required()->value(env('DB_USERNAME', '')) !!}
                        {!! BootForm::password('Password', 'db_password')->value(env('DB_PASSWORD', '')) !!}
                    </fieldset>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6">
            <div class="box box-info">
                <div class="box-body">
                    <fieldset class="col-sm-12">
                        <legend class="box-title">Compte administrateur</legend>
                        {!! BootForm::text('Email', 'email')->required() !!}
                        {!! BootForm::password('Password', 'password')->required() !!}
                        {!! BootForm::text('First name', 'first_name')->required() !!}
                        {!! BootForm::text('Last name', 'last_name')->required() !!}
                    
                        <div class="checkbox icheck">
                            <label>
                                <input name="remember" type="checkbox" class="minimal"> Remember me
                            </label>
                        </div>
                    </fieldset>
                </div>
            </div>
            {!! BootForm::submit('Send', 'btn-primary btn-block') !!}
        </div>
    </div>
    {!! BootForm::close() !!}
@stop

@section('JS')
    <script src="{{ asset ('adminlte/plugins/iCheck/icheck.min.js') }}"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
              });
        } );
    </script>
@endsection

