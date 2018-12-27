<?php
namespace Backoffice\Subaccount\Controllers;

use System\Datalayer\DLUser;
use System\Datalayer\DLUserAclAccess;


use System\Datalayer\DLUserWhitelistIp;
use System\Library\User\General ;
use System\Library\Security\Validation ;
use System\Library\General\GlobalVariable;
use System\Library\Security\User as SecurityUser ;



class SubaccountController extends \Backoffice\Controllers\ProtectedController
{
    protected $_limit = 10;
    protected $_pages = 1;

    public function indexAction()
    {
        $view = $this->view;
        $limit = $this->_limit;
        $pages = $this->_pages;
        if ($this->request->has("pages")){
            $pages = $this->request->get("pages");

        }elseif($this->session->has("pages")){
            $pages = $this->session->get("pages");
        }
        $status = GlobalVariable::$threeLayerStatus;

        $DLuser = new DLUser();
        $user = $DLuser->getSubaccountById($this->_user->id);

//        $paginator = new \Phalcon\Paginator\Adapter\Model(
        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => (array) $user ,
                "limit"=> $limit,
                "page" => $pages
            )
        );
        $page = $paginator->getPaginate();

        $pagination = ceil($user->count()/$limit);
        $view->page = $page->items;
        $view->pagination = $pagination;
        $view->pages = $pages;
        $view->limit = $limit;

        $view->main = $user ;
        $view->status = $status;

        \Phalcon\Tag::setTitle("Manage SubAccount - ".$this->_website->title);
    }

    public function addAction()
    {
        $view = $this->view;
        $code = array();
        foreach(range(0,9) as $v){
            $code[] = $v;
        }
        foreach(range('A','Z') as $v){
            $code[] = $v;
        }

        if ($this->request->isPost()) {
            $data = $this->request->getPost();

            $data['username'] = \implode($data['code']);
            $data['username'] = \filter_var(\strip_tags(\addslashes(strtoupper($data['username']))), FILTER_SANITIZE_STRING);
            $data['password'] = \filter_var(\strip_tags(\addslashes($data['password'])), FILTER_SANITIZE_STRING);
            $data['password_confirm'] = \filter_var(\strip_tags(\addslashes($data['password_confirm'])), FILTER_SANITIZE_STRING);

            $validation = new Validation();
            $validation->addCondition("Username", $data['username'] , "format", "username", 3 , 3  );
            $validation->addCondition("Password", $data['password'], "format", "password");
            $validation->addCondition("confirm_password", $data['password_confirm'], "value", "equal", $data['password']);
            $validation->execute();
            if ($validation->_valid == false) {
                foreach ($validation->_messages as $fieldName => $messages) {
                    foreach ($messages as $message) {
                        $this->errorFlash($message);
                    }
                }
            } else {

                $DLuser = new DLUser();
                $data['username'] =$this->_user->sn."SUB".$data['username'];
                $checknick = $DLuser->checkNickname($data['username']);

                if($checknick){
                    $this->errorFlash("nickname_already_used");
                } else {
                    $securityLibrary = new SecurityUser();
                    $data['password'] = $securityLibrary->enc_str($data['password']);
                    $data['password'] = base64_encode($data['password']);
                    $data['parent'] = $this->_user->id ;
                    $data['timezone'] = $this->_user->tz ;

                    try {
                        $DLuser->createSubaccount($data);
                        $users = $DLuser->findFirstByUsername($data['username']);
                        $DLUserAclAccess = new DLUserAclAccess();
                        $aclObject = $DLUserAclAccess->getByIdParentSubaccount( $users->idp  , false );
                        $generalLibrary = new General();
                        $access = $generalLibrary->setSubaccountDefault($aclObject , $users->id );
                        //TODO :: dont insert subaccount, and module user default = 1

                        $whitelist = new DLUserWhitelistIp();
                        $ip = '*';
                        $whitelist->create( $users->id , $ip);

                        $this->flash->success("subaccount_add_successful");
                        $this->response->redirect($this->_module."/".$this->_controller."/detail/".$users->id."#tab-acl")->send();
                    } catch (\Exception $e) {
//                        //TODO :: remember_to add error log for this function below
                        $this->flash->error($e->getMessage());
                    }

                }


            }


        }

        $view->code = $code;
        $view->subaccounttitle = "subaccount_add" ;
        \Phalcon\Tag::setTitle("Add SubAccount - ".$this->_website->title);
    }

    public function editAction()
    {
        $view = $this->view;
        $previousPage = new GlobalVariable();
        $childId = $this->dispatcher->getParam("id");
        $generalLibrary = new General();

        $DLUser = new DLUser();
        $user = $DLUser->getById($childId);
        if($user->getParent() == $this->_user->getId()){
            if ($this->request->isPost()) {
                $data = $this->request->getPost();

                $DLAccess = new DLUserAclAccess();
                try {
                    $this->db->begin();
//
                    $aclChild = $generalLibrary->getACL( $user->getId() , true );
                    $DLAccess->setACLSubaccountFalse($aclChild);

                    if(!is_null($data['acl']))
                    $result = $generalLibrary->editSubaccountACL($data['acl'] , $user->getParent() , $user->getId()) ;

                    $this->db->commit();
                    $this->flash->success("subaccount_edit_acl_success");
                    $this->response->redirect($this->_module."/".$this->_controller."/detail/".$user->getId());
                } catch (\Exception $e) {
                    $this->db->rollback();
                    $this->flash->error($e->getMessage());
                }

            } else {
//                $aclParent = $generalLibrary->getACL($this->_user->getId() , $this->_user->getParent() );
                $aclParent = $generalLibrary->getSubaccountACLParent($this->_user->getId() , true );
                $aclParent = $generalLibrary->filterACLlistSubaccount($aclParent);

                $aclChild = $generalLibrary->getACL( $user->getId() );
                $aclChild = $generalLibrary->filterACLsubaccountParentId($aclChild) ;

                $view->childuser = $user ;
                $view->aclParent = $aclParent ;
                $view->aclChild = $aclChild ;
            }
        } else {
            $this->errorFlash($this->_translate['cannot_access']);
            $this->response->redirect($previousPage->previousPage());
        }



        $view->status = GlobalVariable::$threeLayerStatus;
        \Phalcon\Tag::setTitle("Edit SubAccount - ".$this->_website->title);
    }

    public function detailAction(){
        $view = $this->view;
        $previousPage = new GlobalVariable();
        $childId = $this->dispatcher->getParam("id");

        $DLUser = new DLUser();
        $user = $DLUser->getById($childId);

        if($user->idp == $this->_user->id ){

            $DLUserAclAccess = new DLUserAclAccess();
            $generalLibrary = new General();
            $aclParent = $DLUserAclAccess->getByIdParentSubaccount( $this->_user->id , true );
            $aclParent = $generalLibrary->filterACLlistSubaccount( $aclParent );

            $aclChild = $generalLibrary->getACL( $user->id );
            $aclChild = $generalLibrary->filterACLsubaccountParentId( $aclChild ) ;

            $view->childuser = $user ;
            $view->aclParent = $aclParent ;
            $view->aclChild = $aclChild ;

        } else {
            $this->errorFlash($this->_translate['cannot_access']);
            $this->response->redirect($previousPage->previousPage());
        }


        $view->status = GlobalVariable::$threeLayerStatus;
        \Phalcon\Tag::setTitle("Detail SubAccount - ".$this->_website->title);
    }

    public function statusAction(){
        //TODO: FIX THIS , redirect from basecontroller still run the code below this
        if($this->_allowed == 0 ) return $this->response->redirect($this->_module . "/" . $this->_controller . "/")->send();
        $previousPage = new GlobalVariable();
        $currentId = $this->dispatcher->getParam("id");


        $currentId = explode("|",$currentId);
        $id = $currentId[0];
        $status = $currentId[1];

        $DLUser = new DLUser();
        $user = $DLUser->getById($id);
        if(!isset($currentId) || !$user){
            $this->flash->error("user_not_exist");
            $this->response->redirect($this->_module."/".$this->_controller."/");
        }

        try {
            $DLUser->setStatus($user->id , $status);

            $this->flash->success("status_changed");
            $this->response->redirect($previousPage->previousPage());
        } catch (\Exception $e) {
            $this->flash->error($e->getMessage());
        }
 
    }


}
