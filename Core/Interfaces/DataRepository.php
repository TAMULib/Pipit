<?php
namespace Core\Interfaces;

/**
*   An interface defining a DataRepository
*   DataRepositories are utilized to perform CRUD actions on data stores
*
*   @author Jason Savell <jsavell@library.tamu.edu>
*/
interface DataRepository {
    /**
    *   Get all records from a data source
    *
    *   @return mixed[] The results
    */
    public function get();

    /**
    *   Get a single record by its unique ID
    *   @param mixed $id The unique ID
    *   @return mixed The record matching the ID
    */
    public function getById($id);

    /**
    *   Remove a single record by its unique ID
    *   @param mixed $id The unique ID
    *   @return boolean The success or failure of the update operation
    */
    public function removeById($id);

    /**
    *   Get a single record by its unique ID
    *
    *   @param mixed $data The search criteria
    *   @return mixed[] The search results
    *
    */
    public function search($data);

    /**
    *   Add a single record to a data store
    *   @param mixed $data A representation of the record to be added
    *   @return mixed|false The unique ID of the record on success, false on failure
    */
    public function add($data);

    /**
    *   Update a single record in a store by its unique ID
    *   @param mixed $id The unique ID
    *   @param mixed $data The search criteria
    *   @return boolean Success or failure
    */
    public function update($id,$data);
}
?>
