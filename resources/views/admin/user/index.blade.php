@extends('admin/dashboard')

@section('content')

<div class="col-md-6">
    @if(session()->has('ok'))

        <div class="alert alert-success alert-dismissible">{!! session('ok') !!}</div>

    @endif
        
    <!-- TABLE: LATEST ORDERS -->
    <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Liste de</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="tab-admin" class="table no-margin table-bordered table-hover">
              <thead>
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>E-mail</th>
                <th></th>
                <th></th>
              </tr>
              </thead>
              <tbody>
                @foreach ($oItems as $oItem)
                    <tr>
                        <td>{!! $oItem->id !!}</td>
                        <td>{!! $oItem->first_name.' '.$oItem->last_name!!}</td>
                        <td>{!! $oItem->email!!}</td>
                        <td>
                            {!! Button::warning('Modifier')->asLinkTo(route('admin.user.edit', [$oItem->id]))->extraSmall()->block() !!}
                        </td>
                        <td>
                            {!! BootForm::open()->action( route('admin.user.destroy', $oItem) )->attribute('onsubmit', 'return confirm(\'Vraiment supprimer cet utilisateur ?\')')->delete() !!}
                                {!! BootForm::submit('Supprimer', 'btn-danger')->addClass('btn-block btn-xs') !!}
                            {!! BootForm::close() !!}
                        </td>
                    </tr>
                @endforeach
              </tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            {!! Button::info('Ajouter')->asLinkTo(route('admin.user.create'))->small() !!}
        </div>
        <!-- /.box-footer -->
    </div>
    <!-- /.box -->
</div>
@endsection
