<?php
/**
 * @file
 * AM_Model_Db_Table_Issue class definition.
 *
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
 *
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Model
 */
class AM_Model_Db_Table_Issue extends AM_Model_Db_Table_Abstract
{
    /**
     * Checks the client's access to the issue
     * @param int $iIssueId
     * @param array $aUserInfo
     * @return boolean
     */
    public function checkAccess($iIssueId, $aUserInfo)
    {
        if ('admin' == $aUserInfo['role']) {
            return true;
        }

        $iIssueId  = intval($iIssueId);
        $iClientId = intval($aUserInfo['client']);

        $oQuery = $this->getAdapter()->select()
                              ->from('issue', array('issue_id' => 'issue.id'))

                              ->join('user', 'issue.user = user.id', null)
                              ->join('application', 'application.id = issue.application', null)

                              ->where('issue.deleted = ?', 'no')
                              ->where('user.deleted = ?', 'no')
                              ->where('application.deleted = ?', 'no')

                              ->where('issue.id = ?', $iIssueId)
                              ->where('user.client = application.client')
                              ->where('application.client = ?', $iClientId);

        $oIssue  = $this->getAdapter()->fetchOne($oQuery);
        $bResult = $oIssue ? true : false;

        return $bResult;
    }

    /**
     * Get issue by page id
     * @param int $iPageId
     * @return AM_Model_Db_Issue
     * @throws AM_Model_Db_Table_Exception
     */
    public function findOneByPageId($iPageId)
    {
        $iPageId = intval($iPageId);
        if ($iPageId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameter PAGE_ID given');
        }

        $oQuery = $this->select()
                ->from('issue')
                ->join('revision', 'issue.id = revision.issue', null)
                ->join('page', 'revision.id = page.revision', null)
                ->where('page.id = ?', $iPageId);

        $oRow = $this->fetchRow($oQuery);

        return $oRow;
    }

    /**
     * Get all issues by application and user
     * @param int $iApplicationId
     * @param int $iUserId
     * @return AM_Model_Db_Rowset_Issue
     */
    public function findAllByApplicationIdAndUser($iApplicationId, $iUserId)
    {
        $iApplicationId = intval($iApplicationId);
        $iUserId        = intval($iUserId);
        if ($iApplicationId <= 0 || $iUserId <= 0) {
            throw new AM_Model_Db_Table_Exception('Wrong parameters given');
        }

        $oSelect = $this->_findAllQuery()
                ->where('issue.application = ?', $iApplicationId)
                ->where('issue.user = ?', $iUserId);
        $oRows = $this->fetchAll($oSelect);

        return $oRows;
    }

    /**
     * Prepare query to find all issues
     * @return Zend_Db_Table_Select
     */
    protected function _findAllQuery()
    {
        $oQuery = $this->select()
                ->from('issue')
                ->where('issue.deleted = ?', 'no');

        return $oQuery;
    }
}