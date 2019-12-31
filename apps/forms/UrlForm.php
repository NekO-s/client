<?php

namespace Apps\Forms;

use Apps\Components\Api;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

class UrlForm extends Form
{
    public function initialize()
    {
        $url = new Text('url');
        $url->addValidator(new \Phalcon\Validation\Validator\Url([
            'model' => $this,
            'message' => 'Укажите верный url.'
        ]));
        $url->addValidator(new \Phalcon\Validation\Validator\StringLength(
            [
                'max' => 150,
                'min' => 10,
                'messageMaximum' => "Слишком длинная ссылка",
                'messageMinimum' => "Слишком короткая ссылка",
                'includedMaximum' => true,
                'includedMinimum' => false,
            ]
        ));
        $this->add($url);
    }

    public function save() :?string
    {
        if (!$this->isValid($this->request->getPost())) {

            /** @var \Phalcon\Validation\Message $message */
            $message = $this->getMessages()[0];

            $this->flash->message($message->getField(), $message);

            return null;
        }

        // api request

        /** @var Api $api */
        $api = $this->getDI()->getShared('api');

        $result = $api->create($this->get('url')->getValue());

        if ($result['status']=='error') {

            $this->flash->message('url', $result['error']);
            return null;
        }

        return $result['url'];
    }
}