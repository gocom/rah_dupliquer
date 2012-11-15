<?php

/**
 * rah_dupliquer plugin for Textpattern CMS.
 *
 * @author  Jukka Svahn
 * @date    2012-
 * @license GNU GPLv2
 * @link    https://github.com/gocom/rah_dupliquer
 * 
 * Copyright (C) 2012 Jukka Svahn http://rahforum.biz
 * Licensed under GNU Genral Public License version 2
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

	new rah_dupliquer();

/**
 * The plugin class.
 */

class rah_dupliquer
{
	/**
	 * Constructor.
	 */

	public function __construct()
	{
		global $event;

		if ($event === 'article')
		{
			register_callback(array($this, 'styles'), 'admin_side', 'head_end');
			register_callback(array($this, 'javascript'), 'admin_side', 'head_end');
		}
	}

	/**
	 * Styles.
	 */

	public function styles()
	{
		echo <<<EOF
			<style>
				.rah_dupliquer_tip
				{
					visibility: hidden;
				}
			</style>
EOF;
	}

	/**
	 * Initializes the JavaScript.
	 */

	public function javascript()
	{
		$js = <<<EOF
			(function() {
				$(document).ready(function() {

					if ($('[name=publish]').length)
					{
						return;
					}

					var tipText = 'CTRL+D';

					if (navigator.userAgent.indexOf('Mac OS X') !== -1)
					{
						tipText = '&#8984;+D';
					}

					$('form .publish').eq(0)
						.after(' <small class="rah_dupliquer_tip information">'+tipText+'</small> ')
						.hover(
							function() {
								$(this).siblings('.rah_dupliquer_tip')
									.css('opacity', 0)
									.css('visibility', 'visible')
									.fadeTo(600, 1);
							},
							function() {
								$(this).siblings('.rah_dupliquer_tip')
									.fadeTo(300, 0, function() {
										$(this).css('visibility', 'hidden');
									});
							}
						)
						.click(function() {
							$(this).siblings('.rah_dupliquer_tip')
								.css('opacity', 0)
								.css('visibility', 'hidden');
						});
				});

				$(window).keydown(function(e) {
					if (String.fromCharCode(e.which).toLowerCase() === 'd' && (e.metaKey || e.ctrlKey))
					{
						var obj = $('.rah_dupliquer_tip');
						if (obj.length)
						{
							e.preventDefault();
							var form = obj.eq(0).parents('form');
							form.find('[name=exp_year], [name=url_title]').val('');
							form.append(
								'<input type="hidden" name="publish" value="1" />'+
								'<input type="hidden" name="publish_now" value="1" />'+
								'<input type="hidden" name="Status" value="1" />'
							);
							form.off('submit.txpAsyncForm').submit();
						}
					}
				});
			})();
EOF;

		echo script_js($js);
	}
}

?>