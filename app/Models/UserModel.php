<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $table = 'users';
	protected $primaryKey = 'id';
	protected $DBGroup = 'default';
	protected $allowedFields = ['id', 'google_id', 'username', 'password', 'first_name', 'last_name', 'email', 'status', 'profile_img', 'created_on'];

	function getById($id)
	{
		$query = $this->db->table("users")
			->select('id, google_id, username, password, first_name, last_name, email, phone, company, active, profile_img, last_login, created_on')
			->where('id', $id)
			->orWhere('google_id', $id)
			->get()
			->getRowArray();

		return $query;
	}
}
