<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 26/10/16
 * Time: 10:10
 */

namespace AppBundle\Model;


final class Status
{
	const OPEN   = 'open';
	const DONE   = 'done';
	const FAILED = 'failed';
	const CLOSE  = 'closed';

	public static function getStatus()
	{
		return
			[
				self::OPEN   => self::OPEN,
				self::DONE   => self::DONE,
				self::FAILED => self::FAILED,
				self::CLOSE  => self::CLOSE
			];
	}

}