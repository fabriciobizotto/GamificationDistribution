<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die;

class GamificationViewBadges extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Registry
     */
    protected $state;

    protected $items;
    protected $pagination;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;

    protected $sidebar;

    public $filterForm;
    public $activeFilters;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Prepare sorting data
        $this->prepareSorting();

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting()
    {
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') === 0);

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }
        
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        GamificationHelper::addSubmenu($this->getName());
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_GAMIFICATION_BADGES_MANAGER'));
        JToolbarHelper::addNew('badge.add');
        JToolbarHelper::editList('badge.edit');
        JToolbarHelper::divider();
        JToolbarHelper::publishList('badges.publish');
        JToolbarHelper::unpublishList('badges.unpublish');
        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_('COM_GAMIFICATION_DELETE_ITEMS_QUESTION'), 'badges.delete');
        JToolbarHelper::divider();
        JToolbarHelper::custom('badges.backToDashboard', 'dashboard', '', JText::_('COM_GAMIFICATION_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_GAMIFICATION_BADGES_MANAGER'));

        // Load language string in JavaScript
        JText::script('COM_GAMIFICATION_SEARCH_IN_TITLE_TOOLTIP');

        // Scripts
        JHtml::_('bootstrap.tooltip');
        JHtml::_('behavior.multiselect');

        JHtml::_('formbehavior.chosen', 'select');

        $this->document->addScript('../media/' . $this->option . '/js/admin/'.strtolower($this->getName()).'.js');
    }
}
