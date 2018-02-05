<?php
App::uses('AppModel', 'Model');
/**
 * Eleve Model
 *
 * @property Note $Note
 */
class Eleve extends AppModel {

/**
 * virtual Field
 *
 * @var String
 */

public $virtualFields = array(
    'fullname' => "CONCAT(Eleve.prenom, ' ', Eleve.nom)"
);

/**
 * display Field
 *
 * @var String
 */

public $displayField = 'fullname';

/**
 * Validation rules
 *
 * @var array
 */
    public $validate = array(
        'prenom' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Ce champ ne peut pas être vide',
            ),
        ),
        'nom' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Ce champ ne peut pas être vide',
            ),
        ),
        'birthday' => array(
            'date' => array(
                'rule' => array('date', 'dmy'),
                'message' => 'Merci de saisir une date valide',
            ),
        ),
    );

/**
 * hasMany associations
 *
 * @var array
 */
    public $hasMany = array(
        'Note' => array(
            'className' => 'Note',
            'foreignKey' => 'eleve_id',
            'dependent' => false,
        )
    );
/**
 * Called before each save operation, after validation. Return a non-true result
 * to halt the save.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if the operation should continue, false if it should abort
 */ 
    public function beforeSave($options = array()) {
        if (!empty($this->data['Eleve']['birthday'])) {
            $date = strtotime($this->data['Eleve']['birthday']);
            $this->data['Eleve']['birthday'] = date('Y-m-d', $date);
        }
        return true;
    }
/**
 * Called after each find operation. Can be used to modify any results returned by find().
 * Return value should be the (modified) results.
 *
 * @param mixed $results The results of the find operation
 * @param bool $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed Result of the find operation
 */
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($val['Eleve']['birthday'])) {
                $date = strtotime($val['Eleve']['birthday']);
                $results[$key]['Eleve']['birthday'] = date('d-m-Y', $date);;
            }
        }
        return $results;
    }
}
