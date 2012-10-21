<?php

/**
 * ChainingLinkType filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseChainingLinkTypeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'chaining_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chaining'), 'add_empty' => true)),
      'name'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'status_code' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'start_time'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'end_time'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'chaining_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Chaining'), 'column' => 'id')),
      'name'        => new sfValidatorPass(array('required' => false)),
      'status_code' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'start_time'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'end_time'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('chaining_link_type_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ChainingLinkType';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'chaining_id' => 'ForeignKey',
      'name'        => 'Text',
      'status_code' => 'Number',
      'start_time'  => 'Date',
      'end_time'    => 'Date',
    );
  }
}
