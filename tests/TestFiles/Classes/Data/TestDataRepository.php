<?php
namespace TestFiles\Classes\Data;
use Pipit\Interfaces as Interfaces;

class TestDataRepository implements Interfaces\DataRepository, Interfaces\Configurable {
	public function get() {
        return [];
    }

	/**
	* 	Get a single record by its unique ID
	*	@param mixed $id The unique ID
	*	@return mixed The record matching the ID
	*/
	public function getById($id) {
        return [];
    }

	/**
	* 	Remove a single record by its unique ID
	*	@param mixed $id The unique ID
	*	@return boolean The success or failure of the update operation
	*/
	public function removeById($id) {
        return true;
    }

	/**
	* 	Get a single record by its unique ID
	*
	*	@param mixed $data The search criteria
	*	@return mixed[] The search results
	*	
	*/
	public function search($data) {
        return [];
    }

	/**
	* 	Find a single record by field/value search criteria
	*
	*	@param array<string,string> $data The search criteria as field/value pairs
	*	@return mixed[] The search results
	*	
	*/
	public function searchAdvanced($data) {
        return [];
    }

	/**
	* 	Add a single record to a data store
	*	@param mixed $data A representation of the record to be added
	*	@return mixed|false The unique ID of the record on success, false on failure
	*/
	public function add($data) {
        return 1;
    }

	/**
	* 	Update a single record in a store by its unique ID
	*	@param mixed $id The unique ID
	*	@param mixed $data The search criteria
	*	@return boolean Success or failure
	*/
	public function update($id,$data) {
        return true;
    }

    public function configure($site) {
    }
}
?>