<?php

/*
 * Rah_dupliquer - Plugin for Textpattern CMS
 * https://github.com/gocom/rah_dupliquer
 *
 * Copyright (C) 2013 Jukka Svahn
 *
 * This file is part of Rah_dupliquer.
 *
 * Rah_dupliquer is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, version 2.
 *
 * Rah_dupliquer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Rah_expanding. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * The plugin class.
 */

class Rah_Dupliquer
{
    /**
     * Constructor.
     */

    public function __construct()
    {
        register_callback(array($this, 'styles'), 'admin_side', 'head_end');
        register_callback(array($this, 'javascript'), 'admin_side', 'head_end');
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
            textpattern.Route.add('article, css, page, form', function ()
            {
                if (!$('#txp_clone').length && $('[name=publish]').length)
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
                    .on('mouseenter', function ()
                    {
                        $(this).siblings('.rah_dupliquer_tip')
                            .css('opacity', 0)
                            .css('visibility', 'visible')
                            .fadeTo(600, 1);
                    })
                    .on('mouseleave', function ()
                    {
                        $(this).siblings('.rah_dupliquer_tip')
                            .fadeTo(300, 0, function ()
                            {
                                $(this).css('visibility', 'hidden');
                            });
                    })
                    .on('click', function ()
                    {
                        $(this).siblings('.rah_dupliquer_tip')
                            .css('opacity', 0)
                            .css('visibility', 'hidden');
                    });

                $(window).on('keydown', function (e)
                {
                    if (String.fromCharCode(e.which).toLowerCase() === 'd' && (e.metaKey || e.ctrlKey))
                    {
                        var obj = $('.rah_dupliquer_tip');

                        if (obj.length)
                        {
                            e.preventDefault();
                            var clone = $('#txp_clone');

                            if (clone.length)
                            {
                                clone.click();
                                return;
                            }

                            var form = obj.eq(0).parents('form');
                            form.find('[name=exp_year], [name=url_title]').val('');
                            form.append(
                                '<input type="hidden" name="publish" value="1" />'+
                                '<input type="hidden" name="publish_now" value="1" />'+
                                '<input type="hidden" name="Status" value="1" />'
                            );
                            form.off('submit.txpAsyncForm').trigger('submit');
                        }
                    }
                });
            });
EOF;

        echo script_js($js);
    }
}

new Rah_Dupliquer();
