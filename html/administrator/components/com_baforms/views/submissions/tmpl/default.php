<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
$sortFields = $this->getSortFields();
$listDirn  = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));
$language = JFactory::getLanguage();
$language->load('com_languages', JPATH_ADMINISTRATOR);
?>
<link rel="stylesheet" href="components/com_baforms/assets/css/ba-admin.css" type="text/css"/>
<script src="components/com_baforms/assets/js/submissions.js" type="text/javascript"></script>
<script src="components/com_baforms/assets/js/ba-about.js" type="text/javascript"></script>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = 'title';
        dirn = direction.options[direction.selectedIndex].value;
		Joomla.tableOrdering(order, dirn, '');
	}
    Joomla.searchTable = function()
    {
        var text = jQuery('#sortTable option:selected').text();
        jQuery('#filter_search').val(text);
        jQuery('.icon-search').parent().trigger('click');
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_baforms&view=submissions'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="export-dialog" class="modal hide ba-modal-md" style="display:none">
        <div class="modal-header">
            <h3><?php echo JText::_('EXPORT') ?></h3>
        </div>
        <div class="modal-body">
             <label class="column xml-upload">
                <img src="components/com_baforms/assets/images/export-xml.png">
                <p><?php echo JText::_('XML') ?></p>
            </label>
             <label class="column csv-upload">
                <img src="components/com_baforms/assets/images/export-csv.png">
                <p><?php echo JText::_('CSV') ?></p>
            </label>
        </div>
        <div class="modal-footer">
            <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CLOSE') ?></a>
        </div>
    </div>
    <div id="error-dialog" class="modal hide ba-modal-sm" style="display:none">
        <div class="modal-body">
            <p class="modal-text not-selected"><?php echo JText::_('PLEASE_SELECT') ?></p>
            <p class="modal-text many-selected"><?php echo JText::_('CANT_PRINT_MANY') ?></p>
        </div>
        <div class="modal-footer">
            <a href="#" class="ba-btn" data-dismiss="modal"><?php echo JText::_('CLOSE') ?></a>
        </div>
    </div>
    <div  class="span2" id="j-sidebar-container">
        <?php echo $this->sidebar; ?>
    </div>
    <div class="span10" id="j-main-container">
        <div id="filter-bar">
            <label for="filter_search" class="element-invisible">
                <?php echo JText::_('SEARCH_FORM?') ?>
            </label>
            <input type="text" name="filter_search" id="filter_search"
                   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                   placeholder="<?php echo JText::_('SEARCH') ?>">
            <button type="submit" class="ba-btn ">
                <i class="icon-search"></i>
            </button>
            <button type="button" class="ba-btn" onclick="document.id('filter_search').value='';this.form.submit();">
                <i class="icon-remove"></i>
            </button>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.searchTable()">
					<option value=""><?php echo JText::_('FORM_NAME') ?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
            <div class="btn-group pull-right">
                <input class="ba-btn export" type="button" value="<?php echo JText::_('EXPORT') ?>">
            </div>
            <div class="btn-group pull-right">
                <button href="#" class="print-submission ba-btn"><i class="icon-print"></i></button>
            </div>
            <div class="btn-group pull-right">
                <button href="#" class="pdf-submission ba-btn"><i class="icon-file-2"></i></button>
            </div>
        </div>
        <div class="clearfix"> </div>
        <div class="span6 submissions-list">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="1%">
                            <input type="checkbox" name="checkall-toggle" value=""
                                   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                        </th>
                        <th>
                            <?php echo JText::_('Forms'); ?>
                        </th>
                        <th>
                            <?php echo JText::_('DATE'); ?>
                        </th>
                        <th width="1%">
                            <?php echo JText::_('ID'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->items as $i => $item) { ?>
                    <tr class="<?php if ($item->submission_state == 1) {
                                         echo 'ba-new';
                                     } else {
                                         echo 'ba-default';
                                     }
                               ?>">
                        <td>
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td>
                            <a href="#" class="show_submissions"><?php echo $item->title; ?></a>
                             <input type="hidden" value="<?php echo htmlentities($item->mesage, ENT_COMPAT); ?>">
                        </td>
                        <td>
                            <?php echo $item->date_time; ?>
                        </td>
                        <td class="item-id">
                            <?php echo $item->id; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="span6">
            <table id="submission-data" class="table table-striped">
                
            </table>
        </div>
        <div>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
    <input type="hidden" name="filter_order" value="title" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
<?php
    include(JPATH_COMPONENT.'/views/layout/about.php');
?>