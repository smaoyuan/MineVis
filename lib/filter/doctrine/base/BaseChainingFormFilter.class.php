<?php

/**
 * Chaining filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseChainingFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'mining_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => true)),
      'name'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'param'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'configured' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'started'    => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'complete'   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'process_id' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'mining_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mining'), 'column' => 'id')),
      'name'       => new sfValidatorPass(array('required' => false)),
      'param'      => new sfValidatorPass(array('required' => false)),
      'configured' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'started'    => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'complete'   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'process_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('chaining_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Chaining';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'mining_id'  => 'ForeignKey',
      'name'       => 'Text',
      'param'      => 'Text',
      'configured' => 'Boolean',
      'started'    => 'Boolean',
      'complete'   => 'Boolean',
      'process_id' => 'Number',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    );
  }
}
