<?php

/**
 * Project form.
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ProjectForm extends BaseProjectForm {

  public function configure() {

    unset(
	    $this['created_at'], $this['updated_at'],
	    $this['expires_at'], $this['is_activated']
    );

    $this->widgetSchema['external_database'] = new sfWidgetFormChoice(array(
		'choices' => Doctrine_Core::getTable('Project')->getDatabaseList(),
		'expanded' => false,
	    ));
  }

}
