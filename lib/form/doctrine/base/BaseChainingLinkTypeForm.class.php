<?php

/**
 * ChainingLinkType form base class.
 *
 * @method ChainingLinkType getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseChainingLinkTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'chaining_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chaining'), 'add_empty' => false)),
      'name'        => new sfWidgetFormInputText(),
      'status_code' => new sfWidgetFormInputText(),
      'start_time'  => new sfWidgetFormInputText(),
      'end_time'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'chaining_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Chaining'))),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'status_code' => new sfValidatorInteger(array('required' => false)),
      'start_time'  => new sfValidatorPass(array('required' => false)),
      'end_time'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('chaining_link_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ChainingLinkType';
  }

}
