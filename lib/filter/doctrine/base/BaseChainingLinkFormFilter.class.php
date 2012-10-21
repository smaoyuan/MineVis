<?php

/**
 * ChainingLink filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseChainingLinkFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'target_bicluster_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TargetBiCluster'), 'add_empty' => true)),
      'chaining_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chaining'), 'add_empty' => true)),
      'chaining_link_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ChainingLinkType'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'target_bicluster_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TargetBiCluster'), 'column' => 'id')),
      'chaining_id'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Chaining'), 'column' => 'id')),
      'chaining_link_type_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ChainingLinkType'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('chaining_link_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ChainingLink';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'target_bicluster_id'   => 'ForeignKey',
      'chaining_id'           => 'ForeignKey',
      'chaining_link_type_id' => 'ForeignKey',
    );
  }
}
