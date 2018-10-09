<?php
namespace Backoffice\User\Controllers;

use \System\Datalayer\DLUser;
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
        $user = $DLuser->getChildById($this->_user->getId());

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $user,
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

        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $data['username'] = \filter_var(\strip_tags(\addslashes($data['username'])), FILTER_SANITIZE_STRING);
            $data['password'] = \filter_var(\strip_tags(\addslashes($data['password'])), FILTER_SANITIZE_STRING);

            $validation = new Validation();
            $validation->addCondition("Username", $data['username'] , "format", "username", 5 , 15  );
            $validation->addCondition("Password", $data['password'], "format", "password");
            $validation->execute();
            if ($validation->_valid == false) {
                foreach ($validation->_messages as $fieldName => $messages) {
                    foreach ($messages as $message) {
                        $this->errorFlash($message);
                    }
                }
            } else {
                $DLuser = new DLUser();
                $checknick = $DLuser->checkNickname($data['username']);

                if($checknick){
                    $this->errorFlash("nickname_already_used");
                } else {
                    $securityLibrary = new SecurityUser();
                    $data['password'] = $securityLibrary->enc_str($data['password']);
                    $data['parent'] = $this->_user->getId();
                    $data['timezone'] = $this->_user->getTimezone();
                    // TODO :: change password manual
                    $user = $DLuser->createSubaccount($data);

                    echo "<pre>";
                    var_dump($user);
                    die;


                    if($user){

                        $this->successFlash($this->_translate['new_subaccount_success']);
                        return $this->response->redirect("/");
                    } else {
                        //TODO :: remember_to add error log for this function below
//                        \error_log('USER_UPDATE_PASSWD', 'username', $this->_user->getUsername(), 'oldpass', '' . $data['password'] . '', '', '');
                        $this->errorFlash($this->_translate['new_subaccount_failed']);
                    }




                }



            }









        }

        \Phalcon\Tag::setTitle("Add SubAccount - ".$this->_website->title);
    }

    public function editAction()
    {
        $view = $this->view;


        \Phalcon\Tag::setTitle("Edit SubAccount - ".$this->_website->title);
    }

    public function detailAction(){

    }

    public function statusAction(){
        $previousPage = new GlobalVariable();
        $currentId = $this->dispatcher->getParam("id");

        $currentId = explode("|",$currentId);
        $id = $currentId[0];
        $status = $currentId[1];

        $DLGame = new DLGame();
        $game = $DLGame->getById($id);
        if(!isset($currentId) || !$game){
            $this->flash->error("undefined_game");
            $this->response->redirect($this->_module."/".$this->_module."/")->send();
        }

        try {
            $this->db->begin();

            $DLGame->setStatus($id,$status);

            $this->db->commit();
            $this->flash->success("status_changed");
            $this->response->redirect($previousPage->previousPage())->send();
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->flash->error($e->getMessage());
        }
 
    }
}