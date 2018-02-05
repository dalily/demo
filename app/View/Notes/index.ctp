 <?php 
$this->viewVars['title_for_layout'] = __('Notes');
?>
<script>

<?php  $this->Html->scriptStart(array('inline' => false)); ?>
var noteCrud = {
        datagrid : {},
        init : function(){
             noteCrud.datagrid = $('#note_datagrid').DataTable({
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
                        var value = $('#NoteFilter').find('input[type = search]').val();
                        var column = $('#NoteFilter').find('.hidden').val();
                        
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
                        visible : false
                    },
                    {
                        title:  '<?php echo __('Élève'); ?>',
                        data: 'Eleve.fullname',
                        sortable: true
                    },
                    {
                        title:  '<?php echo __('Matière'); ?>',
                        data: 'Matiere.nom',
                        sortable: true
                    },
                    {
                        title:  '<?php echo __('Note'); ?>',
                        data: 'Note.note',
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
                            'value': 'Détails',
                            'attr': {
                                'icon': 'folder-open-o',
                                'class': "btn btn-xs btn-primary btn-open",
                                'action-id': data.Note.id
                            }
                        }];

                        actions.push({
                            'value': 'Modifier',
                            'attr': {
                                'icon': 'pencil',
                                'class': "btn btn-xs btn-primary btn-edit",
                                'action-id': data.Note.id
                            }
                        }); 

                        actions.push({
                            'value': 'Supprimer',
                            'attr': {
                                'icon': 'remove',
                                'class': "btn btn-xs btn-danger btn-delete",
                                'action-id': data.Note.id
                            }
                        }); 
                        return createButtonGroup(actions);
                    }
                }],
            });         
        },
        showDetail : function (elm) {
            var tr = $(elm).closest('tr');
            var row = noteCrud.datagrid.row( tr );
     
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( noteCrud.detail(row.data()) ).show();
                tr.addClass('shown');
            }
        },
        detail : function(d){

            return '<table id = "note_row_detail" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+           
                '<tr>'+
                '<td><?php echo __('Élève'); ?></td>'+
                    '<td>'+d.Eleve.fullname+'</td>'+
                '</tr>'+            
                '<tr>'+
                '<td><?php echo __('Matière'); ?></td>'+
                    '<td>'+d.Matiere.nom+'</td>'+
                '</tr>'+
                '<tr>'+
                '<td><?php echo __('Note'); ?></td>'+
                    '<td>'+d.Note.note+'</td>'+
                '</tr>'+
                '</table>'; 
        },
        addRow : function(postData){
            var formURL = $('#add_note_form').attr("action");
            $('#NoteAddDialog').trigger('dialogLoader', 'show');
            $.ajax(
            {
                url : formURL,
                type: "POST",
                data : postData,
                success:function(response, textStatus, jqXHR) 
                {
                    if(response.result == 'success')
                    {
                        noteCrud.datagrid.row.add(response.record).draw();
                        toastr.success(response.message);
                        $('#add_note_form').find('input, select').val('');
                    }
                    else
                    {
                        toastr.error(response.message); 
                    }
                    $('#NoteAddDialog').trigger('dialogLoader', 'hide');
                    $('#NoteAddDialog').modal('hide'); 
                },
                error: function(jqXHR, textStatus, errorThrown) 
                {
                    $('#NoteAddDialog').trigger('dialogLoader', 'hide');
                    toastr.error("<?php echo __('Une erreur est survenue. réessayer svp ultérieurement!'); ?>");
                }
            });
            
        },
        deleteRow : function(id, tr){

            $('#note_datagrid').trigger('loader', 'show');
            $.ajax(
            {
                url : '<?php  echo Router::url(array('action' => 'delete', 'ext' => 'json'));?>',
                type: "POST",
                data : {id : id},
                success:function(response, textStatus, jqXHR) 
                {
                    if(response.result == 'success')
                    {
                        noteCrud.datagrid.row(tr).remove().draw( false );
                        toastr.success(response.message);
                    }
                    else
                    {
                        toastr.error(response.message);
                    }
                    $('#note_datagrid').trigger('loader', 'hide');
                },
                error: function(jqXHR, textStatus, errorThrown) 
                {
                    $('#note_datagrid').trigger('loader', 'hide');
                    toastr.error("<?php echo __('Une erreur est survenue. réessayer svp ultérieurement!'); ?>");
                }
            });
        },
        updateRow : function(data){
            var formURL = $('#edit_note_form').attr("action");
            $('#NoteEditDialog').trigger('dialogLoader', 'show')
            $.ajax(
            {
                url : formURL,
                type: "POST",
                data : data,
                success:function(response, textStatus, jqXHR) 
                { 
                    if(response.result == 'success') {
                        var tr = $('[action-id = '+response.record.Note.id+']').closest('tr');
                        noteCrud.datagrid.row(tr).data( response.record ).draw();
                        toastr.success(response.message);
                    }
                    else
                    {
                        toastr.error(response.message); 
                    }
                     $('#NoteEditDialog').trigger('dialogLoader', 'hide'); 
                    $('#NoteEditDialog').modal('hide'); 
                },
                error: function(jqXHR, textStatus, errorThrown) 
                {
                    $('#NoteEditDialog').trigger('dialogLoader', 'hide');
                    toastr.error("<?php echo __('Une erreur est survenue. réessayer svp ultérieurement!'); ?>");
                }
            });
        }
    }

    jQuery(document).ready(function() {
        noteCrud.init();

        $('#note_datagrid tbody').on('click', '.btn-open', function(){
            noteCrud.showDetail(this)
        });

        $('.notes').on('click', '.btn-delete', function(e) {
            var id = $(this).attr("action-id");
            var tr = $(this).closest("tr");
            
            if(confirm("<?php echo __("êtes-vous sûr de vouloir supprimer la note"); ?>")){
                noteCrud.deleteRow(id, tr);
            }
            
            e.preventDefault();

            return false;
        });

        $('#add_note_form').submit(function(e) {
            var postData = $(this).serializeArray();
            noteCrud.addRow(postData);
            e.preventDefault();

            return false;
        });

        $('#edit_note_form').submit(function(e) {
            var postData = $(this).serializeArray();
            noteCrud.updateRow(postData);
            e.preventDefault();

            return false;
        });

        $(document).on('click', '.btn-edit', function(event){
            $('#edit_note_form').find('input, select').val('');
            var data = noteCrud.datagrid.row($(this).closest('tr')).data();

            $('#edit_note_form input, #edit_note_form select').each(function(){
                
                if($(this).attr('id'))
                {   
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

            $('#NoteEditDialog').modal('show');
            
            event.preventDefault();
            return false;
        });

        $('#NoteFilter').on('click', 'a', function (e) {
            var field_name =  $(this).parent().attr('data-value')
            var field_label = $(this).text();
            $(this).closest('.datagrid-search').find('.hidden').val(field_name);
            $(this).closest('.datagrid-search').find('.selected-label').text(' ' +field_label);
            $(this).closest('.datagrid-search').find('input[type = search]').val("");
            $(this).closest('.datagrid-search').find('input[type = search]').attr('placeholder', 'Chercher par '+field_label);
        });

        $('#NoteFilter .search').on('click', '.btn', function (e) {
            noteCrud.datagrid.ajax.reload();
        });

        $('#NoteEditDialog').on('hidden.bs.modal', function (e) {
            $('#edit_note_form').clearForm();
        });

        $('#NoteAddDialog').on('hidden.bs.modal', function (e) {
            $('#add_note_form').clearForm();
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
            }
            else {
                $(this).find('.loading-message').show();
            }
        }); 
    });

<?php $this->Html->scriptEnd(); ?></script>

<div class="notes index">
    <div class="datagrid" id="note_datagrid_container">
        <div class="datagrid-toolbar">
            <div class="col-xs-12 col-sm-6 col-md-8 no-padding">
                <a htref = "#" class = "btn btn-primary" data-toggle = "modal" data-target = "#NoteAddDialog" >
                    <?php echo __("Nouvelle Note"); ?>              
                </a>
            </div>
            <div class="col-xs-6 col-md-4 no-padding">
                <div class="datagrid-search" id = "NoteFilter">
                    <div class="input-group">
                        <div class="input-group-btn selectlist" data-resize="auto" data-initialize="selectlist">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="selected-label">Nom de l'élève</span>
                                <span class="caret"></span>
                                <span class="sr-only"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">                                          
                                <li data-value="Eleve.nom">  
                                    <a href="#">Nom de l'élève</a>
                                </li>                                           
                                <li data-value="Matiere.nom">    
                                    <a href="#">Matière</a>
                                </li>                                               
                            </ul>
                            <input class="hidden hidden-field" name="column" readonly="readonly" aria-hidden="true" type="text" value = "Note.id">
                        </div>
                        <div class="search input-group">
                            <input type="search" class="form-control" placeholder="<?php  echo __("Charcher par Nom de l'élève");  ?>"/>
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
        <table id="note_datagrid" class="display table-bordered"></table>
    </div>
</div>

<div class="modal fade" id="NoteAddDialog" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="NoteEdition" data-backdrop = "static">
    <?php  
        echo $this->Form->create('Note',
            array(
                'url' => array('action' => 'add', 'ext' => 'json'), 
                'id' => 'add_note_form'
            )
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
                    <?php  echo __('Ajouter une note');  ?>             
                </h4>
            </div>
            <div class="modal-body">
            <?php
                $this->Form->inputDefaults(array('label' => false, 'class' => 'span10'));
                echo $this->Form->input('eleve_id', array(
                    'label' => __('Élève'),
                    'id' => 'AddNoteEleveId'
                ));
                echo $this->Form->input('matiere_id', array(
                    'label' => __('Matière'),
                    'id' => 'AddNoteMatiereId'
                ));
                echo $this->Form->input('note', array(
                    'label' => __('Note'),
                    'id' => 'AddNoteNote',
                    'min' => '0',
                    'max' => '20'
                ));
            ?>
            </div>
            <div class="loading-message" >&nbsp;&nbsp;Loading...</div>
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

<div class="modal fade" id="NoteEditDialog" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="NoteEdition" backdrop = "static">
    <?php  
        echo $this->Form->create('Note', array(
            'url' => array('action' => 'edit', 'ext' => 'json'), 
            'id' => 'edit_note_form'
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
                    <?php  echo __('Edition de la Note');  ?>               
                </h4>
            </div>
            <div class="modal-body">
            <?php
                echo $this->Form->input('id');
                $this->Form->inputDefaults(array('label' => false, 'class' => 'span10'));
                echo $this->Form->input('eleve_id', array(
                    'label' => __('Élève'),
                    'id' => 'EditNoteEleveId'
                ));
                echo $this->Form->input('matiere_id', array(
                    'label' => __('Matière'),
                    'id' => 'EditNoteMatiereId'
                ));
                echo $this->Form->input('note', array(
                    'label' => __('Note'),
                    'id' => 'EditNoteNote',
                    'min' => '0',
                    'max' => '20'
                ));
            ?>
            </div>
            <div class="loading-message" >&nbsp;&nbsp;Loading...</div>
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