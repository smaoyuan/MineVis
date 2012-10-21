<?php

/**
 * ChainingLinkDestination form base class.
 *
 * @method ChainingLinkDestination getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseChainingLinkDestinationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'chaining_link_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ChainingLink'), 'add_empty' => false)),
      'destination_bicluster_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DestinationBiCluster'), 'add_empty' => false)),
      'distance'                 => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'chaining_link_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ChainingLink'))),
      'destination_bicluster_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DestinationBiCluster'))),
      'distance'                 => new sfValidatorPass(),
    ));

    $this->widgetSchema->setNameFormat('chaining_link_destination[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ChainingLinkDestination';
  }

}
