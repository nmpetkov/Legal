<?php
/**
 * Zikula Application Framework
 *
 * @copyright (c) 2001, Zikula Development Team
 * @link http://www.zikula.org
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

class Legal_Controller_Admin extends Zikula_Controller
{
    /**
     * the main administration function
     *
     * This function is the default function, and is called whenever the
     * module is called without defining arguments.
     * As such it can be used for a number of things, but most commonly
     * it either just shows the module menu and returns or calls whatever
     * the module designer feels should be the default function (often this
     * is the view() function)
     *
     * @return       output       The main module admin page.
     */
    public function main()
    {
        // Security check
        if (!SecurityUtil::checkPermission('legal::', '::', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        // Create a new output object
        $renderer = Zikula_View::getInstance('Legal', false);

        // Return the output that has been generated by this function
        return $renderer->fetch('legal_admin_main.htm');
    }

    /**
     * Modify configuration
     *
     * This is a standard function to modify the configuration parameters of the
     * module
     *
     * @return       output       The configuration page
     */
    public function modifyconfig()
    {
        // Security check
        if (!SecurityUtil::checkPermission('legal::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        // get all groups
        $groups = ModUtil::apiFunc('Groups', 'user', 'getall');

        // add dummy group "all groups" on top
        array_unshift($groups, array('gid' => 0, 'name' => $this->__('All users')));

        // add dummy group "no groups" on top
        array_unshift($groups, array('gid' => -1, 'name' => $this->__('No groups')));

        // Create output object
        $renderer = Zikula_View::getInstance('legal', false);

        // Assign all the module vars
        $renderer->assign(ModUtil::getVar('legal'));
        $renderer->assign('groups', $groups);

        // Return the output that has been generated by this function
        return $renderer->fetch('legal_admin_modifyconfig.htm');
    }

    /**
     * Update the configuration
     *
     * This is a standard function to update the configuration parameters of the
     * module given the information passed back by the modification form
     * Modify configuration
     *
     * @param        termsofuse          enable terms of use
     * @param        privacypolicy       enable privacy policy
     * @param        accessibilitypolicy enable accessibility policy
     */
    public function updateconfig()
    {
        // Security check
        if (!SecurityUtil::checkPermission('Legal::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        // Confirm the forms authorisation key
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerAuthidError(ModUtil::url('legal', 'admin', 'main'));
        }

        // set our module variables
        $termsofuse = (bool)FormUtil::getPassedValue('termsofuse', false, 'POST');
        $this->setVar('termsofuse', $termsofuse);
        $privacypolicy = (bool)FormUtil::getPassedValue('privacypolicy', false, 'POST');
        $this->setVar('privacypolicy', $privacypolicy);
        $accessibilitystatement = (bool)FormUtil::getPassedValue('accessibilitystatement', false, 'POST');
        $this->setVar('accessibilitystatement', $accessibilitystatement);

        $resetagreement = (int)FormUtil::getPassedValue('resetagreement', -1, 'POST');
        if ($resetagreement<>-1) {
            ModUtil::apiFunc('Legal', 'admin', 'resetagreement', array('gid' => $resetagreement));
        }

        // the module configuration has been updated successfuly
        LogUtil::registerStatus($this->__('Done! Saved module configuration.'));

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        return System::redirect(ModUtil::url('Legal', 'admin', 'main'));
    }
}