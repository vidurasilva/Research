<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 29-6-16 10:02
 */

namespace UserBundle\Requests;

use JMS\Serializer\Annotation\Type;

class Preference
{
	/**
	 * @var boolean
	 * @Type("boolean")
	 */
	protected $goalStream;

	/**
	 * @var boolean
	 * @Type("boolean")
	 */
	protected $goalPush;

	/**
	 * @var boolean
	 * @Type("boolean")
	 */
	protected $summaryStream;

	/**
	 * @var boolean
	 * @Type("boolean")
	 */
	protected $summaryPush;

	/**
	 * @var boolean
	 * @Type("boolean")
	 */
	protected $liveStream;

	/**
	 * @var boolean
	 * @Type("boolean")
	 */
	protected $livePush;

	/**
	 * @Type("integer")
	 * @var integer
	 */
	protected $team;

	/**
	 * @Type("boolean")
	 * @var boolean
	 */
	protected $member;
}