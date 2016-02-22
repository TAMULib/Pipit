<?php
namespace TAMU\Seed\Interfaces;
/** 
*	An interface defining a DatabaseRepository
*	DatabaseRepositories are utilized by controllers to perform CRUD actions on Databases
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface DatabseRepository {
	public function get();
	public function getById($id);
	public function removeById($id);
	public function search($data);
	public function add($data);
	public function update($data);
}
?>