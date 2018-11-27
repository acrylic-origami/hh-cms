<?hh // strict
class :ui:nav extends :x:element {
	public function render(): \XHPRoot {
		/* HH_IGNORE_ERROR[2050] */
		$request_uri = $_SERVER['REQUEST_URI'];
		return <nav id="main_nav">
			<div class="group">
				<div class="show-line"></div>
				<div class="show-line"></div>
			</div>
			<div style="float:left; width:5%;">
				<div class="show-line"></div>
				<div class="show-line"></div>
				<div class="show-line"></div>
			</div>
			<ul class="horz group" id="main_nav_bar">
				<li class="logo-container">
					<a href="/"><span>Derek Lam</span></a>
				</li>
				<li class={"page-nav" . (preg_match('/^\/(index\.[^\/]+)?$/', $request_uri) ? ' selected' : '')}><a href="/">Home</a></li>
				<li class={"page-nav" . (preg_match('/^\/projects(\.[^\/]+)?(\/.*)?$/', $request_uri) ? ' selected' : '')}><a href="/projects.php">Projects</a></li>
				<li class={"page-nav" . (preg_match('/^\/(writing|blog)(\.[^\/]+)?\/?$/', $request_uri) ? ' selected' : '')}><a href="/writing.php">Writing</a></li>
				<li class="page-nav"><a href="/#contact">Contact</a></li>
			</ul>
		</nav>;
	}
}