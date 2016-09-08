<?php
namespace Df\Api\Google\Fonts;
use Df\Api\Google\Font\Variant\Preview\Params;
class Fs extends \Df\Core\O {
	/**
	 * 2015-12-08
	 * @param string[] $relativeParts
	 * @return string
	 */
	public function absolute(array $relativeParts) {
		return $this->baseAbsolute() . df_cc_path($relativeParts);
	}

	/**
	 * 2015-12-08
	 * @return string
	 */
	public function nameColorsSizeMargin() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode('_', [
				's' . df_pad0(2, $this->params()->fontSize())
				, 'f' . implode('-', $this->params()->fontColor())
				, 'b' . implode('-', $this->params()->bgColor())
				, 'm' . $this->params()->marginLeft()
			]);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * @param string[] $params
	 * @return string
	 */
	public function namePng(array $params) {return df_fs_name(implode('_', $params)) . '.png';}

	/**
	 * 2015-12-08
	 * @return string
	 */
	public function nameResolution() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode('x', [$this->params()->width(), $this->params()->height()]);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function baseAbsolute() {return df_media_path_absolute(self::baseRelative());}

	/** @return string */
	private function baseRelative() {return df_cc_path('df', 'api', 'google', 'fonts') . '/';}

	/** @return Params */
	private function params() {return $this[self::$P__PARAMS];}

	/**
	 * 2015-12-08
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PARAMS, Params::class);
	}

	/** @var string */
	private static $P__PARAMS = 'params';

	/** @return $this */
	public static function s() {
		static $r; return $r ? $r : $r = new self([self::$P__PARAMS => Params::fromRequest()]);
	}
}