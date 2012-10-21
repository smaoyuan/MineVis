<?php

/**
 * ChainingLink form base class.
 *
 * @method ChainingLink getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseChainingLinkForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'target_bicluster_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TargetBiCluster'), 'add_empty' => false)),
      'chaining_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chaining'), 'add_empty' => false)),
      'chaining_link_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ChainingLinkType'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'target_bicluster_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TargetBiCluster'))),
      'chaining_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Chaining'))),
      'chaining_link_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ChainingLinkType'))),
    ));

    $this->widgetSchema->setNameFormat('chaining_link[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ChainingLink';
  }

}
