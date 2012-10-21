<?php

/**
 * ProjectConfig form base class.
 *
 * @method ProjectConfig getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProjectConfigForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'project_id'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Project'), 'add_empty' => false)),
      'table_a'                    => new sfWidgetFormInputText(),
      'table_axb'                  => new sfWidgetFormInputText(),
      'table_b'                    => new sfWidgetFormInputText(),
      'table_a_id_field'           => new sfWidgetFormInputText(),
      'table_b_id_field'           => new sfWidgetFormInputText(),
      'table_a_description_field'  => new sfWidgetFormInputText(),
      'table_b_description_field'  => new sfWidgetFormInputText(),
      'table_axb_table_a_id_field' => new sfWidgetFormInputText(),
      'table_axb_table_b_id_field' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'project_id'                 => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Project'))),
      'table_a'                    => new sfValidatorString(array('max_length' => 255)),
      'table_axb'                  => new sfValidatorString(array('max_length' => 255)),
      'table_b'                    => new sfValidatorString(array('max_length' => 255)),
      'table_a_id_field'           => new sfValidatorString(array('max_length' => 255)),
      'table_b_id_field'           => new sfValidatorString(array('max_length' => 255)),
      'table_a_description_field'  => new sfValidatorString(array('max_length' => 255)),
      'table_b_description_field'  => new sfValidatorString(array('max_length' => 255)),
      'table_axb_table_a_id_field' => new sfValidatorString(array('max_length' => 255)),
      'table_axb_table_b_id_field' => new sfValidatorString(array('max_length' => 255)),
    ));

    $this->widgetSchema->setNameFormat('project_config[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProjectConfig';
  }

}
