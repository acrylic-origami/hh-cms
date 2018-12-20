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
	public function get_title(): string {
		return 'Writing - Derek Lam';
	}
	<<__Memoize>>
	public function render_body(): \XHPRoot {
		$content = LamIO\CMS\content_iterator($this->renderer_struct, __DIR__ . "/../../public/blog_assets")
			|> Vec\sort($$, ($a, $b) ==> $b['mtime'] - $a['mtime']);
			
		$min_time = $content[count($content) - 1]['mtime'];
		$max_time = $content[0]['mtime'];
		$upper_time = \ceil(($max_time - $min_time) / 86400) * 86400 + $min_time; // slightly larger than max time to account for snapping in the range input
		$slider_control = <div class="slider-proxy">
			<div class="slider-thumb-proxy"></div>
			<input type="range" id="control_daterange_left" class="slider-left" step={86400.0} value={strval($min_time)} min={strval($min_time)} max={strval($upper_time)} />
			<input type="range" id="control_daterange_right" class="slider-right" step={86400.0} value={strval($upper_time)} min={strval($min_time)} max={strval($upper_time)} />
		</div>;
		
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
							<li class="group">
								<div class="labeled-select-container">
									<label class="header-3" for="control_sortby">Sort</label>
									<div class="select-wrapper">
										{null /* HH_IGNORE_ERROR[4053] autocomplete needed for `selected` on FF, also wow this fix sucks. */}<select id="control_sortby">
											<option value="chronological">Chronological</option>
											<option value="reverse-chronological" selected={true}>Reverse Chronological</option>
											<option value="views">Views</option>
										</select>
									</div>
								</div>
							</li>
							<li class="group">
								<div class="labeled-select-container">
									<label class="header-3" for="control_show">Show</label>
									<div class="select-wrapper">
										<select id="control_show">
											<option value="all">All articles</option>
											<option value="featured">Only featured</option>
											<option value="unfeatured">Only unfeatured</option>
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
												$key = \str_replace(' ', '_', $cat);
												return <li>
													<input type="checkbox" class="control-cat" id={"cat_{$key}"} value={$key} />
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
								{$slider_control}
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
								$categories = [];
								if($meta != null) {
									$categories = $meta['categories'] ?? [];
									invariant(is_array($categories), '');
									$categories = Vec\map($categories, $cat ==> str_replace(' ', '_', $cat));
									$x_cats = <ul class="horz post-categories group">
										{ Vec\map($categories, $cat ==> <li>{$cat as \XHPChild}</li>) }
									</ul>;
								}
								$featured = $meta !== null && array_key_exists('featured', $meta) && $meta['featured'] === true;
								
								$thumb = '';
								if($featured) {
									$thumb = $bag['hero'] != null ? "blog_assets/{$bag['hero']}" : '';
								}
								else {
									$thumb = $bag['thumb'] != null ? "blog_assets/{$bag['thumb']}" : '';
								}
								return <article class={"post ".($featured ? '' : 'un')."featured"}
									data-category={Str\join($categories, ' ')}
									data-mtime={$bag['mtime']}
									data-ctime={$bag['ctime']}
								>
									<a href={"/blog/{$bag['location']}"} target="_blank"><div class="thumb" style={"background-image:url({$thumb});"}></div></a>
									<header>
										<a href={"/blog/{$bag['location']}"} target="_blank"><h2>{$title}</h2></a>
										{$x_cats}
										<div class="post-date">{date("M j, Y", $bag['mtime'])}</div>
										{
											$featured ? 
											<div class="ctas">
												<a href={"/blog/{$bag['location']}"}>Read more &rarr;</a>
												<span class="collapse"></span>
											</div> : ''
										}
									</header>
									<div class="post-body">
										{$x_content}
									</div>
									{
										$featured ? 
										<div class="readmore-container">
											<a href={"blog/{$bag['location']}"} style="border-color:#000;" class="button-like">Read more &rarr;</a>
										</div> : ''
									}
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
