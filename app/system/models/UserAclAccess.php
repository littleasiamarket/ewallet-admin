<?php

namespace System\Model;

class UserAclAccess extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var integer
     */
    protected $user;

    /**
     *
     * @var integer
     */
    protected $parent;

    /**
     *
     * @var string
     */
    protected $module;

    /**
     *
     * @var string
     */
    protected $controller;

    /**
     *
     * @var string
     */
    protected $action;

    /**
     *
     * @var integer
     */
    protected $sidebar;

    /**
     *
     * @var string
     */
    protected $sidebar_name;

    /**
     *
     * @var string
     */
    protected $sidebar_icon;

    /**
     *
     * @var integer
     */
    protected $sidebar_order;

    /**
     *
     * @var integer
     */
    protected $status;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field user
     *
     * @param integer $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Method to set the value of field parent
     *
     * @param integer $parent
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Method to set the value of field module
     *
     * @param string $module
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Method to set the value of field controller
     *
     * @param string $controller
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Method to set the value of field action
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Method to set the value of field sidebar
     *
     * @param integer $sidebar
     * @return $this
     */
    public function setSidebar($sidebar)
    {
        $this->sidebar = $sidebar;

        return $this;
    }

    /**
     * Method to set the value of field sidebar_name
     *
     * @param string $sidebar_name
     * @return $this
     */
    public function setSidebarName($sidebar_name)
    {
        $this->sidebar_name = $sidebar_name;

        return $this;
    }

    /**
     * Method to set the value of field sidebar_icon
     *
     * @param string $sidebar_icon
     * @return $this
     */
    public function setSidebarIcon($sidebar_icon)
    {
        $this->sidebar_icon = $sidebar_icon;

        return $this;
    }

    /**
     * Method to set the value of field sidebar_order
     *
     * @param integer $sidebar_order
     * @return $this
     */
    public function setSidebarOrder($sidebar_order)
    {
        $this->sidebar_order = $sidebar_order;

        return $this;
    }

    /**
     * Method to set the value of field status
     *
     * @param integer $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the value of field parent
     *
     * @return integer
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns the value of field module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Returns the value of field controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns the value of field action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Returns the value of field sidebar
     *
     * @return integer
     */
    public function getSidebar()
    {
        return $this->sidebar;
    }

    /**
     * Returns the value of field sidebar_name
     *
     * @return string
     */
    public function getSidebarName()
    {
        return $this->sidebar_name;
    }

    /**
     * Returns the value of field sidebar_icon
     *
     * @return string
     */
    public function getSidebarIcon()
    {
        return $this->sidebar_icon;
    }

    /**
     * Returns the value of field sidebar_order
     *
     * @return integer
     */
    public function getSidebarOrder()
    {
        return $this->sidebar_order;
    }

    /**
     * Returns the value of field status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("user_acl_access");
        $this->belongsTo('user', 'System\Model\User', 'id', ['alias' => 'User']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user_acl_access';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserAclAccess[]|UserAclAccess|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return UserAclAccess|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
