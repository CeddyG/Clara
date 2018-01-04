@extends('admin/dashboard')

@section('CSS')
    <!-- Select 2 -->
    {!! Html::style('bower_components/select2/dist/css/select2.min.css') !!}
    <style>
        .select2
        {
            width: 100% !important
        }
        
        .input-group
        {
            width: 100% !important
        }
    </style>
@stop

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
                    @if(isset($oItem))
                        {!! BootForm::open()->action( route('admin.group.update', $oItem) )->put() !!}
                        {!! BootForm::bind($oItem) !!}
                    @else
                        {!! BootForm::open()->action( url('admin/group') )->post() !!}
                    @endif
                    
                    {!! BootForm::text('Nom', 'name') !!}
                    
                    @if(isset($oItem))
                        {!! BootForm::select('Permissions', 'permissions[]', $aPermissions)
                            ->class('select2')
                            ->multiple()
                            ->select(array_keys($oItem->permissions)) !!}
                    @else
                        {!! BootForm::select('Permissions', 'permissions[]', $aPermissions)
                            ->class('select2')
                            ->multiple() !!}
                    @endif
                    
                    @if(isset($oItem))
                        {!! BootForm::select('Utilisateurs', 'users[]', $aUsers)
                            ->class('select2')
                            ->multiple()
                            ->select($oItem->users->pluck('id')->toArray()) !!}
                    @else
                        {!! BootForm::select('Utilisateurs', 'users[]', $aUsers)
                            ->class('select2')
                            ->multiple() !!}
                    @endif

                    {!! BootForm::submit('Envoyer', 'btn-primary')->addClass('pull-right') !!}

                    {!! BootForm::close() !!}
                </div>
            </div>
        </div>
        <a href="javascript:history.back()" class="btn btn-primary">
                <span class="glyphicon glyphicon-circle-arrow-left"></span> Retour
        </a>
    </div>
@stop

@section('JS')

    <!-- Select 2 -->
    {!! Html::script('bower_components/select2/dist/js/select2.full.min.js') !!}
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
        } );
    </script>
                
@stop