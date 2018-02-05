<?php
App::uses('AppController', 'Controller');
/**
 * Eleves Controller
 *
 * @property Eleve
 * @property PaginatorComponent
 * @property uses
 */
class ElevesController extends AppController {

/**
 * Model used
 *
 * @var array
 */
	public $uses = array('Eleve');

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

		$datum = $this->Paginator->paginate('Eleve');

		$data = array(
			"draw" => (isset($this->params['data']['draw']))? $this->params['data']['draw'] : 1, 
			"recordsTotal" => $this->params['paging']['Eleve']['count'], 
			"recordsFiltered" => $this->params['paging']['Eleve']['count'],
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
		$this->Eleve->create();
		
		if ($this->Eleve->save($this->request->data)) {
			$message = __("L'éleve a été bien ajouté");
			$result = 'success';
			$inserted_record = $this->Eleve->find('first', array(
				'conditions' => array(
					'Eleve.id' => $this->Eleve->id
				)
			));
		} else {
			$errors = $this->Eleve->validationErrors;
			$message =__("Une erreur est survenue lors du sauvegarde");
			$result = 'error';
		}

		$formated_record = $this->__format_datagrid_data(array($inserted_record));
		$data = array(
			'message' =>  $message, 'result' => $result, 
			'record' => $formated_record[0], 'errors' => $errors
		);
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

		if ($this->Eleve->save($this->request->data)) {
			$message = __("L'éleve a été bien mis à jour");
			$result = 'success';
			$updated_record = $this->Eleve->find('first', array(
				'conditions' => array(
					'Eleve.id' => $this->Eleve->id
				)
			));
		} else {
			$errors = $this->Eleve->validationErrors;
			$message =__('Une erreur est survenue lors de la mise à jour');
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

		if ($this->Eleve->delete($id)) {
			$message = __('La suppression a été effectuée avec succès');
			$result = 'success';
		} else {
			$message = __('Une erreur est survenue lors de la suppression');
			$result = 'error';
		}

		$data =  array('message' =>  $message, 'result' => $result, 'id' => $id);
		
		$this->set('data', $data);
        $this->set('_serialize', 'data');
	}
}
