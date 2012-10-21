<?php

/**
 * vis actions.
 *
 * @package    MineVis
 * @subpackage vis
 * @author     Patrick Fiaux
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class visActions extends sfActions {

    public function executeAjax(sfWebRequest $request) {
        $vis_id = $request->getParameter('vis_id');
        $vis = Doctrine_Core::getTable('Visualization')->find(array($vis_id));

        /*
         * Make sure vis isn't null
         */
        if ($vis) {
            return $this->renderPartial(
                            'ajax', array(
                        'type' => $request->getParameter('type'),
                        'term' => $request->getParameter('term'),
                        'ent_name' => $request->getParameter('ent_name'),
                        'ent_id' => $request->getParameter('ent_id'),
                        'ent_type' => $request->getParameter('ent_type'),
                        'vis' => $vis
                            )
            );
        } else {
            $error_msg = array(array('label' => "vis not found error: '" . $vis_id . "'", 'category' => 'Errors'));
            return $this->renderText(json_encode($error_msg));
        }
    }

    public function executeAjaxsave(sfWebRequest $request) {
        $vis_id = $request->getParameter('vis_id');
        $save = $request->getParameter('save');
        $vis = Doctrine_Core::getTable('Visualization')->find(array($vis_id));
        /*
         * Make sure vis isn't null
         */
        if ($vis) {
            $vis->setJsondata($save);
            $vis->save();
            return $this->renderText("Ajax Save Successful");

        } else {
            return $this->renderText("Ajax Save Failed");
        }
    }

    public function executeIndex(sfWebRequest $request) {
        $this->visualizations = Doctrine_Core::getTable('Visualization')
                ->createQuery('a')
                ->execute();
    }

    public function executeShow(sfWebRequest $request) {
        $this->visualization = Doctrine_Core::getTable('Visualization')->find(array($request->getParameter('id')));
        $this->forward404Unless($this->visualization);
    }

    public function executeNew(sfWebRequest $request) {
        $this->form = new VisualizationForm();
    }

    public function executeCreate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST));

        $this->form = new VisualizationForm();

        $this->processForm($request, $this->form);

        $this->setTemplate('new');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->forward404Unless($visualization = Doctrine_Core::getTable('Visualization')->find(array($request->getParameter('id'))), sprintf('Object visualization does not exist (%s).', $request->getParameter('id')));
        $this->form = new VisualizationForm($visualization);
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
        $this->forward404Unless($visualization = Doctrine_Core::getTable('Visualization')->find(array($request->getParameter('id'))), sprintf('Object visualization does not exist (%s).', $request->getParameter('id')));
        $this->form = new VisualizationForm($visualization);

        $this->processForm($request, $this->form);

        $this->setTemplate('edit');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $this->forward404Unless($visualization = Doctrine_Core::getTable('Visualization')->find(array($request->getParameter('id'))), sprintf('Object visualization does not exist (%s).', $request->getParameter('id')));
        $visualization->delete();

        $this->redirect('vis/index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $params = $request->getParameter($form->getName());
        //echo "id: " . $params['id'] . " chaining id: " . $params['chaining_id'] . "<br>/n";
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $visualization = $form->save();

            $this->redirect('vis/show?id=' . $visualization->getId());
        }
    }

}
