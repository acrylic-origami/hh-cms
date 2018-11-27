<?hh // strict
use LamIO\Page;
use namespace LamIO\CMS;
use namespace HH\Lib\{C, Str, Vec, Keyset};
use namespace Facebook\Markdown;
class Writing extends Common {
	<<__Override>>
	public function get_style_deps(): vec<string> {
		return Vec\concat(parent::get_style_deps(), vec[
			"/css/writing.css",
			"/css/plain-nav.css",
		]);
	}
	public function get_js_deps(): vec<string> {
		return vec[
			"js/index.writing.js",
		];
	}
	<<__Memoize>>
	public function render_body(): \XHPRoot {
		$content = LamIO\CMS\content_iterator($this->renderer_struct, __DIR__ . "/../../public/blog_assets")
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
					<h1>Writing</h1>
					<p>
						{vec["Hey look! I write technically", "Hey look! I write, technically", "Technical writing painstakingly designed to be almost slightly funny"][mt_rand(0, 2)]}
					</p>
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
								<header>
									<h3>Categories</h3>
								</header>
								<ul>
									{
										C\reduce($content,
											($cats, $bag) ==> {
												$meta = $bag['meta'];
												if($meta != null) {
													$categories = $meta['categories'] ?? [];
													invariant(is_array($categories), '');
													return Keyset\union($cats, vec($categories) ?? vec[]);
												}
												else
													return $cats;
											},
											keyset[])
											|> Vec\map($$, $cat ==> {
												$key = strtolower(str_replace([' ', '/', '-'], ['_'], $cat));
												return <li>
													<input type="checkbox" id={"cat_{$key}"} />
													<label for={"cat_{$key}"}>{$cat}</label>
													<span class="count"></span>
												</li>;
											})
									}
								</ul>
							</li>
							<li>
								<header>
									<h3>Date published</h3>
								</header>
								<div class="slider-proxy">
									<div class="slider-thumb-proxy"></div>
									<input type="range" class="slider-left" />
									<input type="range" class="slider-right" />
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
								$x_cats = '';
								if($meta != null) {
									$categories = $meta['categories'] ?? [];
									invariant(is_array($categories), '');
									$x_cats = <ul class="horz post-categories group">
										{ Vec\map($categories, $cat ==> <li>{$cat as \XHPChild}</li>) }
									</ul>;
								}
								if($meta === null || $meta['featured'] === false) {
									$thumb = $bag['thumb'] != null ? "blog_assets/{$bag['thumb']}" : '';
									return <article class="unfeatured">
										<a href={"/blog/{$bag['location']}"} target="_blank"><div class="thumb" style={"background-image:url({$thumb});"}></div></a>
										<header>
											<a href={"/blog/{$bag['location']}"} target="_blank"><h2>{$title}</h2></a>
											{$x_cats}
											<div class="post-date">{date("M j, Y", $bag['mtime'])}</div>
										</header>
										<div class="post-body">
											{$x_content}
										</div>
									</article>;
								}
								else {
									$thumb = $bag['hero'] != null ? "blog_assets/{$bag['hero']}" : '';
									return <article class="featured">
										<a href={"/blog/{$bag['location']}"} target="_blank"><div class="thumb" style={"background-image:url({$thumb});"}></div></a>
										<header>
											<a href={"/blog/{$bag['location']}"} target="_blank"><h2>{$title}</h2></a>
											{$x_cats}
											<div class="post-date">{date("M j, Y", $bag['mtime'])}</div>
											<div class="ctas">
												<a href={"/blog/{$bag['location']}"}>Read more &rarr;</a>
												<span class="collapse"></span>
											</div>
										</header>
										<div class="post-body">
											{$x_content}
										</div>
										<div class="readmore-container">
											<a href={"blog/{$bag['location']}"} style="border-color:#000;" class="button-like">Read more &rarr;</a>
										</div>
									</article>;
								}
							})
						}
					</div>
				</section>
			</section>
			<ui:footer />
		</x:frag>;
	}
}
