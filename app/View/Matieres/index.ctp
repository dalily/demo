 <?php 
$this->viewVars['title_for_layout'] = __('Matieres');
?>

<script>

<?php  $this->Html->scriptStart(array('inline' => false)); ?>
var matiereCrud = {
        datagrid : {},
        init : function(){
             matiereCrud.datagrid = $('#matiere_datagrid').DataTable({
                "processing": true,
                "serverSide": true,
                "language": {
                    "lengthMenu": "",
                    "processing": '<div class = "loading-message"><span>Chargement...</span></div>',
                    "sInfo": "",
                    "sPrevious": "Précdent",
                    "sNext": "Suivant",
                    "sInfoEmpty": "",
                    "zeroRecords" : 'Aucun enregistrement trouvé' 
                },
                "ajax": {
                    url : '<?php echo $this->Html->url(array('action' => 'get_datagrid_data', 'ext' => 'json')); ?>',
                    type: "POST",
                    data : function ( d ) {
                        var value = $('#MatiereFilter').find('input[type = search]').val();
                        var column = $('#MatiereFilter').find('.hidden').val();
                        
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
                        data: 'Matiere.id',
                        sortable: true,
                    },
                    {
                        title: '<?php echo __('Nom'); ?>',
                        data: 'Matiere.nom',
                        sortable: true
                    },
                {
                    title:  '<?php echo __('Actions'); ?>',
                    data: null,
                    sortable: false
                }],
                "columnDefs": [{
                    "targets": [2],
                    "width" : "230px",
                    render: function (e, type, data, meta)
                    {   
                        var actions = [{
                            'value': 'Détails',
                            'attr': {
                                'icon': 'folder-open-o',
                                'class': "btn btn-xs btn-primary btn-open",
                                'action-id': data.Matiere.id
                            }
                        }];

                        actions.push({
                            'value': 'Modifier',
                            'attr': {
                                'icon': 'pencil',
                                'class': "btn btn-xs btn-primary btn-edit",
                                'action-id': data.Matiere.id
                            }
                        }); 

                        actions.push({
                            'value': 'Supprimer',
                            'attr': {
                                'icon': 'remove',
                                'class': "btn btn-xs btn-danger btn-delete",
                                'action-id': data.Matiere.id
                            }
                        }); 
                        return createButtonGroup(actions);
                    }
                }],
            });         
        },
        showDetail : function (elm) {
            var tr = $(elm).closest('tr');
            var row = matiereCrud.datagrid.row( tr );
     
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( matiereCrud.detail(row.data()) ).show();
                tr.addClass('shown');
            }
        },
        detail : function(d){

            return '<table id = "matiere_row_detail" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                        
                '<tr>'+
                '<td><?php echo __('Identifiant'); ?></td>'+
                    '<td>'+d.Matiere.id+'</td>'+
                '</tr>'+            
                '<tr>'+
                '<td><?php echo __('Nom'); ?></td>'+
                    '<td>'+d.Matiere.nom+'</td>'+
                '</tr>'+
                '</table>'; 
        },
        addRow : function(postData){
            var formURL = $('#add_matiere_form').attr("action");
            $('#MatiereAddDialog').trigger('dialogLoader', 'show');
            $.ajax(
            {
                url : formURL,
                type: "POST",
                data : postData,
                success:function(response, textStatus, jqXHR) {
                    if(response.result == 'success') {
                        matiereCrud.datagrid.row.add(response.record).draw();
                        toastr.success(response.message);
                        $('#add_matiere_form').find('input, select').val('');
                    } else {
                        toastr.error(response.message); 
                    }
                    $('#MatiereAddDialog').trigger('dialogLoader', 'hide');
                    $('#MatiereAddDialog').modal('hide'); 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#MatiereAddDialog').trigger('dialogLoader', 'hide');
                    toastr.error("<?php echo __('An error occured please try again!'); ?>");
                }
            });
            
        },
        deleteRow : function(id, tr){
            $('#matiere_datagrid').trigger('loader', 'show');
            $.ajax({
                url : '<?php  echo Router::url(array('action' => 'delete', 'ext' => 'json'));?>',
                type: "POST",
                data : {id : id},
                success:function(response, textStatus, jqXHR) {
                    if(response.result == 'success') {
                        matiereCrud.datagrid.row(tr).remove().draw( false );
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                    $('#matiere_datagrid').trigger('loader', 'hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#matiere_datagrid').trigger('loader', 'hide');
                    toastr.error("<?php echo __('An error occured please try again!'); ?>");
                }
            });
        },
        updateRow : function(data){
            var formURL = $('#edit_matiere_form').attr("action");
            $('#MatiereEditDialog').trigger('dialogLoader', 'show')
            $.ajax({
                url : formURL,
                type: "POST",
                data : data,
                success:function(response, textStatus, jqXHR) {
                     
                    if(response.result == 'success') {
                        var tr = $('[action-id = '+response.record.Matiere.id+']').closest('tr');
                        matiereCrud.datagrid.row(tr).data( response.record ).draw();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message); 
                    }
                     $('#MatiereEditDialog').trigger('dialogLoader', 'hide'); 
                    $('#MatiereEditDialog').modal('hide'); 
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#MatiereEditDialog').trigger('dialogLoader', 'hide');
                    toastr.error("<?php echo __('An error occured please try again!'); ?>");
                }
            });
        }
    }

    jQuery(document).ready(function() {
        matiereCrud.init();

        $('#matiere_datagrid tbody').on('click', '.btn-open', function(){
            matiereCrud.showDetail(this)
        });

        $('.matieres').on('click', '.btn-delete', function(e) {
            var id = $(this).attr("action-id");
            var tr = $(this).closest("tr");
            
            if(confirm("<?php echo __('Are you sure'); ?>")) {
                matiereCrud.deleteRow(id, tr);
            }
            
            e.preventDefault();

            return false;
        });

        $('#add_matiere_form').submit(function(e) {
            var postData = $(this).serializeArray();
            matiereCrud.addRow(postData);
            e.preventDefault();

            return false;
        });

        $('#edit_matiere_form').submit(function(e){
            var postData = $(this).serializeArray();
            matiereCrud.updateRow(postData);
            e.preventDefault();

            return false;
        });

        $(document).on('click', '.btn-edit', function(event){
            $('#edit_matiere_form').find('input, select').val('');
            var data = matiereCrud.datagrid.row($(this).closest('tr')).data();

            $('#edit_matiere_form input, #edit_matiere_form select').each(function(){
                
                if($(this).attr('id')) {    
                    regex = /\[([^\]]*)]/g;
                    keys = [];
                    
                    while (m = regex.exec($(this).attr('name'))) {
                      keys.push(m[1]);
                    }

                    if(data.hasOwnProperty(keys[0]) && data[keys[0]][keys[1]]){
                        $(this).val(data[keys[0]][keys[1]]);
                    }
                }
            });

            $('#MatiereEditDialog').modal('show');
            
            event.preventDefault();
            return false;
        });

        $('#MatiereFilter').on('click', 'a', function (e) {
            var field_name =  $(this).parent().attr('data-value')
            var field_label = $(this).text();
            $(this).closest('.datagrid-search').find('.hidden').val(field_name);
            $(this).closest('.datagrid-search').find('.selected-label').text(' ' +field_label);
            $(this).closest('.datagrid-search').find('input[type = search]').val("");
            $(this).closest('.datagrid-search').find('input[type = search]').attr('placeholder', 'Chrcher par '+field_label);
        });

        $('#MatiereFilter .search').on('click', '.btn', function (e) {
            matiereCrud.datagrid.ajax.reload();
        });

        $('#MatiereEditDialog').on('hidden.bs.modal', function (e) {
            $('#edit_matiere_form').clearForm();
        });

        $('#MatiereAddDialog').on('hidden.bs.modal', function (e) {
            $('#add_matiere_form').clearForm();
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

<div class="matieres index">
    <div class="datagrid" id="matiere_datagrid_container">
        <div class="datagrid-toolbar">
            <div class="col-xs-12 col-sm-6 col-md-8 no-padding">
                <a htref = "#" class = "btn btn-primary" data-toggle = "modal" data-target = "#MatiereAddDialog" >
                    <?php echo __("Nouvelle Matiere"); ?>                
                </a>
            </div>
            <div class="col-xs-6 col-md-4 no-padding">
                <div class="datagrid-search" id = "MatiereFilter">
                    <div class="input-group">
                        <div class="input-group-btn selectlist" data-resize="auto" data-initialize="selectlist">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="selected-label">Identifiant</span>
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                                
                                <li data-value="Matiere.id">    
                                    <a href="#">Identifiant</a>
                                </li>                                           
                                <li data-value="Matiere.nom">   
                                    <a href="#">Nom</a>
                                </li>                                               
                            </ul>
                            <input class="hidden hidden-field" name="column" readonly="readonly" aria-hidden="true" type="text" value = "Matiere.id">
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
        <table id="matiere_datagrid" class="display table-bordered"></table>
    </div>
</div>

<div class="modal fade" id="MatiereAddDialog" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="MatiereEdition" data-backdrop = "static">
 
    <?php  
        echo $this->Form->create('Matiere', array(
            'url' => array('action' => 'add', 'ext' => 'json'), 
            'id' => 'add_matiere_form')
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
                    <?php  echo __('Ajout Matière');  ?>                
                </h4>
            </div>

            <div class="modal-body">
            <?php
                $this->Form->inputDefaults(array('label' => false, 'class' => 'span10'));
                echo $this->Form->input('nom', array(
                    'label' => __('Nom'),
                    'id' => 'AddMatiereNom'
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
<?php echo $this->Form->end(); ?></div><!-- /.modal -->

<div class="modal fade" id="MatiereEditDialog" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="MatiereEdition" backdrop = "static">
<?php  
    echo $this->Form->create('Matiere', array(
        'url' => array('action' => 'edit', 'ext' => 'json'), 
        'id' => 'edit_matiere_form'
    ));
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
                    <?php  echo __('Edition de la matière');  ?>             
                </h4>
            </div>
            <div class="modal-body">
            <?php
                echo $this->Form->input('id');
                $this->Form->inputDefaults(array('label' => false, 'class' => 'span10'));
                echo $this->Form->input('nom', array(
                    'label' => __('Nom'),
                    'id' => 'EditMatiereNom'
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

                ?>          </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
<?php echo $this->Form->end(); ?>
</div><!-- /.modal -->