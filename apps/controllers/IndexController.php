<?php

namespace Apps\Controllers;

use Apps\Forms\UrlForm;
use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $url = null;

        $form = new UrlForm();

        if ($this->request->isPost()) {

            $url = $form->save();
        }

        $this->view->setVar('url', $url);
        $this->view->setVar('form', $form);
    }
}
