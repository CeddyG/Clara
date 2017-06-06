@extends('admin.dashboard')

@section('CSS')
    <!-- iCheck -->
    <link href="{{ asset("/adminlte/plugins/iCheck/all.css")}}" rel="stylesheet" type="text/css" />
    
    <!-- Select 2 -->
    {!! Html::style('/adminlte/plugins/select2/select2.min.css') !!}
    
    <style>
        .select2
        {
            width: 100% !important
        }
        
        .wrapper-icheckbox
        {
            margin-right: 5px
        }
        
        .table-name
        {
            line-height: 90px;
            margin-left: 20px
        }

        .label-table-name
        {
            cursor: pointer;
        }
        
        .block-form .box-title
        {
            border-bottom: 1px solid #f4f4f4;
            padding-bottom: 10px;
            text-align: center;
        }
        
        .no-relation
        {
            text-align: center;
            margin-top: 60px;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label>Go to</label>
                <select autocomplete="off" class="form-control" name="" id="goto-table">
                    <option selected value="">Choose a table</option>
                    @foreach($objects as $table => $v)
                        <option value="box-{{ $table }}">{{ $table }}</option>
                    @endforeach
                </select>

            </div>
        </div>
    </div>
    {!! BootForm::open()->action( route('admin.entity.store') )->post() !!}
    @foreach($objects as $table => $v)
    <div class="row">
        <div class="col-sm-8">
            <br>
            <div id="box-{{ $table }}" class="box box-info">
                <div class="box-header with-border">
                    <h2 class="box-title">{{ $table }}</h2>
                </div>
                <div class="box-body"> 
                    
                    <div class="col-sm-4 block-form">  
                        <h4 class="box-title">Files</h4>
                        {!! BootForm::checkbox('All', '')
                            ->class('minimal all-check') !!}
                            
                        {!! BootForm::checkbox('Controller', 'table['.$table.'][controller]')
                            ->class('minimal') !!}

                        {!! BootForm::checkbox('Model', 'table['.$table.'][model]')
                            ->class('minimal') !!}

                        {!! BootForm::checkbox('Repository', 'table['.$table.'][repository]')
                            ->class('minimal') !!}

                        {!! BootForm::checkbox('Request', 'table['.$table.'][request]')
                            ->class('minimal') !!}

                        {!! BootForm::checkbox('Index view', 'table['.$table.'][index]')
                            ->class('minimal') !!}

                        {!! BootForm::checkbox('Form view', 'table['.$table.'][form]')
                            ->class('minimal') !!}

                        {!! BootForm::checkbox('Traduction', 'table['.$table.'][traduction]')
                            ->class('minimal') !!}
                    </div>
                    
                    <div class="col-sm-8 block-form">   
                        <h4 class="box-title">Relations</h4>
                        @if(count($v) > 0)
                            <table class="table table-condensed table-bordered">
                                <tr>
                                    <th>Table</th>
                                    <th>Relations</th>
                                </tr>
                                @foreach($v as $relation => $relats)

                                    <tr>
                                        <td>
                                            <big><b>{{ $relation }}</b></big>
                                        </td>
                                        <td>
                                            @if(count($relats) > 0)
                                                <select class="select2" name="related-{{ $table }}[]">
                                                    <option value="0">Relation standard</option>
                                                    @foreach($relats as $related => $value)

                                                        <option value="{{ $value }}">Table de liaison avec {{ $related }}</option>

                                                    @endforeach
                                                </select>
                                            @else
                                                Relation standard
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach
                            </table>
                        @else
                        <div class="row no-relation"><b>No relations</b></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
                        
    {!! BootForm::submit('Save', 'btn-primary') !!}

    {!! BootForm::close() !!}
@stop

@section('JS')
    <script src="{{ asset ("/adminlte/plugins/iCheck/icheck.min.js") }}"></script>
    <!-- Select 2 -->
    {!! Html::script('/adminlte/plugins/select2/select2.full.min.js') !!}
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue wrapper-icheckbox',
                radioClass: 'iradio_minimal-blue'
            });
            
            $('.select2').select2();
            
            $('.all-check').on('ifChanged', function () {
                if ($(this).is(':checked'))
                {
                    $(this).parents('.block-form').find('.minimal').iCheck('check');
                }
                else
                {
                    $(this).parents('.block-form').find('.minimal').iCheck('uncheck');              
                }
            });
            
            $('.minimal').on('ifChanged', function () {
                if (!$(this).is(':checked'))
                {
                    var oCheckAll = $(this).parents('.block-form').find('.all-check');
                    oCheckAll.prop('checked', false);
                    oCheckAll.iCheck('update');
                }
            });
                    
        } );

        $('#goto-table').on('change', function(evt){
            var ref = $(this).val();

            if(ref !== ''){
                $('html, body').animate({
                    scrollTop: $('#'+ref).offset().top + 'px'
                }, 200);
            }
        })
    </script>
@endsection