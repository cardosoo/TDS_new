<?php
namespace zeroU;

class Authenticate extends \base\App {

    protected function forceMoi($id){
        $app = \TDS\App::get();
                
        $this->data = new \stdClass();
        $user = $app::load('Personne',$id);
        $this->data->uid = $user->uid;
        $this->data->method = 'Force';
        $this->data->displayname = 'Force uid';
    }


}
