<?hh // strict
use LamIO\Page;
use namespace LamIO\CMS;
use namespace HH\Lib\{C, Str, Vec, Keyset};
use namespace Facebook\Markdown;
class Projects extends Common {
	<<__Override>>
	public function get_style_deps(): vec<string> {
		return Vec\concat(parent::get_style_deps(), vec[
			"/css/projects.css",
			"/css/plain-nav.css",
		]);
	}
	public function get_js_deps(): vec<string> {
		return vec[
			"js/index.projects.js",
		];
	}
	<<__Override>>
	public function get_title(): string {
		return 'Projects - Derek Lam';
	}
	<<__Memoize>>
	public function render_body(): \XHPRoot {
		// Note: most of this is self-plagiarized from Writing.php
		$content = LamIO\CMS\content_iterator($this->renderer_struct, __DIR__ . "/../../public/project_assets")
			|> Vec\sort($$, ($a, $b) ==> $b['mtime'] - $a['mtime']);
		return <x:frag>
			<header>
				<a href="#main_content" id="skip_to_main">Skip to main content</a>
				<a id="top"></a>
				<ui:nav />
				<div id="sandbox"></div>
			</header>
			<section id="main_content" class="group">
				<header id="principal_header">
					<h1>Projects</h1>
					<p>Past jobs here too, provided that I have permission to show them.</p>
				</header>
				<section id="main_body">
					<nav id="controls_container">
						<h2>Options</h2>
						<ul>
							<li>
								<div class="labeled-select-container">
									<label class="header-3" for="control-sortby">Sort</label>
									<div class="select-wrapper">
										<select id="control-sortby">
											<option value="chronological">Chronological</option>
											<option value="reverse-chronological">Reverse Chronological</option>
											<option value="views">Views</option>
										</select>
									</div>
								</div>
							</li>
							<li>
								<label class="header-3" for="control-daterange-left">Date range</label>
								<div class="slider-proxy">
									<div class="slider-thumb-proxy"></div>
									<input type="range" id="control-daterange-left" class="slider-left" step={86400.0} min={strval($content[count($content) - 1]['mtime'])} max={strval($content[0]['mtime'])} />
									<input type="range" class="slider-right" step={86400.0} min={strval($content[count($content) - 1]['mtime'])} max={strval($content[0]['mtime'])} />
								</div>
							</li>
						</ul>
					</nav>
					<div id="principal_content">
						{
							Vec\map($content, $bag ==> {
								$ast_title = LamIO\CMS\title_from_AST($bag['content']);
								$content_offset = 0;
								$title = 'Untitled';
								if($ast_title != null) {
									$title = new \MarkdownRenderable($this->renderer_struct, vec[$ast_title]);
									$content_offset = 1;
								}
								$x_content = new \MarkdownRenderable($this->renderer_struct, Vec\slice($bag['content']->getChildren(), $content_offset, 5));
								$meta = $bag['meta'];
								// $x_cats = '';
								// if($meta != null) {
								// 	$categories = $meta['categories'] ?? [];
								// 	invariant(is_array($categories), '');
								// 	$x_cats = <ul class="horz post-categories group">
								// 		{ Vec\map($categories, $cat ==> <li>{$cat as \XHPChild}</li>) }
								// 	</ul>;
								// }
								$thumb = $bag['thumb'] != null ? "project_assets/{$bag['thumb']}" : '';
								return <article>
									<a href={"/projects/{$bag['location']}"} target="_blank"><div class="thumb" style={"background-image:url({$thumb});"}></div></a>
									<header>
										<a href={"/projects/{$bag['location']}"} target="_blank"><h2>{$title}</h2></a>
										<!-- {$x_cats} -->
										<div class="post-date">{date("M j, Y", $bag['mtime'])}</div>
									</header>
									<div class="post-body">
										{$x_content}
									</div>
								</article>;
							})
						}
					</div>
				</section>
			</section>
			<ui:footer />
		</x:frag>;
	}
}
