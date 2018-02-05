<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php 
		echo $this->Html->css(array(
			'../plugins/bootstrap/css/bootstrap.min.css',
			'../plugins/fontawesome/css/font-awesome.min.css',
			'../plugins/toastr/toastr.min',
			'../plugins/DataTables/css/jquery.dataTables.css',
			'../plugins/bootstrap-datepicker/bootstrap-datepicker3.standalone.min',
			'../plugins/fullcalendar/fullcalendar.min',
			'admin'
		));
		echo $this->Html->script(array(
			'../plugins/jquery/jquery.min.js',
			'../plugins/bootstrap/js/bootstrap.min.js',
			'../plugins/bootbox/bootbox.min',
			'../plugins/DataTables/jquery.dataTables.min.js',
			'../plugins/moment/moment-with-locales',
			'../plugins/toastr/toastr.min.js',
			'../plugins/bootstrap-datepicker/bootstrap-datepicker.min.js',
			'../plugins/fullcalendar/fullcalendar.min',
			'../plugins/fullcalendar/fr',
			'admin'
		));
		echo $this->Html->meta('icon');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1><?php echo $this->Html->link($cakeDescription, 'https://cakephp.org'); ?></h1>
		</div>
		<div id = "menu">
			<ul id="navigation">
			  <li class = "<?php if($this->params['controller'] == 'notes') echo 'current'; ?>">
			  	<?php echo $this->Html->link(__("Notes"), array('controller' => 'notes')); ?>
			  </li>
			  <li class = "<?php if($this->params['controller'] == 'eleves') echo 'current'; ?>">
			  	<?php echo $this->Html->link(__("Elèves"), array('controller' => 'eleves')); ?></li>
			  <li class = "<?php if($this->params['controller'] == 'matieres') echo 'current'; ?>">
			  	<?php echo $this->Html->link(__("Matières"), array('controller' => 'matieres')); ?>
			  </li>
			  <li class = "<?php if($this->params['controller'] == 'calendars') echo 'current'; ?>">
			  	<?php echo $this->Html->link(__("Calendrier"), array('controller' => 'calendars')); ?>
			  </li>
			  <li class = "<?php if($this->params['controller'] == 'maps') echo 'current'; ?>">
			  	<?php echo $this->Html->link(__("Map"), array('controller' => 'maps')); ?>
			  </li>
			</ul>
		</div>
		<div id="content">
			<?php echo $this->Flash->render(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
					'https://cakephp.org/',
					array('target' => '_blank', 'escape' => false, 'id' => 'cake-powered')
				);
			?>
			<p>
				<?php echo $cakeVersion; ?>
			</p>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
