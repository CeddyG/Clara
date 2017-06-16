@extends('admin/dashboard')

@section('CSS')
    <!-- Select 2 -->
    {!! Html::style('/adminlte/plugins/select2/select2.min.css') !!}
    <style>
        .select2
        {
            width: 100% !important
        }
        
        .input-group
        {
            width: 100% !important
        }
        
        .input-group-addon:hover
        {
            color: black;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <br>
            <div class="box box-info">	
                <div class="box-header with-border">
                    @if(isset($oItem))
                        <h3 class="box-title">Modification</h3>
                    @else
                        <h3 class="box-title">Ajouter</h3>
                    @endif
                </div>
                <div class="box-body"> 
                    <div class="col-sm-12">
                        @if(isset($oItem))
                            {!! BootForm::open()->action( route('admin.dataflow.update', $oItem->id_dataflow) )->put() !!}
                            {!! BootForm::bind($oItem) !!}
                        @else
                            {!! BootForm::open()->action( url('admin/dataflow') )->post() !!}
                        @endif
                        
                        {!! BootForm::text(trans('dataflow.name'), 'name') !!}
                        
                        @if(isset($oItem))
                            {!! BootForm::select(trans('dataflow.repository'), 'repository')
                                ->class('select2')
                                ->options($aRepositories)
                                ->select($oItem->repository) !!}
                            
                            {!! BootForm::text(trans('dataflow.separator_csv_column'), 
                                'separator_csv[]', 
                                str_replace(["\t", "\n", "\r"], ["\\t",  "\\n",  "\\r"], $oItem->separator_csv['colonne'])) !!}
                            {!! BootForm::text(trans('dataflow.separator_csv_line'), 
                                'separator_csv[]', 
                                str_replace(["\t", "\n", "\r"], ["\\t",  "\\n",  "\\r"], $oItem->separator_csv['ligne'])) !!}
                            {!! BootForm::text(trans('dataflow.separator_csv_text'), 
                                'separator_csv[]', 
                                str_replace(["\t", "\n", "\r"], ["\\t",  "\\n",  "\\r"], $oItem->separator_csv['texte'])) !!}
                        @else
                            {!! BootForm::select(trans('dataflow.repository'), 'repository')
                                ->class('select2')
                                ->options($aRepositories) !!}
                                
                            {!! BootForm::text(trans('dataflow.separator_csv_column'), 'separator_csv[]') !!}
                            {!! BootForm::text(trans('dataflow.separator_csv_line'), 'separator_csv[]') !!}
                            {!! BootForm::text(trans('dataflow.separator_csv_text'), 'separator_csv[]') !!}
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
    </div>
@stop

@section('JS')
    <!-- Select 2 -->
    {!! Html::script('/adminlte/plugins/select2/select2.full.min.js') !!}
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script> 

@stop