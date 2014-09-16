<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * [$fillable description]
	 * @var [type]
	 */
    protected $fillable = [
        'username', 'password', 'email', 'remember_token'
    ];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	/**
     * Find by username, or throw an exception.
     *
     * @param string $username The username.
     * @param mixed $columns The columns to return.
     *
     * @throws ModelNotFoundException if no matching User exists.
     *
     * @return User
     */
    public static function findByUsernameOrFail($username, $columns = array('*'))
    {
        if ( ! is_null($user = static::whereUsername($username)->first($columns))) {
            return $user;
        }

        throw new ModelNotFoundException;
    }

    /**
     * Find by email, or throw an exception.
     *
     * @param string $email The email.
     * @param mixed $columns The columns to return.
     *
     * @throws ModelNotFoundException if no matching User exists.
     *
     * @return User
     */
    public static function findByEmailOrFail($email, $columns = array('*'))
    {
        if ( ! is_null($user = static::whereEmail($email)->first($columns))) {
            return $user;
        }

        throw new ModelNotFoundException;
    }

	public function barber()
	{
		return $this->hasOne('Barber');
	}

	public function client()
	{
		return $this->hasOne('Client');
	}
}
