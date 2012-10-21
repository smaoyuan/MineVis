<?php

/**
 * navigation components.
 *
 * @package    MineVis
 * @subpackage navigation
 * @author     Patrick Fiaux
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class navigationComponents extends sfComponents {

    /**
     * This sets the selected menu section.
     * It's done automatically based on route here. It can be set manually by
     * passing current as a string. It's better automatically because it's all
     * here rather than in each single template file...
     */
    public function executeMenu() {
        if ($this->current == '') {
            $module = $this->getRequestParameter('module');
            switch ($module) {
                case "project":
                case "project_config":
                    $this->current = 'project';
                    break;
                case "mining":
                case "bicluster":
                    $this->current = 'mining';
                    break;
                case "chaining":
                    $this->current = 'chaining';
                    break;
                case "visualization":
                    $this->current = 'viz';
                    break;
            }
        }
    }

    /**
     * This tries to generate bread crumbs for pages based on the request.
     * No longer used...
     */
    public function executeBreadcrumbs() {
        $crumbs = array();
        $crumbs[] = array('project/index', 'MineVis Home');
        $action = $this->getRequestParameter('action');
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');

        if ($module == "project") {
            if ($action == "show" or $action == "edit") {
                //breadcrumb = MineVis > project
                $crumbs[] = array('project/' . $id, 'Project Detail');
            } else { //index or new or ...
                //breadcrumb = minviz (> projects)
            }
        } else if ($module == "project_config") {
            $pid = $this->getRequestParameter('project_id');
            $crumbs[] = array('project/' . $pid, 'Project');
            //echo 'project_id ' . $pid;
            if ($action == "show" or $action == "edit") {
                //breadcrumb = MineVis > project > config > detail
                $crumbs[] = array('project/' . $pid . '/config', 'Configuration');
                $crumbs[] = array('project/' . $pid . '/config/' . $id, 'Detail');
            } else { //index or new or ...
                //breadcrumb = minviz > project > config
                $crumbs[] = array('project/' . $pid . '/config', 'Configuration');
            }
        } else if ($module == "mining") {
            if ($action == "show" or $action == "edit") {
                //breadcrumb = MineVis > project
                $crumbs[] = array('project/', 'Projects');
                $crumbs[] = array('mining/' . $id, "Mining");
            } else { //index or new or ...
                //breadcrumb = minviz > minings
                $crumbs[] = array('mining/' . $id, 'Minings');
            }
        } else {
            //try to guess the path
            if ($action == "show" or $action == "edit") {
                $crumbs[] = array($module . '/', $module);
            } else { //index or new or ...
                $crumbs[] = array($module . '/' . $id, $module);
            }
        }

        switch ($module) {
            case "project":
                $this->current = 'project';
                break;
            case "project_config":
                $this->current = 'project';
                break;
            case "mining":
                $this->current = 'project';
                break;
            case "bicluster":
                $this->current = 'mining';
                break;
            case "chaining":
                $this->current = 'chaining';
                break;
            case "visualization":
                $this->current = 'viz';
                break;
        }
        $this->crumbs = $crumbs;
    }
}

