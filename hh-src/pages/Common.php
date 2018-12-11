<?hh // strict
use LamIO\Page;
use type LamIO\MDRendererStruct;
use namespace HH\Lib\{C, Str, Vec};
use namespace Facebook\Markdown;
use type LamIO\MDRendererStruct;

abstract class Common implements Page {
	protected MDRendererStruct<string> $renderer_struct;
	
	public function __construct() {
		$this->renderer_struct = LamIO\default_renderer_factory();
	}
	
	const vec<string> COMMON_FONTS = vec[
		"/css/fonts/Adagio_Serif/Adagio_Serif/Adagio_Serif-Regular_italic.css",
		"/css/fonts/Adagio_Serif/Adagio_Serif/Adagio_Serif-Regular.css",
		"/css/fonts/Adagio_Serif/Adagio_Serif_Bold/Adagio_Serif-Bold_italic.css",
		"/css/fonts/Adagio_Serif/Adagio_Serif_Bold/Adagio_Serif-Bold.css",
		// "css/fonts/respublikafyregular/respublikafyregular.css",
		"/css/fonts/TT_Bricks/Bold/TTBricks-Bold.css",
		"/css/fonts/TT_Bricks/EBold/TTBricks-Extrabold.css",
		"/css/fonts/TT_Bricks/Medium/TTBricks-Medium.css"
	];
	public function get_style_deps(): vec<string> {
		return Vec\concat(self::COMMON_FONTS, vec["/css/common.css"]);
	}
	public function get_title(): string { return ''; }
}