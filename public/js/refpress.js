/****************************************************************************
 * RefPress v1.0.0
 * helpers.js
 ****************************************************************************/

window.RefPress = window.RefPress || {};

jQuery( document ).ready( function( $ ){
    'use strict';

    RefPress.chartdata = {

        start: function() {
            if ( typeof Chart !== 'undefined' && Chart ){
                this.chartInit();
            }
        },

        chartInit: function() {

            var canvas = document.getElementById("RefPressChartCanvas");
            if ( ! canvas ){
                return ;
            }

            var chartValue = canvas.getAttribute( 'data-chartvalue' );
            if ( ! chartValue ) {
                return ;
            }

            var chartData = JSON.parse( chartValue );

            var refpressContext = canvas.getContext('2d');
            var RefPressChart = new Chart(refpressContext, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Amount',
                        backgroundColor: 'rgba(219,173,201,0.4)',
                        borderColor: '#9B2B69',
                        data: chartData.value,
                        borderWidth: 2,
                        fill: true,
                        lineTension: 0,
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            label: function( t, d ) {
                                return d.datasets[t.datasetIndex].label + ' : ' + _refpress.currency_sign + t.yLabel;
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                callback: function(value, index, values) {
                                    if ( chartData.xAxes && chartData.xAxes[index] ) {
                                        return chartData.xAxes[index];
                                    }

                                    return value;
                                }
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0, // it is for ignoring negative step.
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    if (Math.floor(value) === value) {
                                        return _refpress.currency_sign + value;
                                    }
                                }
                            }
                        }]
                    },

                    legend: {
                        display: false
                    }
                }
            });




        },

    }

    //KickStart the JS
    RefPress.chartdata.start();

});


;/****************************************************************************
 * RefPress v1.0.0
 * Copyright 2020 | RefPress | https://www.themeqx.com
 ****************************************************************************/

window.RefPress = window.RefPress || {};

jQuery( document ).ready( function( $ ){
    'use strict';

    RefPress.core = {

        start: function() {
            this.pluginInit();
            this.addListeners();
            this.loadEvents();
            this.create_account_search_wp_user();
        },

        pluginInit: function() {
            /**
             * Datepicker
             */
            if ( jQuery.datepicker ) {
                $( ".refpress_datepicker" ).datepicker( {"dateFormat": 'yy-mm-dd'} );
            }

            /**
             * Load Color Picker
             */
            if ( typeof $.fn.wpColorPicker !== 'undefined' ) {
                $( ".refpress_color_picker" ).wpColorPicker();
            }

            /**
             * Select2
             */
            if ( jQuery().select2 ) {
                $( '.refpress_select2' ).select2();
            }
        },

        addListeners: function() {
            $( document ).on( 'submit', '#refpressLinkGeneratorForm', this.generateAffiliateLink.bind( this ) );
            $( document ).on( 'click', '#refpressCopyLinkSelector', this.copyLink.bind( this ) );

            //Tab Settings ID
            $( document ).on( 'click', '.refpress-admin-payout-nav-method-item', this.payoutSettingsTabToggle.bind( this ) );
            //Payout Settings Frontend
            $( document ).on( 'change', '#refpress_payout_method_select', this.switchPayoutMethod.bind( this ) );

        },

        loadEvents: function() {

            /**
             * Settings Panel Tab
             * @since RefPress 1.0.0
             */

            $( document ).on( 'click', '.refpress-settings-sidebar a', function( e ) {
                e.preventDefault();

                var nav_id = $( this ).attr( 'data-target' );
                $( '.option-sidebar-item' ).removeClass( 'current' );
                $( this ).addClass( 'current' );
                $( '.refpress-settings-panel-nav_tab' ).hide();
                $( nav_id ).addClass( 'current' ).show();
                window.history.pushState( 'obj', '', $( this ).attr( 'href' ) );
            } );

            $( document ).on( 'change', '#RefPressSettingsForm', function( e ) {
                $( '#refpress-settings-panel-notice-wrap' ).html(
                    '<p class="settings-changes-warning"><strong>' + _refpress.settings_changed_msg + '</strong></p>' );
            } );

            $( document ).on( 'submit', '#RefPressSettingsForm', function( e ) {
                e.preventDefault();

                var $form = $( this );
                var data = $form.serialize();

                $.ajax( {
                    url: _refpress.ajaxurl,
                    type: 'POST',
                    data: data,
                    beforeSend: function() {
                        $( '#refpress-settings-panel-notice-wrap' ).html( '' );
                        $form.find( '.button' ).addClass( 'refpress-spinner' );
                    },
                    complete: function() {
                        $form.find( '.button' ).removeClass( 'refpress-spinner' );
                    }
                } );
            } );

        },


        generateAffiliateLink: function( e ) {
            e.preventDefault();

            var $form = $( e.currentTarget );
            var submitted_link = $( '#landing-page-url-input' ).val().trim();
            var campaign = $( '#refpress-campaign-input' ).val().trim();

            var generated_link = '';
            if ( submitted_link.length > 0 ) {
                generated_link = RefPress.updateQueryStringParameter( submitted_link, _refpress.ref_param,
                    _refpress.current_user_id );

                if ( campaign.length > 0 ) {
                    generated_link = RefPress.updateQueryStringParameter( generated_link, 'campaign', campaign );
                }
            }

            $( '#repressGeneratedLink' ).val( generated_link );

            $( '#refpressGeneratorlinkWrap' ).show();
        },

        copyLink: function( e ) {
            e.preventDefault();

            var $that = $( e.target );

            var copyLink = document.getElementById( "repressGeneratedLink" );
            copyLink.select();
            copyLink.setSelectionRange( 0, 99999 ); /* For mobile devices */
            document.execCommand( "copy" );

            $that.html( _refpress.copied );
        },

        create_account_search_wp_user: function() {
            //setup before functions
            var typingTimer;                //timer identifier

            $( document ).on( 'keyup change paste', '#reppress_wp_user_name', function(){
                var search_text = $( this ).val().trim();
                var current_val = $(this).attr('data-curr_val') ? $(this).attr('data-curr_val').trim() : null;

                if ( search_text === current_val )
                    return false;
                $(this).attr('data-curr_val', search_text);


                if ( search_text.length >= 3 ) {
                    $( '#refpress-account-search-results' ).html( '<p class="searching-text"> ' + _refpress.searching_user + ' </p>' );
                } else{
                    $( '#refpress-account-search-results' ).html( '' );
                }

                clearTimeout( typingTimer );
                if ( search_text.length >= 3 ) {
                    typingTimer = setTimeout( search_wp_user, 2000 );
                }

            } );

            //user is "finished typing," do something
            function search_wp_user() {
                var search_term = $( '#reppress_wp_user_name' ).val().trim();

                $.ajax( {
                    url: _refpress.ajaxurl,
                    type: 'POST',
                    data: { search_term : search_term, action : 'refpress_create_account_search_wp_user' },
                    success: function( response ){
                        $( '#refpress-account-search-results' ).html( response.data.results );
                    },
                    complete: function() {
                        //
                    }
                } );
            }

            /**
             * Set selected user to the form
             */

            $(document).on( 'click', '.choose-user-to-create-account', function( e ){
                e.preventDefault();

                var $that = $( this );
                var user_id = $that.attr( 'data-user-id' ).trim();
                var full_name = $that.attr( 'data-user-fullname' ).trim();

                $( '#refpress_wp_user_id' ).val(user_id);
                $( '#reppress_wp_user_name' ).val(full_name);
                $( '#refpress-account-search-results' ).html('');
            } );

        },

        /**
         * Payouts
         */

        payoutSettingsTabToggle: function( e ){
            e.preventDefault();

            var $that = $( e.target );

            var target_id = $that.attr( 'data-target-id' );
            $( '.refpress-settings-payout-method-content' ).hide();
            $( '#' + target_id ).show();
        },

        /**
         * Change Payout Method From Referer Dashboard
         */

        switchPayoutMethod: function( e ){
            e.preventDefault();

            var $that = $( e.target );
            var payout_id = $that.val();

            $( '.payout-method-form-wrap' ).hide();
            $( '#payout-method-form-' + payout_id ).show();
        },

    }

    //KickStart the JS
    RefPress.core.start();

});
;/****************************************************************************
 * RefPress v1.0.0
 * helpers.js
 ****************************************************************************/

window.RefPress = window.RefPress || {};

jQuery( document ).ready( function( $ ){
    'use strict';

    RefPress.updateQueryStringParameter = function updateQueryStringParameter( uri, key, value ) {
        var re = new RegExp( "([?&])" + key + "=.*?(&|$)", "i" );
        var separator = uri.indexOf( '?' ) !== - 1 ? "&" : "?";

        var updated_uri = null;
        if ( uri.match( re ) ) {
            updated_uri = uri.replace( re, '$1' + key + "=" + value + '$2' );
        } else {
            updated_uri = uri + separator + key + "=" + value;
        }

        if ( updated_uri ) {
            return encodeURI( updated_uri );
        }
    }

});



//# sourceMappingURL=refpress.js.map