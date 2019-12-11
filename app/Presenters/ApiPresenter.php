<?php

namespace App\Presenters;

use Ahc\Jwt\JWT;
use Nette;
use Tracy\Debugger;

class ApiPresenter extends Nette\Application\UI\Presenter
{

    /**
     * @var \DibiConnection $connection
     */
    protected $connection;

    public function getService($name)
    {
        return $this->context->getService($name);
    }

    private function getJwt()
    {
        return new JWT($this->context->parameters['jwt']['secret_token'], 'HS256', 60 * 60 * 24 * 365 * 5, 0);
    }

    public function actionAuth()
    {
        $password = substr($this->getParameter('p'), 0, 20);

        $userModel = $this->getService('UserModel');
        $user = $userModel->findUser($password);

        if (!$user) {
            $this->sendJson(['error' => 'User does not exist']);
        } else {
            $jwt = $this->getJwt();

            $this->sendJson(['token' => $jwt->encode([
                'name' => $user->name,
                'id' => $user->id,
            ])]);
        }

    }


    public function actionSave()
    {

        $params = $this->getHttpRequest()->getPost();

        $jwt = $this->getJwt();
        try {
            $jwtValues = $jwt->decode($params['token']);
            $logModel = $this->getService('LogModel');
            $logId = $logModel->save([
                'time' => new \Dibi\DateTime(),
                'lat' => $params['lat'],
                'lng' => $params['lng'],
                'type' => $params['type'],
                'user_id' => $jwtValues['id']
            ]);


        } catch (\Exception $e) {
            $this->sendJson(['error' => 'Token is not valid']);
        }

        $this->sendJson(['success' => true, 'id' => $logId]);
    }

    public function actionSaveImage()
    {

        $params = $this->getHttpRequest()->getPost();
        $fileUpload = $this->getHttpRequest()->getFile('image');

        $file = $fileUpload->getContents();

        $jwt = $this->getJwt();
        try {
            $jwtValues = $jwt->decode($params['userToken']);
            $logImageModel = $this->getService('LogImageModel');
            $logImageModel->save([
                'log_id' => $params['log_id'],
                'data' => $file,
            ]);


        } catch (\Exception $e) {
            Debugger::log($e);
            $this->sendJson(['error' => 'Token is not valid']);
        }

        $this->sendJson(['success' => true]);
    }

    public function actionSuperRead()
    {
        $this->sendJson(['John' => 'Doe']);
    }
}