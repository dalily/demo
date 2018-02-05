<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package     app.Controller
 * @link        https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

/**
 * Components
 *
 * @var array
 */ 
    public $components = array('RequestHandler');

/**
 * __format_datagrid_data method
 *
 * @access protected
 * @param array $unformated_data unformated data
 * @return array $formated_data formated data
 */
    protected function __format_datagrid_data($unformated_data) {
        $formated_data = array();

        foreach ($unformated_data as $datum) {

            $formated_data[] = $datum;
        }

        return $formated_data;
    }
/**
 * __getDataTablePaginator method
 *
 * @access protected
 * @return array dataTable order and page
 */

    protected function __getDataTablePaginator(){
        
        if ( isset( $this->params['data']['start'] ) && 
            $this->params['data']['length'] != '-1' ) {
            $limit = $this->params['data']['length'];
        }

        $page = "1";
        
        if ( isset( $this->params['data']['start'] )) {
            $page = ($this->params['data']['start'] / $limit) + 1;
        }

        $order = "";
        
        if ( isset( $this->params['data']['order'] ) ) {

            foreach ($this->params['data']['order'] as $i => $datum) {
                if ( $this->params['data']['columns'][$datum['column']]['orderable'] == "true" ) {
                    if(!empty($order)) {
                        $order .= ", ";
                    }
                    $order .= "".$this->params['data']['columns'][$datum['column']]['data']." ".$datum['dir'];
                }
            }
        }

        return array($page, $order);        
    }
}
