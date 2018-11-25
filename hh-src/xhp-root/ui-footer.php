<?hh // strict
class :ui:footer extends :x:element {
	public function render(): \XHPRoot {
		return <footer id="main_footer">
			<div class="content-container">
				<img src="/img/graphics/bottom_logo.svg" id="footer_logo" /><!-- conceding for aspect ratio -->
				<span class="header-1">EOF</span>
				<a href="#top" class="skip-to-top"></a>
			</div>
		</footer>;
	}
}