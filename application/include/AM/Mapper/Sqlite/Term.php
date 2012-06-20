<?php
/**
 * @file
 * AM_Mapper_Sqlite_Term class definition.
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
 * @ingroup AM_Mapper
 */
class AM_Mapper_Sqlite_Term extends AM_Mapper_Sqlite_Abstract
{
    /**
     * @return AM_Mapper_Sqlite_Term
     */
    protected function _unmapCustom()
    {
        $oTerm   = $this->getModel();
        /* @var $oTerm AM_Model_Db_Term */
        $iPageId = null;

        $oPages = $oTerm->getPages();
        if(count($oPages)) {
            $iPageId = $oPages->current()->id;
        }

        $aData = array(
                'id'            => $oTerm->id,
                'title'         => $oTerm->title,
                'description'   => $oTerm->description,
                'thumb_stripe'  => $oTerm->getResources()->getValueForExport(AM_Model_Db_Term_Data_Resource::RESOURCE_KEY_STRIPE),
                'thumb_summary' => $oTerm->getResources()->getValueForExport(AM_Model_Db_Term_Data_Resource::RESOURCE_KEY_SUMMARY),
                'color'         => $oTerm->color,
                'firstpage_id'  => $iPageId
        );
        $this->_getSqliteGateway('menu')->insert($aData);

        return $this;
    }
}