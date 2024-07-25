<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\IncomingRequest;

/**
 * Este filtro sirve para comprobar si la sesión aun no se cerro por+
 * exceder el limite de tiempo, para así evitar errores, sobre todo al guardar datos
 */
class CheckUserSession implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
		assert($request instanceof IncomingRequest);
        $session = session();

		if (!$session->has('userid'))
		{
			$session->destroy();			
			$response = service('response');
            $response->setHeader('Location', base_url('login'));
            $response->setStatusCode(302);
            
            return $response->send();
		}
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}