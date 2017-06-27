@extends('admin/dashboard')

@section('CSS')
    <!-- DataTables -->
    <link href="{{ asset("/adminlte/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
@stop

@section('content')

<div class="col-md-12">
    @if(session()->has('ok'))

        <div class="alert alert-success alert-dismissible">{!! session('ok') !!}</div>

    @endif
        
    <!-- TABLE: LATEST ORDERS -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Liste</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="tab-admin" class="table no-margin table-bordered table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Token</th>
                    <th>Repository</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            {!! Button::info('Ajouter')->asLinkTo(route('admin.dataflow.create'))->small() !!}
        </div>
        <!-- /.box-footer -->
    </div>
    <!-- /.box -->
</div>
@endsection

@section('JS')
    <script src="{{ asset ("/adminlte/plugins/datatables/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset ("/adminlte/plugins/datatables/dataTables.bootstrap.min.js") }}"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tab-admin').DataTable({
                serverSide: true,
                ajax: {
                    'url': '{{ route('admin.dataflow.index.ajax') }}'
                },
                columns: [
                    { 'data': 'id_dataflow' },
                    { 'data': 'name' },
                    { 'data': 'token' },
                    { 'data': 'repository' },
                    {
                        "data": "token",
                        "render": function ( data, type, row, meta ) {

                            var render = "{!! DropdownButton::normal('Export')
                            ->withContents([
                                ['url' => route('api.dataflow', ['format' => 'xml', 'token' => 'dummyToken']), 'label' => 'XML'],
                                ['url' => route('api.dataflow', ['format' => 'json', 'token' => 'dummyToken']), 'label' => 'JSON'],
                                ['url' => route('api.dataflow', ['format' => 'csv', 'token' => 'dummyToken']), 'label' => 'CSV'],
                                ['url' => route('api.dataflow', ['format' => 'xls', 'token' => 'dummyToken']), 'label' => 'XLS'],
                                ['url' => route('api.dataflow', ['format' => 'xlsx', 'token' => 'dummyToken']), 'label' => 'XLSX'],
                            ]) !!}";
                        
                            render = render.replace(/dummyToken/g, data);
                            render = render.replace(/<a/g, '<a target="_blank"');

                            return render;
                        }
                    },
                    {
                        "data": "id_dataflow",
                        "render": function ( data, type, row, meta ) {

                            var render = "{!! Button::warning('Modifier')->asLinkTo(route('admin.dataflow.edit', 'dummyId'))->extraSmall()->block()->render() !!}";
                            render = render.replace("dummyId", data);

                            return render;
                        }
                    },
                    {
                        "data": "id_dataflow",
                        "render": function ( data, type, row, meta ) {

                            var render = '{!! BootForm::open()->action( route("admin.dataflow.destroy", "dummyId") )->attribute("onsubmit", "return confirm(\'Vraiment supprimer cet objet ?\')")->delete() !!}'
                                +'{!! BootForm::submit("Supprimer", "btn-danger")->addClass("btn-block btn-xs") !!}'
                                +'{!! BootForm::close() !!}';
                            render = render.replace("dummyId", data);

                            return render;
                        }
                    }
                ], 
                aoColumnDefs: [
                    {
                        bSortable: false,
                        aTargets: [-1, -2, -3]
                    }
                ],
                "language": {
                    "sProcessing":     "Traitement en cours...",
                    "sSearch":         "Rechercher&nbsp;:",
                    "sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
                    "sInfo":           "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                    "sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
                    "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                    "sInfoPostFix":    "",
                    "sLoadingRecords": "Chargement en cours...",
                    "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
                    "oPaginate": {
                        "sFirst":      "Premier",
                        "sPrevious":   "Pr&eacute;c&eacute;dent",
                        "sNext":       "Suivant",
                        "sLast":       "Dernier"
                    },
                    "oAria": {
                        "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                        "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
                    }
                }
            });
        } );
    </script>
@endsection
