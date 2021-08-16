<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Assignments\Component;

defined('_JEXEC') or die;

class K2Category extends K2Base
{
    /**
     *  Pass check
     *
     *  @return bool
     */
    public function pass()
    {
	    return $this->passCategories('k2_categories', 'parent');
	}
}