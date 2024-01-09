<?php
/**
 * The template for displaying the footer
 *
 * Contains the opening of the #site-footer div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

?>
		<footer>
			<div class="container fflex">
				<p>
					<?php _e('Copyright', 'custom-string-translation'); ?> © <?php echo date("Y"); ?> <?php _e('by Roman Kropachek', 'custom-string-translation'); ?>
				</p>
				<ul>
					<li>
						<a href="https://twitter.com/RKropachek" rel="nofollow" target="_blank">
							<svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect width="20" height="16" fill="transparent"></rect>
								<path fill-rule="evenodd" clip-rule="evenodd" class="transition-all" d="M20 1.89406C19.2642 2.21534 18.4734 2.43244 17.6434 2.53003C18.4904 2.03013 19.1411 1.23861 19.4475 0.295344C18.6546 0.758222 17.7765 1.0944 16.8418 1.2755C16.0934 0.490443 15.027 0 13.8468 0C11.5808 0 9.7435 1.80845 9.7435 4.03911C9.7435 4.35569 9.77976 4.66396 9.84977 4.9596C6.43962 4.79113 3.41611 3.18308 1.39238 0.739302C1.03917 1.33586 0.836808 2.02966 0.836808 2.76994C0.836808 4.17129 1.56117 5.40763 2.66218 6.13199C1.98956 6.11097 1.35685 5.92932 0.803675 5.62674C0.803284 5.64364 0.803284 5.66047 0.803284 5.67759C0.803284 7.63461 2.2177 9.26705 4.09477 9.63824C3.75048 9.73053 3.38798 9.77996 3.01377 9.77996C2.74936 9.77996 2.49234 9.75458 2.24175 9.7075C2.76392 11.3121 4.27922 12.48 6.07481 12.5125C4.67052 13.596 2.90128 14.2416 0.97882 14.2416C0.647619 14.2416 0.321027 14.2225 0 14.1851C1.81589 15.3312 3.97272 16 6.28987 16C13.8372 16 17.9644 9.84511 17.9644 4.50743C17.9644 4.33232 17.9604 4.15815 17.9526 3.98488C18.7543 3.41542 19.45 2.70403 20 1.89406Z" fill="#30373B"></path>
							</svg>
							Twitter
						</a>
					</li>
					<li>
						<a href="https://www.linkedin.com/in/kropachek/" rel="nofollow" target="_blank">
							<svg height="16" viewBox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg">
							<path class="transition-all" d="m12.6666667 0h-9.33333337c-1.84066666 0-3.33333333 1.49266667-3.33333333 3.33333333v9.33333337c0 1.8406666 1.49266667 3.3333333 3.33333333 3.3333333h9.33333337c1.8413333 0 3.3333333-1.4926667 3.3333333-3.3333333v-9.33333337c0-1.84066666-1.492-3.33333333-3.3333333-3.33333333zm-7.33333337 12.6666667h-2v-7.33333337h2zm-1-8.1786667c-.644 0-1.16666666-.52666667-1.16666666-1.176s.52266666-1.176 1.16666666-1.176 1.16666667.52666667 1.16666667 1.176-.522 1.176-1.16666667 1.176zm8.99999997 8.1786667h-2v-3.73600003c0-2.24533334-2.66666663-2.07533334-2.66666663 0v3.73600003h-2v-7.33333337h2v1.17666667c.93066666-1.724 4.66666663-1.85133333 4.66666663 1.65066667z" fill="#30373B"></path></svg>
							LinkedIn
						</a>
					</li>
				</ul>
			</div>
		</footer>
		<script src="<?php echo get_site_url();?>/wp-content/themes/ping/js/main.js"></script>
	</body>
</html>