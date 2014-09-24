<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
     * This variable specifies which attributes should be guarded against mass-assignable.
     * @var array
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
     * Find user information by username, or throw an exception.
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
        if ( ! is_null($userInformation = static::whereUsername($username)->first($columns))) {
            return $userInformation;
        }
        return false;
    }

    /**
     * Find user information by email, or throw an exception.
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
        if ( ! is_null($userInformation = static::whereEmail($email)->first($columns))) {
            return $userInformation;
        }
        return false;
    }

    /**
     * Find user information by token, or throw an exception.
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
        if (!is_null($userInformation = static::where('access_token', '=', $token)->first($columns))) {
            return $userInformation;
        }
        return false;
    }

    /**
     * Find user information by token and username, or throw an exception.
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
        if (!is_null($userInformation = static::whereUsername($username)->where('access_token', '=', $token)->first($columns))) {
            return $userInformation;
        }
        return false;
    }

    /**
     * Scopes allow you to easily re-use query logic in your models.
     * This function defines a scope that accepts parameters
     * @param  [type] $query [description]
     * @param  [type] $type  [description]
     * @return [type]        [description]
     */
	public function scopeOfType($query, $type)
    {
        return $query->whereType($type);
    }
}