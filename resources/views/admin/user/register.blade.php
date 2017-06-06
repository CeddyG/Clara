@extends('dashboard')

@section('content')
    
    <div class="col-sm-6">
    	<br>
        <div class="box box-info">	
            <div class="box-header with-border">
                @if(isset($objet))
                    <h3 class="box-title">Modification</h3>
                @else
                    <h3 class="box-title">Ajouter</h3>
                @endif
            </div>
            <div class="box-body"> 
                <div class="col-sm-12">
                    @if(isset($objet))
                        {!! Form::model($objet, ['route' => ['sentinel.update', $objet->id_client], 'method' => 'put', 'class' => 'form-horizontal panel']) !!}
                    @else
                        {!! Form::open(['url' => 'sentinel', 'method' => 'post', 'class' => 'form-horizontal panel']) !!}
                    @endif
                    <div class="form-group {!! $errors->has('last_name') ? 'has-error' : '' !!}">
                            {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'Nom']) !!}
                            {!! $errors->first('last_name', '<small class="help-block">:message</small>') !!}
                    </div>
                    
                    <div class="form-group {!! $errors->has('first_name') ? 'has-error' : '' !!}">
                            {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => 'Prenom']) !!}
                            {!! $errors->first('first_name', '<small class="help-block">:message</small>') !!}
                    </div>
                    <div class="form-group {!! $errors->has('email') ? 'has-error' : '' !!}">
                            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) !!}
                            {!! $errors->first('email', '<small class="help-block">:message</small>') !!}
                    </div>
                    
                    <div class="form-group {!! $errors->has('password') ? 'has-error' : '' !!}">
                            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) !!}
                            {!! $errors->first('password', '<small class="help-block">:message</small>') !!}
                    </div>

                    {!! Form::submit('Envoyer', ['class' => 'btn btn-primary pull-right']) !!}

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <a href="javascript:history.back()" class="btn btn-primary">
                <span class="glyphicon glyphicon-circle-arrow-left"></span> Retour
        </a>
    </div>
@stop

