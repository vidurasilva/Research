<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/13/16
 * Time: 2:56 PM
 */

namespace ApiBundle\Entity;


interface BasePathAware
{
	public function setBasePath($basePath);
}