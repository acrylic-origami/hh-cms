<?hh // strict
use LamIO\Page;
use namespace LamIO\CMS;
use namespace HH\Lib\{C, Str, Vec};
use namespace Facebook\Markdown;
class Index extends Common {
	<<__Override>>
	public function get_style_deps(): vec<string> {
		return Vec\concat(parent::get_style_deps(), vec["css/index.css"]);
	}
	public function get_js_deps(): vec<string> {
		return vec["js/index.main.js"];
	}
	public function get_title(): string {
		return 'Derek Lam';
	}
	<<__Memoize>>
	public function render_body(): \XHPRoot {
		$projects_content = LamIO\CMS\content_iterator($this->renderer_struct, __DIR__ . '/../../public/project_assets');
		$writing_content = LamIO\CMS\content_iterator($this->renderer_struct, __DIR__ . '/../../public/blog_assets')
			|> Vec\sort($$, ($a, $b) ==> $b['mtime'] - $a['mtime']);
			
		return <x:frag>
			<a id="top"></a>
			<a href="#main_content" id="skip_to_main">Skip to main content</a>
			<div id="sandbox"></div>
			<section id="hero">
				<!-- <canvas class="fullwidth-canvas fullheight-canvas particle-canvas"></canvas> -->
				<canvas class="fullwidth-canvas fullheight-canvas fin-canvas"></canvas>
				<!-- <canvas class="fullwidth-canvas fullheight-canvas particle-canvas"></canvas> -->
				<canvas class="fullwidth-canvas fullheight-canvas fin-canvas"></canvas>
				<!-- <canvas class="fullwidth-canvas fullheight-canvas particle-canvas"></canvas> -->
				<canvas class="fullwidth-canvas fullheight-canvas fin-canvas"></canvas>
				<!-- <canvas class="fullwidth-canvas fullheight-canvas particle-canvas"></canvas> -->
				<canvas class="fullwidth-canvas fullheight-canvas fin-canvas"></canvas>
				<canvas class="fullwidth-canvas fullheight-canvas" id="remainder_fin_canvas"></canvas>
				<canvas class="fullwidth-canvas fullheight-canvas" id="logo_stage_canvas"></canvas>
				<canvas class="fullwidth-canvas fullheight-canvas" id="logo_shard_canvas"></canvas>
				
				<ui:nav />
				<div id="welcome_wrapper">
					<h1>Welcome!</h1>
				</div>
				<div id="intro_wrapper">
					<div>{"This is Derek Lam's personal site."}</div>
					<div>{"Let's get personal."}</div>
					<div>{"Scroll, I'll tell you a bit about myself."}</div>
					<div>{"Or just enjoy the "}<a href="blog/logo-sketches/shard" target="_blank">Shard</a>{" demo shown above"}</div>
				</div>
			</section>
			<section id="about">
				<div class="group" id="about_content_wrapper">
					<h1>Spending time</h1>
					<p>
						Studying <a href="//engsci.utoronto.ca/" target="_blank">Engineering Science</a> at the <a href="//utoronto.ca" target="_blank">University of Toronto</a> is my full-time occupation. There isn’t much about a circuit that is totally uninteresting to me, so I specialized in Electrical and Computer Engineering (ECE).
					</p>
					<p>
						Links to great groups of people that I have the privilege to work with right now:
					</p>
					<ul class="horz bulleted group">
						<li><a href="http://utrahumanoid.ca/">UTRA Robosoccer, Custom Servo team</a></li>
						<li><a href="https://www.autodrive.utoronto.ca/">aUtoronto Self-driving Car Team, Simulation subteam</a></li>
						<li><a href="http://www.eecg.toronto.edu/~roman/lab/index.html">ISM Lab, Thesis Student</a></li>
					</ul>
					<a id="contact" name="contact" />
					<h2>Projects on this site</h2>
					<ul class="horz bulleted group">
						<li>Visit the <a href="./projects">Projects Page</a></li>
						<li>Scroll down to see <a href="#recent_work">Recent Work</a>&nbsp;&#x02193;</li>
					</ul>
					<h2>Presence Elsewhere</h2>
					<ul class="horz group logo-farm">
						<li><a href="//github.com/acrylic-origami" style="background-image:url(img/content/github.svg);" class="logo"></a></li>
						<li><a href="//www.behance.net/dereklam97382c" style="background-image:url(img/content/behance.svg);" class="logo"></a></li>
						<li><a href="//stackoverflow.com/users/3925507/concat" style="background-image:url(img/content/so-icon.svg);" class="logo"></a></li>
						<li><a href="https://twitter.com/acrylicorigami" style="background-image:url(img/content/twitter.svg);" class="logo"></a></li>
					</ul>
					<dl>
						<dt>Email:</dt>
						<dd>&#60;my name&#62;@lam.io</dd>
						<dt>Resume:</dt>
						<dd><a href="resume.pdf">everything here, but smaller</a></dd>
					</dl>
				</div>
			</section>
			<section id="work">
				<div id="work_content_wrapper">
					<section id="projects">
						<h1 id="recent_work">Recent Work</h1>
						<a href="projects.php" class="work-cta button-like" style="border-color:#FFF">All Projects</a>
						<h2>Recent Projects</h2>
						<ul class="isotope horz" id="ongoing_project_list">
							{
								Vec\slice($projects_content, 0, 10)
									|> Vec\map($$, $bag ==> {
										$ast_title = LamIO\CMS\title_from_AST($bag['content']);
										$content_offset = 0;
										$title = 'Untitled';
										if($ast_title != null) {
											$title = new \MarkdownRenderable($this->renderer_struct, vec[$ast_title]);
											$content_offset = 1;
										}
										$meta = $bag['meta'];
										$x_meta = '';
										if($meta != null) {
											$categories = $meta['categories'] ?? [];
											invariant(is_array($categories), '');
											$x_meta = <ul class="horz project-meta group">
												{ Vec\map($categories, $cat ==> <li>{$cat as \XHPChild}</li>) }
											</ul>;
										}
										$thumb = $bag['thumb'] != null ? "project_assets/{$bag['thumb']}" : '';
										return <li class="grid-item project">
											<span>{date("M Y", $bag['mtime'])}</span>
											<div class="work-thumbnail" style={"background-image:url({$thumb});"}></div>
											<h3><a href={"/{$bag['location']}"} target="_blank">{$title}</a></h3>
											{$x_meta}
											<div class="post-body">
												{new \MarkdownRenderable($this->renderer_struct, Vec\slice($bag['content']->getChildren(), $content_offset, 5))}
											</div>
										</li>;
									})
							}
						</ul>
					</section><!--
				--><section id="writing">
						<a href="writing.php" class="work-cta button-like" style="border-color:#FFF">All Writing</a>
						<h1>Writing</h1>
						<ol>
							{
								/* TODO: reduce this redundancy with Writing */
								Vec\slice($writing_content, 0, 10)
									|> Vec\map($$, $bag ==> {
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
										// if($meta != null) {
										// 	$categories = $meta['categories'] ?? [];
										// 	invariant(is_array($categories), '');
										// 	$x_cats = <ul class="horz post-categories group">
										// 		{ Vec\map($categories, $cat ==> <li>{$cat as \XHPChild}</li>) }
										// 	</ul>;
										// }
										
										return <li class="writing-item">
											<header>
												<h2><a href={"blog/{$bag['location']}"}>{$title}</a></h2>
												<!-- {$x_cats} -->
												<div class="post-date">{date("M j, Y", $bag['mtime'])}</div>
											</header>
											<div class="post-body">
												{$x_content}
											</div>
										</li>;
									})
							}
						</ol>
					</section>
				</div>
			</section>
			<ui:footer />
		</x:frag>;
	}
}
