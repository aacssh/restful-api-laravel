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
    protected $guarded = [
        'id', 'password', 'access_token', 'password_temp', 'code'
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
        return false;
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
        return false;
    }

    /**
     * Find by token, or throw an exception.
     *
     * @param string $token The token.
     * @param mixed $columns The columns to return.
     *
     * @throws ModelNotFoundException if no matching User exists.
     *
     * @return User
     */
    public static function findByTokenOrFail($token, $columns = array('*'))
    {
        if (!is_null($user = static::where('access_token', '=', $token)->first($columns))) {
            return $user;
        }
        return false;
    }

    /**
     * Find by token, or throw an exception.
     *
     * @param string $token The token.
     * @param mixed $columns The columns to return.
     *
     * @throws ModelNotFoundException if no matching User exists.
     *
     * @return User
     */
    public static function findByTokenAndUsernameOrFail($token, $username, $columns = array('*'))
    {
        if (!is_null($user = static::whereUsername($username)->where('access_token', '=', $token)->first($columns))) {
            return $user;
        }
        return false;
    }

	public function scopeOfType($query, $type)
    {
        return $query->whereType($type);
    }
}