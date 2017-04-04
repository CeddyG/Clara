<?php

namespace App\Console\Commands\Services;

/**
 * Description of SimpleViewService
 *
 * @author alexandre
 */
class ContentManagerViewService {
    
    /*
     * Liste des champs pour le formulaire en fonction du type
     */
    static function getFieldText($table, $name)
    {
        $field = '{!! BootForm::text(trans(\''. $table .'.'. $name .'\'), \''. $name .'\') !!}
                  ';
        
        return $field;
    }
    
    static function getFieldTextArea($table, $name)
    {
        $field = '{!! BootForm::textarea(trans(\''. $table .'.'. $name .'\'), \''. $name .'\')->addClass(\'ckeditor\') !!}
                  ';
        
        return $field;
    }
    
    static function getFieldDate($table, $name)
    {
        $field = '{!! BootForm::inputGroup(trans(\''. $table .'.'. $name .'\'), \''. $name .'\')->type(\'date\')->beforeAddon(\'<i class="fa fa-calendar"></i>\') !!}
                  {!! BootForm::hidden(\''. $name .'\')->id(\'alt_'. $name .'\') !!}                
                  ';
        
        return $field;
    }
    
    static function getFieldCheck($table, $name)
    {
        $field = '<div class="form-group {!! $errors->has(\''. $name .'\') ? \'has-error\' : \'\' !!}">
                        <div class="checkbox">
                            <label>
                            {!! Form::checkbox(\''. $name .'\', \'true\') !!}
                            {!! trans(\''. $table .'.'. $name .'\') !!}
                            </label>
                        </div>
                  </div>
                
                  ';
        
        return $field;
    }
    
    static function getFieldSelect($name, $table, $idFk)
    {
        $label = str_replace('_', ' ', ucfirst($table));
        
        $field = '<div class="form-group">
                        {!! Form::label(\''. $name .'\', \''. $label .'\') !!}
                            
                        @if(isset($objet))
                            {!! Form::select(\''. $name .'\', $foreign[\''. $name .'\'], $objet->'. $name .'->lists(\''. $idFk .'\')->toArray(), [\'class\' => \'form-control select2 select2-hidden-accessible\',
                                                                    \'name\' => \''. $name .'[]\',
                                                                    \'multiple\' => \'\',
                                                                    \'tabindex\' => \'-1\',
                                                                    \'aria-hidden\' => \'true\',
                                                                    \'style\' => \'width: 100%;\'
                                                                ]) !!}
                        @else
                            {!! Form::select(\''. $name .'\', $foreign[\''. $name .'\'], null, [\'class\' => \'form-control select2 select2-hidden-accessible\',
                                                                    \'name\' => \''. $name .'[]\',
                                                                    \'multiple\' => \'\',
                                                                    \'tabindex\' => \'-1\',
                                                                    \'aria-hidden\' => \'true\',
                                                                    \'style\' => \'width: 100%;\'
                                                                ]) !!}
                        @endif
                    </div>
                
                  ';
        
        return $field;
    }
    
    static function getFieldFk($table, $col)
    {
        //On formatte le nom du champ du tableau qui va contenir la liste de la table étrangère
        $fkTabField = "";
        $fkFieldTmp = explode('_', $col['tableFk']);
        
        $i = 0;
        foreach($fkFieldTmp as $tmp)
        {
            $fkTabField .= ($i == 1) ? ucfirst($tmp) : $tmp;
            $i = 1;
        }
        
        $field = '{!! BootForm::select(trans(\''. $table .'.'. $col['field'] .'\'), \''. $col['field'] .'\')->options($foreign[\''. $fkTabField .'\']) !!}
                  ';
        
        return $field;
    }
    
    /*
     * Liste des code JS à intégrer en fonction des types de champ
     */
    static function getJsDatepicker()
    {
        $js = '{!! Html::script(\'/bower_components/adminLTE/plugins/datepicker/bootstrap-datepicker.js\') !!}
    {!! Html::script(\'/bower_components/adminLTE/plugins/datepicker/locales/bootstrap-datepicker.fr.js\') !!}
    
    <script type="text/javascript">
        $(document).ready(function() {
            var altField = "#alt_";
            $(\'input[type=date]\').datepicker({
                format: \'dd/mm/yyyy\',
                autoclose: true,
                todayHighlight: true,
                language: \'fr\'
            }).on(\'changeDate\', function(ev){
                if(ev.format() != "")
                {
                    var newdate = ev.format().split("/");
                    newdate = newdate[2] +\'-\'+ newdate[1] +\'-\'+ newdate[0];
                }
                else
                {
                    var newdate = null;
                }
                
                $(altField+$(this).attr(\'id\')).attr(\'value\', newdate);
            });
            
            $(\'input[type=date]\').each(function(){
                var val = $(this).val();
                
                if(val != "")
                {
                    var newdate = val.split("/");
                    newdate = newdate[2] +\'-\'+ newdate[1] +\'-\'+ newdate[0];
                    
                    $(altField+$(this).attr(\'id\')).attr(\'value\', newdate);
                }
            });
        } );
    </script>
                
    ';
        
        return $js;
    }
    
    static function getJsCkeditor()
    {
        $js = '{!! Html::script(\'https://cdn.ckeditor.com/4.4.3/standard/ckeditor.js\') !!}
    
    <script>
        $(function () {
          // Replace the <textarea id="editor1"> with a CKEditor
          // instance, using default configuration.
          CKEDITOR.replace(\'.ckeditor\');
        });
    </script>
                
    ';
        
        return $js;
    }
    
    static function getJsSelect()
    {
        $js = '<!-- Select 2 -->
    {!! Html::script(\'/bower_components/adminLTE/plugins/select2/select2.full.min.js\') !!}
    
    <script type="text/javascript">
        $(document).ready(function() {
            $(\'.select2\').select2({
                tags: true
            });
        } );
    </script>
                
    ';
        
        return $js;
    }
    
    /*
     * Idem pour le css
     */
    static function getCssDatepicker()
    {
        $css = '<!-- Datepicker -->
    {!! Html::style(\'/bower_components/adminLTE/plugins/datepicker/datepicker3.css\') !!}
    ';
        
        return $css;
    }
    
    static function getCssSelect()
    {
        $css = '<!-- Select 2 -->
    {!! Html::style(\'/bower_components/adminLTE/plugins/select2/select2.min.css\') !!}
    ';
        
        return $css;
    }
}
