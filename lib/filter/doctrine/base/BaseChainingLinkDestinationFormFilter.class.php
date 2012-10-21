<?php

/**
 * ChainingLinkDestination filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseChainingLinkDestinationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'chaining_link_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ChainingLink'), 'add_empty' => true)),
      'destination_bicluster_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DestinationBiCluster'), 'add_empty' => true)),
      'distance'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'chaining_link_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ChainingLink'), 'column' => 'id')),
      'destination_bicluster_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DestinationBiCluster'), 'column' => 'id')),
      'distance'                 => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('chaining_link_destination_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ChainingLinkDestination';
  }

  public function getFields()
  {
    return array(
      'id'                       => 'Number',
      'chaining_link_id'         => 'ForeignKey',
      'destination_bicluster_id' => 'ForeignKey',
      'distance'                 => 'Text',
    );
  }
}
