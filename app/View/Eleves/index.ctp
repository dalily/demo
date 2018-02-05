 <?php 
$this->viewVars['title_for_layout'] = __('Elèves');
?>

<script>

<?php  $this->Html->scriptStart(array('inline' => false)); ?>
var eleveCrud = {
        datagrid : {},
        init : function(){
             eleveCrud.datagrid = $('#eleve_datagrid').DataTable({
                "processing": true,
                "serverSide": true,
                "language": {
                    "lengthMenu": "",
                    "processing": '<div class = "loading-message"><span>Chargement...</span></div>',
                    "sInfo": "",
                    "sInfoEmpty": "",
                    "zeroRecords" : 'Aucun enregistrement trouvé' 
                },
                "ajax": {
                    url : '<?php echo $this->Html->url(array('action' => 'get_datagrid_data', 'ext' => 'json')); ?>',
                    type: "POST",
                    data : function ( d ) {
                        var value = $('#EleveFilter').find('input[type = search]').val();
                        var column = $('#EleveFilter').find('.hidden').val();
                        
                        if(column && value)
                        {
                            d['filter'] = {};
                            d['filter'][column] = value;
                        }   
                    }
                },
                "sort": true,
                "filter": false,
                "columns": [
                    {
                        title: '<?php echo __('Identifiant'); ?>',
                        data: 'Eleve.id',
                        sortable: true,
                    },
                    {
                        title: '<?php echo __('Prénom'); ?>',
                        data: 'Eleve.prenom',
                        sortable: true
                    },
                    {
                        title: '<?php echo __('Nom'); ?>',
                        data: 'Eleve.nom',
                        sortable: true
                    },
                    {
                        title: '<?php echo __('Date de naissance'); ?>',
                        data: 'Eleve.birthday',
                        sortable: true
                    },
                {
                title:  '<?php echo __('Actions'); ?>',
                data: null,
                sortable: false
            }],
                "columnDefs": [{
                    "targets": [4],
                    "width" : "230px",
                    render: function (e, type, data, meta)
                    {   
                        var actions = [{
                            'value': 'Détail',
                            'attr': {
                                'icon': 'folder-open-o',
                                'class': "btn btn-xs btn-primary btn-open",
                                'action-id': data.Eleve.id
                            }
                        }];

                        actions.push({
                            'value': 'Modifier',
                            'attr': {
                                'icon': 'pencil',
                                'class': "btn btn-xs btn-primary btn-edit",
                                'action-id': data.Eleve.id
                            }
                        }); 

                        actions.push({
                            'value': 'Supprimer',
                            'attr': {
                                'icon': 'remove',
                                'class': "btn btn-xs btn-danger btn-delete",
                                'action-id': data.Eleve.id
                            }
                        }); 
                        return createButtonGroup(actions);
                    }
                }],
            });         
        },
        showDetail : function (elm) {
            var tr = $(elm).closest('tr');
            var row = eleveCrud.datagrid.row( tr );
     
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( eleveCrud.detail(row.data()) ).show();
                tr.addClass('shown');
            }
        },
        detail : function(d){

            return '<table id = "eleve_row_detail" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                        
                '<tr>'+
                '<td><?php echo __('Identifiant'); ?></td>'+
                    '<td>'+d.Eleve.id+'</td>'+
                '</tr>'+            
                '<tr>'+
                '<td><?php echo __('Prénom'); ?></td>'+
                    '<td>'+d.Eleve.prenom+'</td>'+
                '</tr>'+            
                '<tr>'+
                '<td><?php echo __('Nom'); ?></td>'+
                    '<td>'+d.Eleve.nom+'</td>'+
                '</tr>'+            
                '<tr>'+
                '<td><?php echo __('Date de naissance'); ?></td>'+
                    '<td>'+d.Eleve.birthday+'</td>'+
                '</tr>'+
                '</table>'; 
        },
        addRow : function(postData){
            var formURL = $('#add_eleve_form').attr("action");
            $('#EleveAddDialog').trigger('dialogLoader', 'show');
            $.ajax({
                url : formURL,
                type: "POST",
                data : postData,
                success:function(response, textStatus, jqXHR) {
                    if(response.result == 'success') {
                        eleveCrud.datagrid.row.add(response.record).draw();
                        toastr.success(response.message);
                        $('#add_eleve_form').find('input, select').val('');
                    } else {
                        toastr.error(response.message); 
                    }
                    $('#EleveAddDialog').trigger('dialogLoader', 'hide');
                    $('#EleveAddDialog').modal('hide'); 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#EleveAddDialog').trigger('dialogLoader', 'hide');
                    toastr.error("<?php echo __('Une erreur est survenue. réessayer svp ultérieurement!'); ?>");
                }
            });
            
        },
        deleteRow : function(id, tr){

            $('#eleve_datagrid').trigger('loader', 'show');
            $.ajax({
                url : '<?php  echo Router::url(array('action' => 'delete', 'ext' => 'json'));?>',
                type: "POST",
                data : {id : id},
                success:function(response, textStatus, jqXHR) {
                    if(response.result == 'success') {
                        eleveCrud.datagrid.row(tr).remove().draw( false );
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                    $('#eleve_datagrid').trigger('loader', 'hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#eleve_datagrid').trigger('loader', 'hide');
                    toastr.error("<?php echo __('Une erreur est survenue. réessayer svp ultérieurement!'); ?>");
                }
            });
        },
        updateRow : function(data){
            var formURL = $('#edit_eleve_form').attr("action");
            $('#EleveEditDialog').trigger('dialogLoader', 'show')
            $.ajax({
                url : formURL,
                type: "POST",
                data : data,
                success:function(response, textStatus, jqXHR) {
                     
                    if(response.result == 'success') {
                        var tr = $('[action-id = '+response.record.Eleve.id+']').closest('tr');
                        eleveCrud.datagrid.row(tr).data( response.record ).draw();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message); 
                    }
                     $('#EleveEditDialog').trigger('dialogLoader', 'hide'); 
                    $('#EleveEditDialog').modal('hide'); 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#EleveEditDialog').trigger('dialogLoader', 'hide');
                    toastr.error("<?php echo __('Une erreur est survenue. réessayer svp ultérieurement!'); ?>");
                }
            });
        }
    }

    jQuery(document).ready(function() {
        eleveCrud.init();

        $('#eleve_datagrid tbody').on('click', '.btn-open', function() {
            eleveCrud.showDetail(this)
        });

        $('.eleves').on('click', '.btn-delete', function(e) {
            var id = $(this).attr("action-id");
            var tr = $(this).closest("tr");
            
            if(confirm("<?php echo __("êtes-vous sûr de vouloir supprimer l´élève"); ?>")){
                eleveCrud.deleteRow(id, tr);
            }
            
            e.preventDefault();

            return false;
        });

        $('#add_eleve_form').submit(function(e) {
            var postData = $(this).serializeArray();
            eleveCrud.addRow(postData);
            e.preventDefault();

            return false;
        });

        $('#edit_eleve_form').submit(function(e) {
            var postData = $(this).serializeArray();
            eleveCrud.updateRow(postData);
            e.preventDefault();

            return false;
        });

        $(document).on('click', '.btn-edit', function(event) {
            $('#edit_eleve_form').find('input, select').val('');
            var data = eleveCrud.datagrid.row($(this).closest('tr')).data();

            $('#edit_eleve_form input, #edit_eleve_form select').each(function(){
                
                if($(this).attr('id')) {    
                    regex = /\[([^\]]*)]/g;
                    keys = [];
                    
                    while (m = regex.exec($(this).attr('name'))) {
                      keys.push(m[1]);
                    }

                    if(data.hasOwnProperty(keys[0]) && data[keys[0]][keys[1]]) {
                        $(this).val(data[keys[0]][keys[1]]);
                    }
                }
            });

            $('#EleveEditDialog').modal('show');
            
            event.preventDefault();
            return false;
        });

        $('#EleveFilter').on('click', 'a', function (e) {
            var field_name =  $(this).parent().attr('data-value')
            var field_label = $(this).text();
            $(this).closest('.datagrid-search').find('.hidden').val(field_name);
            $(this).closest('.datagrid-search').find('.selected-label').text(' ' +field_label);
            $(this).closest('.datagrid-search').find('input[type = search]').val("");
            $(this).closest('.datagrid-search').find('input[type = search]').attr('placeholder', 'Chercher par '+field_label);
        });

        $('#EleveFilter .search').on('click', '.btn', function (e) {
            eleveCrud.datagrid.ajax.reload();
        });

        $('#EleveEditDialog').on('hidden.bs.modal', function (e) {
            $('#edit_eleve_form').clearForm();
        });

        $('#EleveAddDialog').on('hidden.bs.modal', function (e) {
            $('#add_eleve_form').clearForm();
        });

        $.fn.clearForm = function() {
            
            return this.each(function() {
                var type = this.type, tag = this.tagName.toLowerCase();
                
                if (tag == 'form')
                    return $(':input',this).clearForm();
                if (type == 'text' || type == 'password' || tag == 'textarea')
                    this.value = '';
                else if (type == 'checkbox' || type == 'radio')
                    this.checked = false;
                else if (tag == 'select')
                    this.selectedIndex = -1;
            });
        };

        $(document).on('dialogLoader', '.modal', function(e, action){

            if(action == 'hide') {
                $(this).find('.loading-message').hide();
            } else {
                $(this).find('.loading-message').show();
            }
        }); 
    });

<?php $this->Html->scriptEnd(); ?></script>

<div class="eleves index">
    <div class="datagrid" id="eleve_datagrid_container">
        <div class="datagrid-toolbar">
            <div class="col-xs-12 col-sm-6 col-md-8 no-padding">
                <a htref = "#" class = "btn btn-primary" data-toggle = "modal" data-target = "#EleveAddDialog" >
                    <?php echo __("Nouvel Élève"); ?>               
                </a>
            </div>
            <div class="col-xs-6 col-md-4 no-padding">
                <div class="datagrid-search" id = "EleveFilter">
                    <div class="input-group">
                        <div class="input-group-btn selectlist" data-resize="auto" data-initialize="selectlist">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="selected-label">Id</span>
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                                
                                <li data-value="Eleve.id">  
                                    <a href="#">Identifiant</a>
                                </li>                                           
                                <li data-value="Eleve.prenom">  
                                    <a href="#">Prénom</a>
                                </li>                                           
                                <li data-value="Eleve.nom"> 
                                    <a href="#">Nom</a>
                                </li>                                           
                                <li data-value="Eleve.birthday">    
                                    <a href="#">Date de naissance</a>
                                </li>                                               
                            </ul>
                            <input class="hidden hidden-field" name="column" readonly="readonly" aria-hidden="true" type="text" value = "Eleve.id">
                        </div>
                        <div class="search input-group">
                            <input type="search" class="form-control" placeholder="<?php  echo __('Chercher par Identifiant');  ?>"/>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <span class="glyphicon glyphicon-search"></span>
                                    <span class="sr-only">
                                        <?php  echo __('Chercher');  ?>                                 
                                    </span>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class = "clear"></div>
        </div>
        <table id="eleve_datagrid" class="display table-bordered"></table>
    </div>
</div>

<div class="modal fade" id="EleveAddDialog" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="EleveEdition" data-backdrop = "static">
    <?php  
        echo $this->Form->create('Eleve',
            array('url' => array('action' => 'add', 'ext' => 'json'), 
            'id' => 'add_eleve_form')
        );
    ?>  
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">
                        <?php  echo __('Fermer');  ?>
                    </span>
                </button>
                <h4 class="modal-title">
                    <?php  echo __('Ajouter un Élève');  ?>             
                </h4>
            </div>

            <div class="modal-body">
            <?php
                $this->Form->inputDefaults(array('label' => false, 'class' => 'span10'));
                echo $this->Form->input('prenom', array(
                    'label' => __('Prénom'),
                    'id' => 'AddElevePrenom'
                ));
                echo $this->Form->input('nom', array(
                    'label' => __('Nom'),
                    'id' => 'AddEleveNom'
                ));
                echo $this->Form->input('birthday', array(
                    'label' => __('Date de naissance'),
                    'id' => 'AddEleveBirthday',
                    'type' => 'text',
                    'class' => 'datepicker',
                    'data-date-end-date' => '-1d'
                ));
            ?>
            </div>
            <div class="loading-message" >Chargement...</div>
            <div class="modal-footer">
                <?php 
                    echo $this->Html->link(__('Annuler'), '#', 
                        array('class' => 'btn btn-danger', 'data-dismiss' => 'modal')
                    ); 
                    echo $this->Form->button(__('Valider'), array('class' => 'btn btn-primary'));
                ?>          
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
<?php echo $this->Form->end(); ?>
</div><!-- /.modal -->

<div class="modal fade" id="EleveEditDialog" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="EleveEdition" backdrop = "static">
    <?php  
        echo $this->Form->create('Eleve',
            array('url' => array('action' => 'edit', 'ext' => 'json'), 
            'id' => 'edit_eleve_form')
        );
    ?> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">
                        <?php  echo __('Fermer');  ?>                   
                    </span>
                </button>
                <h4 class="modal-title">
                    <?php  echo __("Edition d'un Élève");  ?>               
                </h4>
            </div>
            <div class="modal-body">
            <?php
                echo $this->Form->input('id');
                $this->Form->inputDefaults(array('label' => false, 'class' => 'span10'));
                echo $this->Form->input('prenom', array(
                    'label' => __('Prenom'),
                    'id' => 'EditElevePrenom'
                ));
                echo $this->Form->input('nom', array(
                    'label' => __('Nom'),
                    'id' => 'EditEleveNom'
                ));
                echo $this->Form->input('birthday', array(
                    'label' => __('Date de naissance'),
                    'id' => 'EditEleveBirthday',
                    'type' => 'text',
                    'class' => 'datepicker',
                    'data-date-end-date' => '-1d'
                ));
            ?>
            </div>
            <div class="loading-message" >Chargement...</div>
            <div class="modal-footer">
                <?php 
                    echo $this->Html->link(__('Annuler'), '#', 
                        array('class' => 'btn btn-danger', 'data-dismiss' => 'modal')
                    ); 
                    echo $this->Form->button(__('Valider'), array('class' => 'btn btn-primary'));
                ?>          
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
<?php echo $this->Form->end(); ?>
</div><!-- /.modal -->