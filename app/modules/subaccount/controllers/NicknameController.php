<?php
namespace Backoffice\Subaccount\Controllers;

use System\Datalayer\DLUser;
use System\Library\Security\User as SecurityUser ;

use System\Datalayer\DLUserAclAccess;
use System\Datalayer\DLUserAclResource;
use System\Library\User\General ;
use System\Library\Security\Validation ;
use System\Library\General\GlobalVariable;



class NicknameController extends \Backoffice\Controllers\ProtectedController
{
    public function resetAction()
    {
        $previousPage = new GlobalVariable();
        $childId = $this->dispatcher->getParam("id");

        $DLUser = new DLUser();
        $Child = $DLUser->getById($childId);

        if(!isset($childId) || !$Child){
            $this->flash->error("undefined_subaccount");
            $this->response->redirect($this->_module."/subaccount/")->send();
        }
//        $realParent = false;
        if($this->_user->id != $Child->idp ){
            $this->errorFlash("cannot_access");
            return $this->response->redirect($this->_module."/subaccount/detail/".$childId)->send();
        }

        try {
            $DLUser->resetNickname($childId);

            $this->flash->success("reset_nickname_success");
            $this->response->redirect($previousPage->previousPage())->send();
        } catch (\Exception $e) {
            $this->flash->error($e->getMessage());
        }
    }


}
