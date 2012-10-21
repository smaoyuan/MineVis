<?php

/**
 * EntityFrequency filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseEntityFrequencyFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'mining_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => true)),
      'entity_id'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'entity_name'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'entity_type'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'bicluster_count' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'mining_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mining'), 'column' => 'id')),
      'entity_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'entity_name'     => new sfValidatorPass(array('required' => false)),
      'entity_type'     => new sfValidatorPass(array('required' => false)),
      'bicluster_count' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('entity_frequency_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EntityFrequency';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'mining_id'       => 'ForeignKey',
      'entity_id'       => 'Number',
      'entity_name'     => 'Text',
      'entity_type'     => 'Text',
      'bicluster_count' => 'Number',
    );
  }
}
