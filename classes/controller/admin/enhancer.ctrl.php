<?php
namespace Nos\Form;

class Controller_Admin_Enhancer extends \Nos\Controller
{
    public function action_preview()
    {
        return $this->action_save();
    }

    public function action_popup()
    {
        return \View::forge($this->config['views']['popup']);
    }

    public function action_save()
    {
        $body = array(
            'config'  => \Format::forge()->to_json($_POST),
            'preview' => \View::forge($this->config['views']['preview'], $_POST)->render(),
        );
        \Response::json($body);
    }a
}
