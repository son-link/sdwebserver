<?php

namespace App\Controllers;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use App\Models\UsersModel;
use CodeIgniter\API\ResponseTrait;

class Register extends BaseController
{
    protected $users;
	protected $captcha;
	use ResponseTrait;

	public function __construct()
	{
		$this->captcha = new CaptchaBuilder;
		session_start();
	}

    public function index()
    {
		$this->captcha->build();
		$_SESSION['phrase'] = $this->captcha->getPhrase();	
		echo get_header('Register');
		echo view('register', ['captcha' => $this->captcha->inline()]);
		echo get_footer(['register.js']);
    }

	public function newuser()
	{
		$data = $this->request->getVar();
		$response = [
			'ok'	=> false,
			'msg'	=> ''
		];

		// First verify the captcha
		if (isset($_SESSION['phrase']) && PhraseBuilder::comparePhrases($_SESSION['phrase'], $data['phrase'])) {
            // Move the image
			$file = $this->request->getFile('imginput');
			if ($file) {
				// Verify is the file is correct and not, for example, a .exe renamed to .jpg
				$ext = $file->guessExtension();

				if ($ext != $file->getExtension()) $response['msg'] = 'The image is not valid';
				else
				{
					$users = new UsersModel();
					// Now we check if the user and/or email is already in use.
					if ($users->compUser($data['username'], $data['email']))
						$response['msg'] = 'Username and/or email is already in use';
					else $response = $users->addUser($data, $file);
				}
			}
        }
		else
		{
           $response['msg'] = 'Captcha not valid';
        }
		return $this->respond($response);
	}

	public function new_captcha()
	{
		$this->captcha->build();
		$_SESSION['phrase'] = $this->captcha->getPhrase();	
		return $this->captcha->inline();
	}

	public function ok()
	{
		echo get_header('Register');
		echo view('register_ok');
		echo get_footer();
	}
}