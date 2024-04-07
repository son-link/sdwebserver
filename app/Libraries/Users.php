<?php
namespace App\Libraries;
/**
 * Librería para la gestión de los usuarios de la plataforma.
 */
class Users
{
	private $username;
	private $passwd;
	protected $db;
	protected $session;
	const HASH = PASSWORD_DEFAULT;
	const COST = 16;

	/**
	 * Add new to the database
	 */
	public function addUser($data, $image)
	{
		$response = [
			'ok'	=> false,
			'msg'	=> ''
		];

		unset($data['passwordcheck']);
		unset($data['phrase']);
		unset($data['PHPSESSID']);

		// First verify if the user or email
		$sql = $this->db->table('users');
		$sql->where('username', $data['username']);
		$sql->orWhere('email', $data['email']);
		$query = $sql->get(1);
		if ($query && $query->getNumRows() == 1){
			$response['msg'] = 'Username and/or email are registered';
		}
		else
		{
			// Encrypt the password
			$data['password'] = password_hash($data['password'], self::HASH, [self::COST]);
			
			// Insert data
			$sql->insert($data);
			$error = $this->db->error();
			if ($error['code'] != 0)
			{
				$resp['msg'] = 'An error ocurred on insert the new user to the database';
			}
			else
			{
				$id = $this->db->insertID();
				$sql->resetQuery();
				// Move the file to it's new home
				$filename =  $data['username'] . '.' . $image->getExtension();
				$image->move(FCPATH . '/img/users/', $filename);
				$sql->where('id', $id)->update(['img' => $filename]);
				$response['ok'] = true;
			}
		}

		return $response;
	}
}