<?hh // strict
use LamIO\Page;
use namespace LamIO\CMS;
use namespace HH\Lib\{C, Str, Vec};
use namespace Facebook\Markdown;
class ShadowProject extends Common {
	<<__Override>>
	public function __construct(protected CMS\PostBag $post) {
		parent::__construct();
	}
	<<__Override>>
	public function get_style_deps(): vec<string> {
		return Vec\concat(parent::get_style_deps(), vec[
			"/css/project-page.css",
			"/css/plain-nav.css",
			"/css/vendor/highlight/solarized-light.css"
		]);
	}
	public function get_js_deps(): vec<string> {
		return vec[
			"/js/vendor/highlight/highlight.pack.js",
			"//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.5/MathJax.js?config=TeX-AMS_CHTML",
			"/js/index.project_page.js",
			"/js/mathjax-config.js"
			 
		];
	}
	<<__Memoize>>
	public function render_body(): \XHPRoot {
		$ast_title = LamIO\CMS\title_from_AST($this->post['content']);
		$content_offset = 0;
		$title = 'Untitled';
		if($ast_title != null) {
			$title = new \MarkdownRenderable($this->renderer_struct, vec[$ast_title]);
			$content_offset = 1;
		}
		
		$x_hero = null;
		/* HH_IGNORE_ERROR[2050] */
		$page_dir = __DIR__ . '/../../public/project_assets' . substr($_SERVER['REQUEST_URI'], strlen('/projects'));
		/* HH_IGNORE_ERROR[2050] */
		if(\file_exists("{$page_dir}/hero/index.html")) // not great this location is constant but whatever
			$x_hero = <section id="hero">
				<iframe src="./hero/index.html" />
			</section>;
		elseif($this->post['thumb'] !== null)
			$x_hero = <section id="hero" style={"background-image:url({$this->post['hero']});"} />;
		
		return <x:frag>
			<header>
				<a href="#main_content" id="skip_to_main">Skip to main content</a>
				<a id="top"></a>
				<ui:nav />
				<div id="sandbox"></div>
			</header>
			{$x_hero}
			<section id="main_content" class="group">
				<header class="group">
					<h1>{$title}</h1>
					<ul class="horz toc" id="principal_toc"></ul>
				</header>
				<section id="principal_pane">
					{new \MarkdownRenderable($this->renderer_struct, Vec\slice($this->post['content']->getChildren(), $content_offset))}
				</section>
				<section class="aux-pane">
					
				</section>
				<section class="aux-pane">
					
				</section>
			</section>
			<ui:footer />
		</x:frag>;
	}
}