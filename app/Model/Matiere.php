<?php
App::uses('AppModel', 'Model');
/**
 * Matiere Model
 *
 * @property Note $Note
 */
class Matiere extends AppModel {

/**
 * display Field
 *
 * @var String
 */

public $displayField = 'nom';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'nom' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Ce champ ne peut pas être vide',
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
			'foreignKey' => 'matiere_id',
			'dependent' => false
		)
	);

}
