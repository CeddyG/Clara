<!DOCTYPE html>

<html>
  <head>
    <meta charset="UTF-8">
    <title>{{ $sPageTitle or 'AdminLTE Dashboard' }}</title>
      
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
      
    <!-- Bootstrap 3.3.2 -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    
    @yield('CSS')
    
    <!-- Theme style -->
    <link href="{{ asset('adminlte/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('adminlte/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />
      
  </head>
  <body class="skin-blue">
    <div class="wrapper">

        @if(Sentinel::check())
            <!-- Header -->
            @include('admin/header')

            <!-- Sidebar -->
            @include('admin/sidebar')
        @endif

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    {{ $sPageTitle or 'Page Title' }}
                    <small>{{ $sPageDescription or null }}</small>
                </h1>
                <!-- You can dynamically generate breadcrumbs here -->
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                    <li class="active">Here</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                @if (session('error'))
                    <div class="alert alert-error">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif
                
                <!-- Your Page Content Here -->
                @yield('content')
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        @if(Sentinel::check())
            <!-- Footer -->
            @include('admin/footer')
        @endif

    </div><!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->

    <!-- jQuery 2.2.3 -->
    <script src="{{ asset ('adminlte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset ('bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset ('adminlte/js/app.min.js') }}" type="text/javascript"></script>
    
    <!-- Optionally, you can add Slimscroll and FastClick plugins. 
          Both of these plugins are recommended to enhance the 
          user experience -->
    <script type="text/javascript">
    
        $('.sidebar-menu').each(function(){
            $(this).removeClass('nav');
        });
    
        $('.link-blank').each(function(){
            $(this).find('a').attr('target', '_blank');
        });
        
        $('.sidebar-menu > li.dropdown').each(function(){
            $(this).removeClass('dropdown');
            $(this).addClass('treeview');
            
            $(this).find( "a" ).removeClass();
            $(this).find( "ul" ).removeClass();
            $(this).find( "ul" ).addClass('treeview-menu');
        });
    </script>
    
    @yield('JS')
    
  </body>
</html>