<?php
App::uses('AppController', 'Controller');
/**
 * Notes Controller
 *
 * @property Note 
 * @property PaginatorComponent
 */
class NotesController extends AppController {

/**
 * Components
 *
 * @var array
 */
    public $components = array('Paginator');

/**
 * beforeFilter method
 *
 * @return void
 */
    function beforeFilter() { 
        parent::beforeFilter();
    }

/**
 * get_datagrid_data method
 *
 * @return array
 */
    public function get_datagrid_data() {

        $limit = Configure::read('DataGrid.limit');
        list($page, $order) = $this->__getDataTablePaginator();
        $conditions = array();
        
        if ( isset($this->params['data']['filter'])) {
            $conditions = array($this->params['data']['filter']);
        }

        $this->Paginator->settings = array(
            'conditions' => $conditions,
            'limit' => $limit,
            'page' => $page,
            'order' => $order
        );

        $datum = $this->Paginator->paginate('Note');

        $data = array(
            "draw" => (isset($this->params['data']['draw']))? $this->params['data']['draw'] : 1, 
            "recordsTotal" => $this->params['paging']['Note']['count'], 
            "recordsFiltered" => $this->params['paging']['Note']['count'],
            "data" => $datum
        );
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }

/**
 * index method
 *
 * @return void
 */
    public function index() {
        $eleves = $this->Note->Eleve->find('list');
        $matieres = $this->Note->Matiere->find('list');
        $this->set(compact('eleves', 'matieres'));
    }

/**
 * add method
 *
 * @return void
 */
    public function add() {

        $inserted_record = array();
        $errors = array();
        $this->Note->create();
        
        if ($this->Note->save($this->request->data)) {
            $message = __('La note a été bien ajoutée');
            $result = 'success';
            $inserted_record = $this->Note->find('first', array(
                'conditions' => array(
                    'Note.id' => $this->Note->id
                )
            ));
        } else {
            $errors = $this->Note->validationErrors;
            $message =__('Une erreur est survenu lors du sauvegarde. réessayer svp ultérieurement.');
            $result = 'error';
        }

        $formated_record = $this->__format_datagrid_data(array($inserted_record));
        $data = array('message' =>  $message, 'result' => $result, 'record' => $formated_record[0], 'errors' => $errors);
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }

/**
 * edit method
 *
 * @return void
 */
    public function edit() {
        
        $updated_record = array();
        $errors = array();

        if ($this->Note->save($this->request->data)) {

            $message = __('la note a été bien modifiée');
            $result = 'success';
            $updated_record = $this->Note->find('first', array(
                'conditions' => array(
                    'Note.id' => $this->Note->id
                )
            ));

        } else {
            $errors = $this->Note->validationErrors;
            $message =__('Une erreur est survenue lors du sauvegarde. réessayer svp ultérieurement.');
            $result = 'error';
        }

        $formated_record = $this->__format_datagrid_data(array($updated_record));
        $data = array(
            'message' =>  $message, 'result' => $result, 
            'record' => $formated_record[0] , 'errors' => $errors
        );
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }

/**
 * delete method
 *
 * @access public
 * @return void
 */
    public function delete() {

        $id = (isset($this->request->data['id']))? $this->request->data['id'] : -1;

        if ($this->Note->delete($id)) {
            $message = __('La Note a été bien supprimé');
            $result = 'success';
        } else {
            $message = __('Une erreur est survenue lors de suppression de la note');
            $result = 'error';
        }

        $data =  array('message' =>  $message, 'result' => $result, 'id' => $id);
        
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
}
