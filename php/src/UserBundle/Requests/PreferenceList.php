<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 29-6-16 10:05
 */

namespace UserBundle\Requests;

use JMS\Serializer\Annotation\Type;

class PreferenceList
{
	/**
	 * @var array
	 * @Type("array<UserBundle\Requests\Preference>")
	 */
	protected $data;
}