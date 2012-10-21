<?php

/**
 * Mining form base class.
 *
 * @method Mining getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMiningForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'project_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Project'), 'add_empty' => false)),
      'type'                    => new sfWidgetFormInputText(),
      'algorithm'               => new sfWidgetFormInputText(),
      'name'                    => new sfWidgetFormInputText(),
      'param'                   => new sfWidgetFormInputText(),
      'started'                 => new sfWidgetFormInputCheckbox(),
      'setup'                   => new sfWidgetFormInputCheckbox(),
      'complete'                => new sfWidgetFormInputCheckbox(),
      'process_id'              => new sfWidgetFormInputText(),
      'document_link_status'    => new sfWidgetFormInputText(),
      'entity_frequency_status' => new sfWidgetFormInputText(),
      'created_at'              => new sfWidgetFormDateTime(),
      'updated_at'              => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'project_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Project'))),
      'type'                    => new sfValidatorInteger(),
      'algorithm'               => new sfValidatorInteger(),
      'name'                    => new sfValidatorString(array('max_length' => 255)),
      'param'                   => new sfValidatorString(array('max_length' => 255)),
      'started'                 => new sfValidatorBoolean(array('required' => false)),
      'setup'                   => new sfValidatorBoolean(array('required' => false)),
      'complete'                => new sfValidatorBoolean(array('required' => false)),
      'process_id'              => new sfValidatorInteger(array('required' => false)),
      'document_link_status'    => new sfValidatorInteger(array('required' => false)),
      'entity_frequency_status' => new sfValidatorInteger(array('required' => false)),
      'created_at'              => new sfValidatorDateTime(),
      'updated_at'              => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('mining[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Mining';
  }

}
