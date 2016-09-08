<?php
namespace Df\Api\Google\Fonts;
use Df\Api\Google\Font;
use Df\Api\Google\Font\Variant;
use Df\Api\Google\Font\Variant\Preview;
use Df\Api\Google\Font\Variant\Preview\Params;
use Df\Api\Google\Fonts;
class Sprite extends Png {
	/**
	 * 2015-12-08
	 * Возвращает координаты левого верхнего угла изображения шрифта в общей картинке-спрайте.
	 * Клиентская часть затем использует эти координаты в правиле CSS background-position:
	 * https://developer.mozilla.org/en-US/docs/Web/CSS/background-position
	 * https://developer.mozilla.org/en-US/docs/Web/CSS/position_value
	 * Обратите внимание, что размеры изображения шрифта мы клиентской части не передаём,
	 * потому что клиентская часть сама передала их нам и знает их.
	 * @param Preview $preview
	 * @return int[]
	 */
	public function datumPoint(Preview $preview) {
		return df_a($this->datumPoints(), $preview->getId());
	}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\Api\Google\Fonts\Png::draw()
	 * @used-by \Df\Api\Google\Fonts\Png::image()
	 * @param resource $image
	 * @return void
	 */
	protected function draw($image) {
		/** @var int $x */
		$x = 0;
		/** @var int $y */
		$y = 0;
		$this->_datumPoints = [];
		/** @var int|bool|resource $r */
		$r = imagefill($image, 0, 0, $this->colorAllocateAlpha($image, $this->bgColor()));
		df_assert($r);
		// http://stackoverflow.com/a/1397584/254475
		imagealphablending($image, true);
		foreach ($this->previews() as $preview) {
			/** @var Preview $preview */
			try {
				/** @var resource $previewImage */
				$previewImage = imagecreatefromstring($preview->contents());
				df_assert($previewImage);
				try {
					$r = imagecopy(
						$image, $previewImage, $x, $y, 0, 0, $preview->width(), $preview->height()
					);
					df_assert($r);
					$this->_datumPoints[$preview->getId()] = [$x, $y];
				}
				finally {
					imagedestroy($previewImage);
				}
			}
			catch (\Exception $e) {
				df_log($e->getMessage());
			}
			$x += $this->previewWidth();
			if ($x >= $this->width()) {
				$x = 0;
				$y += $this->previewHeight() + $this->marginY();
			}
		}
		df_media_write($this->pathToDatumPoints(), df_json_encode($this->_datumPoints));
	}

	/**
	 * 2015-12-08
	 * @override
	 * Высота спрайта.
	 * @see \Df\Api\Google\Fonts\Png::height()
	 * @used-by \Df\Api\Google\Fonts\Png::image()
	 * @return int
	 */
	protected function height() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2015-12-10
			 * Результат $this->square() / $this->width() может быть дробным (например: 3796.1538461538),
			 * т.к. мы уже провели много замысловатых операций.
			 * Поэтому для приведения результату к целому типу надло использоват именно @uses ceil(),
			 * иначе последнему ряду спрайта не хватит места.
			 *
			 * Также надо учитывать, что последний ряд почти наверняка будет заполнен не полностью,
			 * а наш алгоритм вычисления площади исходит из суммы площадей миниатюр,
			 * т.е. считает, что последний ряд заполнен полностью.
			 * Поэтому добавляем место для ещё одого ряда снизу,
			 * чтобы последнему ряду уж наверняка хватило места.
			 */
			$this->{__METHOD__} =
				ceil($this->square() / $this->width())
				+ $this->previewHeight() + $this->marginY()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * Кэшировать результат нельзя!
	 * @override
	 * @see \Df\Api\Google\Fonts\Png::needToCreate()
	 * @used-by \Df\Api\Google\Fonts\Png::createIfNeeded()
	 * @return bool
	 */
	protected function needToCreate() {
		return !file_exists($this->pathToDatumPoints()) || parent::needToCreate();
	}

	/**
	 * 2015-12-08
	 * @override
	 * @see \Df\Api\Google\Fonts\Png::pathRelativeA()
	 * @used-by \Df\Api\Google\Fonts\Png::path()
	 * @return string[]
	 */
	protected function pathRelativeA() {
		return [$this->pathRelativeBase(), $this->fs()->namePng(['i'])];
	}

	/**
	 * 2015-12-08
	 * @override
	 * Ширина спрайта.
	 * Изначально я делал ширину равной максимальной из ширин картинок спрайта
	 * (а картинки по сути были одинаковой ширины).
	 * В таком случае спрайт получается узкий по ширине и длинный по высоте.
	 * Намного удобнее его смотреть (ну, для тестирования),
	 * когда его ширина и высота примерно равны друг другу,
	 * поэтому чуть переделал алгоритм.
	 * @see \Df\Api\Google\Fonts\Png::width()
	 * @used-by \Df\Api\Google\Fonts\Png::image()
	 * @return int
	 */
	protected function width() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->numPreviewsInARow() * $this->previewWidth();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * @return array(string => int[])
	 */
	private function datumPoints() {
		if (!$this->_datumPoints) {
			if (file_exists($this->pathToDatumPoints())) {
				try {
					$this->_datumPoints = df_json_decode(df_media_read($this->pathToDatumPoints()));
				}
				catch (\Exception $e) {
					df_log($e->getMessage());
				}
			}
			if (!$this->_datumPoints) {
				$this->create();
				df_assert_array($this->_datumPoints);
			}
		}
		return $this->_datumPoints;
	}

	/** @return Fonts */
	private function fonts() {return $this[self::$P__FONTS];}

	/**
	 * 2015-12-10
	 * Межстрочное расстояние.
	 * Текст на миниатюре прижат к нижнему краю холста.
	 * Бывает, что в браузере нам требуется сдвинуть текст чуть вверх.
	 * Конечно, это в простом случае можно сделать средствами CSS,
	 * однако хорошо иметь и возможность двигать холст.
	 * @return int
	 */
	private function marginY() {return 4;}

	/**
	 * 2015-12-08
	 * Количество картинок в одном горизонтальном ряду спрайта.
	 * @return int
	 */
	private function numPreviewsInARow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = ceil(sqrt($this->square()) / $this->previewWidth());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * @return string
	 */
	private function pathRelativeBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cc_path('sprite', df_fs_name(implode('_', [
				$this->fs()->nameResolution(), $this->fs()->nameColorsSizeMargin()
			])));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * @return string
	 */
	private function pathToDatumPoints() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->fs()->absolute([
				$this->pathRelativeBase(), 'datum-points.json'
			]);
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function previewHeight() {return $this->params()->height();}

	/**
	 * 2015-12-08
	 * @return Preview[]
	 */
	private function previews() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = call_user_func_array('array_merge',
				df_map(function(Font $font) {
					return array_map(function(Variant $variant) {
						return $variant->preview();
					}, array_values($font->variantsAvailable()));
				}, $this->fonts())
			);
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function previewWidth() {return $this->params()->width();}

	/**
	 * 2015-12-08
	 * Площадь спрайта: сумм площадей всех картинок спрайта.
	 * @return int
	 */
	private function square() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				($this->previewHeight() + $this->marginY())
				* $this->previewWidth()
				* count($this->previews())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-08
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__FONTS, Fonts::class);
	}
	/** @var string */
	private static $P__FONTS = 'fonts';

	/**
	 * 2015-12-08
	 * @used-by \Df\Api\Google\Fonts\Sprite::datumPoint()
	 * @var array(string => int[])
	 */
	private $_datumPoints = [];

	/**
	 * 2015-12-08
	 * @param Fonts $fonts
	 * @param Params $params
	 * @return \Df\Api\Google\Fonts\Sprite
	 */
	public static function i(Fonts $fonts, Params $params) {return new self([
		self::$P__FONTS => $fonts, self::$P__PARAMS => $params
	]);}
}