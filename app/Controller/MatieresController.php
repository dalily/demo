<?php
App::uses('AppController', 'Controller');
/**
 * Matieres Controller
 *
 * @property Matiere
 * @property PaginatorComponent
 */
class MatieresController extends AppController {

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
 * @access public
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

        $datum = $this->Paginator->paginate('Matiere');

        $data = array(
            "draw" => (isset($this->params['data']['draw']))? $this->params['data']['draw'] : 1, 
            "recordsTotal" => $this->params['paging']['Matiere']['count'], 
            "recordsFiltered" => $this->params['paging']['Matiere']['count'],
            "data" => $datum
        );
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }

/**
 * index method
 *
 * @access public
 * @return void
 */
    public function index() {}

/**
 * add method
 *
 * @access public
 * @return void
 */
    public function add() {

        $inserted_record = array();
        $errors = array();
        $this->Matiere->create();
        
        if ($this->Matiere->save($this->request->data)) {
            $message = __('La matière a été bien ajoutée');
            $result = 'success';
            $inserted_record = $this->Matiere->find('first', array(
                'conditions' => array(
                    'Matiere.id' => $this->Matiere->id
                )
            ));
        } else {
            $errors = $this->Matiere->validationErrors;
            $message =__('Une erreur est survenue lors du sauvegarde de la matière');
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
 * @access public
 * @return void
 */
    public function edit() {
        
        $updated_record = array();
        $errors = array();

        if ($this->Matiere->save($this->request->data)) {

            $message = __('La matière a été bien ajoutée');
            $result = 'success';
            $updated_record = $this->Matiere->find('first', array(
                'conditions' => array(
                    'Matiere.id' => $this->Matiere->id
                )
            ));

        } else {
            $errors = $this->Matiere->validationErrors;
            $message =__('Une erreur est survenue lors de modification de la matière');
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

        if ($this->Matiere->delete($id)) {
            $message = __('La matière a été bien supprimé');
            $result = 'success';
        } else {
            $message = __('Une erreur est survenue lors de suppression de la matière');
            $result = 'error';
        }

        $data =  array('message' =>  $message, 'result' => $result, 'id' => $id);
        
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
}
