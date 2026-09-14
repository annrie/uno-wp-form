/**
 * Unomoon Form inquiry data chart renderer.
 *
 * Renders the series prepared by Unomoon_Form_Chart_Controller (exposed as
 * window.unomoonformChartData) with the bundled Chart.js. Replaces the
 * former Google Charts implementation so that no external script is loaded.
 */
( function( $ ) {
	'use strict';

	var BASE_COLOR = '1e8cbe';
	var PIE_HEIGHT = 260;
	var BAR_ROW_HEIGHT = 40;

	/**
	 * Build a gradient palette starting from the admin blue, one colour per item.
	 *
	 * @param {number} count Number of colours.
	 * @return {string[]} Hex colours.
	 */
	function getColors( count ) {
		var red = parseInt( BASE_COLOR.substr( 0, 2 ), 16 );
		var green = parseInt( BASE_COLOR.substr( 2, 2 ), 16 );
		var blue = parseInt( BASE_COLOR.substr( 4, 2 ), 16 );
		// Spread the steps so that a handful of items still get distinguishable shades.
		var steps = Math.max( count, 1 );
		var redStep = Math.max( 15, Math.round( ( 209 - red ) / steps ) );
		var greenStep = Math.max( 10, Math.round( ( 223 - green ) / steps ) );
		var blueStep = Math.max( 5, Math.round( ( 229 - blue ) / steps ) );
		var colors = [];

		for ( var i = 0; i < count; i++ ) {
			colors.push( '#' + toHex( red ) + toHex( green ) + toHex( blue ) );
			red = Math.min( red + redStep, 209 );
			green = Math.min( green + greenStep, 223 );
			blue = Math.min( blue + blueStep, 229 );
		}
		return colors;
	}

	function toHex( value ) {
		var hex = value.toString( 16 );
		return hex.length < 2 ? '0' + hex : hex;
	}

	function pieConfig( series ) {
		return {
			type: 'pie',
			data: {
				labels: series.labels,
				datasets: [ {
					data: series.counts,
					backgroundColor: getColors( series.counts.length ),
					borderWidth: 1
				} ]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { position: 'right' }
				}
			}
		};
	}

	function barConfig( series ) {
		var total = series.total || 0;
		var ratios = series.counts.map( function( count ) {
			return total ? Math.round( count / total * 1000 ) / 10 : 0;
		} );

		return {
			type: 'bar',
			data: {
				labels: series.labels,
				datasets: [ {
					data: ratios,
					backgroundColor: getColors( series.counts.length ),
					borderWidth: 0
				} ]
			},
			options: {
				indexAxis: 'y',
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: false },
					tooltip: {
						callbacks: {
							label: function( context ) {
								return context.parsed.x + ' %';
							}
						}
					}
				},
				scales: {
					x: {
						min: 0,
						max: 100,
						ticks: {
							color: '#999',
							callback: function( value ) {
								return value + ' %';
							}
						}
					},
					y: {
						ticks: { color: '#444' }
					}
				}
			}
		};
	}

	function render( $container, series ) {
		var isBar = 'bar' === series.chart;
		var height = isBar ? series.labels.length * BAR_ROW_HEIGHT + 30 : PIE_HEIGHT;
		var canvas = document.createElement( 'canvas' );

		$container.css( 'height', height + 'px' ).empty().append( canvas );

		return new window.Chart( canvas, isBar ? barConfig( series ) : pieConfig( series ) );
	}

	$( function() {
		var charts = window.unomoonformChartData;
		if ( ! charts || 'undefined' === typeof window.Chart ) {
			return;
		}

		$.each( charts, function( key, series ) {
			var $container = $( '[data-chart-key="' + key + '"]' );
			if ( $container.length && series.labels && series.labels.length ) {
				render( $container, series );
			}
		} );
	} );
} )( jQuery );
