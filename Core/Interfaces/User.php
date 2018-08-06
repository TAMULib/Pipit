<?php
namespace Core\Interfaces;

/**
*   An interface defining a User
*   Pipit Core doesn't really need to know anything specific about a User class beyond its existence, but defining this interface helps guarantee consistency between client app User implementations.
*
*   @author Jason Savell <jsavell@library.tamu.edu>
*/
interface User {
    /**
    *   Ends a logged in User's session
    *   @return boolean True on success, false on failure
    */
    public function logOut();

    /**
    *   Checks if the user has a session
    *   @return boolean True if logged in, false if not
    */
    public function isLoggedIn();

    /**
    *   Log in a User
    *   @param string $username The User's username
    *   @param string $password The User's password
    *   @return boolean True on successful login, false on anything else
    */
    public function logIn($username,$password);

    /**
    *   Retrieves a particular profile value from the User's profile
    *   @param string $field The name of the profile value to retrieve
    *   @return mixed The value of the profile $field, null if the $field is not present on the profile
    */
    public function getProfileValue($field);

    /**
    *   Returns the User's profile
    *   @return mixed[]
    */
    public function getProfile();

    /**
    *   Checks if the User is in the administrator class
    *   @return boolean
    */
    public function isAdmin();
}
