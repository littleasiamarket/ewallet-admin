<?php
namespace Backoffice\Game\Controllers;

use System\Datalayer\DLCurrency;
use System\Datalayer\DLGame;
use System\Datalayer\DLGameCurrency;
use System\Datalayer\DLProviderGame;
use System\Library\General\GlobalVariable;

class SubController extends \Backoffice\Controllers\ProtectedController
{
    protected $_categoryType = 1;
    protected $_mainType = 2;
    protected $_type = 3;
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
        $subGame = new DLGame();
        $status = GlobalVariable::$threeLayerStatus;
        $sub = $subGame->getAll($this->_type);

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $sub,
                "limit"=> $limit,
                "page" => $pages
            )
        );
        $page = $paginator->getPaginate();

        $pagination = ceil($sub->count()/$limit);
        $view->page = $page->items;
        $view->pagination = $pagination;
        $view->pages = $pages;
        $view->limit = $limit;

        $view->sub = $sub;
        $view->status = $status;

        \Phalcon\Tag::setTitle("Game Category - ".$this->_website->title);
    }

    public function addAction()
    {
        $view = $this->view;

        $DLProviderGame = new DLProviderGame();
        $providerGame = $DLProviderGame->getAll(1);
        $DLGame = new DLGame();
        $categoryGame = $DLGame->getAll($this->_categoryType);
        $mainGame = $DLGame->getAll($this->_mainType);

        if ($this->request->getPost()) {
            try {
                $this->db->begin();
                $data = $this->request->getPost();

                $data['type'] = $this->_type;

                $module = $this->router->getModuleName();
                $controller = $this->router->getControllerName();

                $filterData = $DLGame->filterSubInput($data);
                $DLGame->validateSubAdd($filterData);
                $game = $DLGame->createSub($filterData);

                if($game && $filterData['parent_currency'] == 1){
                    $gameCurrency = new DLGameCurrency();
                    $gameCurrency->setFromParent($game->getGameParent(),$game->getId());
                }

                $this->db->commit();

                $this->flash->success('sub_game_create_success');
                return $this->response->redirect("/".$module."/".$controller."/detail/".$game->getCode())->send();
            } catch (\Exception $e) {
                $this->db->rollback();
                $this->flash->error($e->getMessage());
            }
        }

        $view->providerGame = $providerGame;
        $view->categoryGame = $categoryGame;
        $view->mainGame = $mainGame;
        \Phalcon\Tag::setTitle("Add New Main Game - ".$this->_website->title);
    }

    public function editAction()
    {
        $view = $this->view;

        $currentCode = $this->dispatcher->getParam("code");
        $module = $this->router->getModuleName();
        $controller = $this->router->getControllerName();

        $DLGame = new DLGame();
        $game = $DLGame->getByCode($currentCode, $this->_type);

        if ($this->request->getPost()) {
            try {
                $this->db->begin();

                $data = $this->request->getPost();
                $data['main_code'] = $currentCode;
                $data['type'] = $this->_type;
                $data['id'] = $game->getId();

                $filterData = $DLGame->filterMainInput($data);
                $DLGame->validateMainEdit($filterData);
                $DLGame->setMain($filterData);

                $this->db->commit();

                $this->flash->success('main_game_update_success');
                return $this->response->redirect("/".$module."/".$controller."/detail/".$game->getCode())->send();
            } catch (\Exception $e) {
                $this->db->rollback();
                $this->flash->error($e->getMessage());
            }
        }
        $view->game = $game;

        \Phalcon\Tag::setTitle("Update Game Provider - ".$this->_website->title);
    }

    public function detailAction()
    {
        $view = $this->view;

        $currentCode = $this->dispatcher->getParam("code");
        $module = $this->router->getModuleName();
        $controller = $this->router->getControllerName();

        $status = GlobalVariable::$threeLayerStatus;

        $DLCurrency = new DLCurrency();
        $currency = $DLCurrency->getAllByStatus(1);

        $DLGame = new DLGame();
        $game = $DLGame->getByCode($currentCode, $this->_type);

        $DLGameCurrency = new DLGameCurrency();
        $gameCurrency = $DLGameCurrency->getAll($game->getId());
        $gameCurrencyData = count($gameCurrency);

        if(!$game){
            $this->flash->error("undefined_game_code");
            return $this->response->redirect("/".$module."/".$controller)->send();
        }

        $view->game = $game;
        $view->status = $status;
        $view->gameCurrencyData = $gameCurrencyData;
        $view->gameCurrency = $gameCurrency;

        \Phalcon\Tag::setTitle("Update Game Provider - ".$this->_website->title);
    }

    public function statusAction()
    {
        $view = $this->view;

        $previousPage = new GlobalVariable();
        $currentId = $this->dispatcher->getParam("id");

        $module = $this->router->getModuleName();
        $controller = $this->router->getControllerName();

        $currentId = explode("|",$currentId);
        $id = $currentId[0];
        $status = $currentId[1];

        $DLGame = new DLGame();
        $game = $DLGame->getById($id);
        if(!isset($currentId) || !$game){
            $this->flash->error("undefined_game");
            $this->response->redirect($module."/".$controller."/")->send();
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

        \Phalcon\Tag::setTitle("Edit Currency - ".$this->_website->title);
    }
}
