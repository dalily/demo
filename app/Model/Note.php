<?php
App::uses('AppModel', 'Model');
/**
 * Note Model
 *
 * @property Eleve $Eleve
 * @property Matiere $Matiere
 */
class Note extends AppModel {


/**
 * Validation rules
 *
 * @var array
 */
    public $validate = array(
        'eleve_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Veuillez sélectionner un éléve',
            ),
        ),
        'matiere_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Veuillez sélectionner une matière',
            ),
        ),
        'note' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Ce champ ne peut pas être vide',
            ),
            'between' => array(
                'rule' => array('comparison', '<=', 20),
                'message' => 'La note maximale est 20'
            )
        ),
    );

/**
 * belongsTo associations
 *
 * @var array
 */
    public $belongsTo = array(
        'Eleve' => array(
            'className' => 'Eleve',
            'foreignKey' => 'eleve_id'
        ),
        'Matiere' => array(
            'className' => 'Matiere',
            'foreignKey' => 'matiere_id'
        )
    );
}
