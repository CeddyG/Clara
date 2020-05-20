<!DOCTYPE html>

<html>
  <head>
    <meta charset="UTF-8">
    <title>{{ $sPageTitle ?? 'AdminLTE Dashboard' }}</title>
      
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
      
    <!-- Bootstrap 3.3.7 -->
    {!! Html::style('bower_components/bootstrap/dist/css/bootstrap.min.css') !!}
    <!-- Font Awesome Icons -->
    {!! Html::style('bower_components/font-awesome/css/font-awesome.min.css') !!}
    <!-- Ionicons -->
    {!! Html::style('bower_components/Ionicons/css/ionicons.min.css') !!}
    
    @yield('CSS')
    
    <!-- Theme style -->
    {!! Html::style('adminlte/css/AdminLTE.min.css') !!}
    {!! Html::style('adminlte/css/skins/skin-blue.min.css') !!}
	
	<!-- Google Font -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
      
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
                    {{ $sPageTitle ?? 'Page Title' }}
                    <small>{{ $sPageDescription ?? null }}</small>
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
                    {!! Alert::danger(session('error'))->close() !!}
                @endif
                @if (session('success'))
                    {!! Alert::success(session('success'))->close() !!}
                @endif
                @if (session('info'))
                    {!! Alert::info(session('info'))->close() !!}
                @endif
                @if (session('warning'))
                    {!! Alert::warning(session('warning'))->close() !!}
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

    <!-- jQuery 3 -->
    {!! Html::script('bower_components/jquery/dist/jquery.min.js') !!}
    <!-- Bootstrap 3.3.7 JS -->
    {!! Html::script('bower_components/bootstrap/dist/js/bootstrap.min.js') !!}
    <!-- AdminLTE App -->
    {!! Html::script('adminlte/js/adminlte.min.js') !!}
    
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
        
        $('.sidebar-menu .caret').each(function(){
            $(this).removeClass('caret');
            $(this).addClass('pull-right-container');
            $(this).html('<i class="fa fa-angle-left pull-right"></i>');
        });
    </script>
    
    @yield('JS')
    
  </body>
</html>