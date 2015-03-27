<style>
.filter-bar {
	background: #e9e9e9;
	margin-bottom: 30px;
	height: 42px;
	line-height: 42px;
	padding: 0;
	border-radius: 0;
	font-size: 13px;
}

.filter-bar .btn-default {
	margin-left: -20px;
	vertical-align: top;
	background: #ddd;
	color: #333;
}

.filter-bar .btn-group {
	vertical-align: top;
}

.js-platform .dropdown-toggle .caret {
/*	margin-left: 8px;
	margin-top: -1px;
	position: absolute;
	right: 15px;
	top: 50%;*/
}

.js-platform .dropdown-toggle {
	padding: 5px 12px;
	font-size: 13px;
	margin-top: 7px;
}
.js-platform {
	padding-right: 10px;
	width: 100%;
	max-width: 180px;
}

</style>

<div class="filter-bar hidden-xs">

	<?php if ( !is_single() && have_posts() && !is_tax('socrata_apps_resources')) { ?>

	<div style="text-align:right; width: 100%;">
		<span style="display: inline-block; margin-left: 15px; margin-right: 5px; color: #999">
		Filter by: 
		</span>

		<div style="display: inline-block" class="js-cost">
			<label style="display:inline-block; margin: 0 10px; font-weight: normal"><input type="checkbox" value="free"> Free</label>
		</div>

		<div style="display: inline-block; margin-right: 15px" class="js-certified">
			<label style="display:inline-block; margin: 0 10px; font-weight: normal"><input type="checkbox" value="certified"> Socrata Certified</label>
		</div>

		<div style="display: inline-block" class="js-platform">
			<select class="selectpicker show-tick" data-style="btn-info" style="margin: 0 0 0 10px;">
			    <option value="all">All Platforms</option>
			    <option value="web">Web</option>
			    <option value="ios">iOS</option>
			    <option value="android">Android</option>
			    <option value="windows-phone">Windows Phone</option>
			    <!-- <option disabled >&#x23af;&#x23af;&#x23af;&#x23af;&#x23af;&#x23af;&#x23af;</option> -->
			    <option value="mac-os">Mac OS</option>
			    <option value="linux">Linux</option>
			    <option value="windows">Windows</option>
			</select>
		</div>

	</div>

	<?php } ?>

</div>

<script>

'use strict';

var Exports = {
	Modules : {}
};

Exports.Modules.Gallery = (function($, undefined) {

	var $grid,
	$cost,
	$certified,
	$platform,
	$device,
	$sizer,

	cost = [],
	certified = [],
	platform = [],
	device = [],

	init = function() {
		setVars();
		initFilters();
		initShuffle();
	},

	setVars = function() {
		$grid = $('.js-shuffle');
		$cost = $('.js-cost');
		$certified = $('.js-certified');
		$platform = $('.js-platform');
		$device = $('.js-device');
		$sizer = $grid.find('.shuffle__sizer');
	},

	initShuffle = function() {
	    // instantiate the plugin
	    $grid.shuffle({
	    	itemSelector: '.shuffle-item',
	    	// columnWidth: 233,
	    	// columnWidth: function( containerWidth ) {
	    	// 	console.log(Math.round((containerWidth - 30) / 3));
	    	// .box's have a width of 18%
	    	// 	// return 0.33 * containerWidth;
	    	// 	// return Math.round((containerWidth - 30) / 3);
	    	// 	return 283;
	    	// },
	    	// sizer: $('.js-shuffle .sizer'),
	    	// sizer: $sizer,
	    	// gutterWidth: 0,
	      	speed: 250,
	      	delimeter: ',',
	      	// buffer: 0, // Useful for percentage based heights when they might not always be exactly the same (in pixels).
			// columnThreshold: 0 ? 0.01 : 0.1, // Reading the width of elements isn't precise enough and can cause columns to jump between values.
	        // easing: 'cubic-bezier(0.165, 0.840, 0.440, 1.000)' // easeOutQuart
	    });

		$('.js-cost input, .js-certified input, .js-platform select').change();

  	},

 	initFilters = function() {

		// cost
		$cost.find('input').on('change', function() {
			var $checked = $cost.find('input:checked'),
			groups = [];

			if ($checked.length !== 0) {
				$checked.each(function() {
					groups.push(this.value);
				});
			}
			cost = groups;

			filter();
		});

		// certified
		$certified.find('input').on('change', function() {
			var $checked = $certified.find('input:checked'),
			groups = [];

			if ($checked.length !== 0) {
				$checked.each(function() {
					groups.push(this.value);
				});
			}
			certified = groups;

			filter();
		});

		// platform
		$platform.find('select').on('change', function() {
			var $select = $platform.find('select'),
			groups = [];
			platform = $select.val();
			filter();
		});

		// device
		$device.find('select').on('change', function() {
			var $select = $device.find('select'),
			groups = [];
			device = $select.val();
			filter();
		});

	},

	filter = function() {
		if ( hasActiveFilters() ) {
			$grid.shuffle('shuffle', function($el) {
				return itemPassesFilters( $el.data() );
			});
		} else {
			$grid.shuffle( 'shuffle', 'all' );
		}

		if ($grid.find('.filtered').length === 0) {
			$grid.addClass('no-results');
		} else {
			$grid.removeClass('no-results');
		}
	},

	itemPassesFilters = function(data) {

		if ( cost.length > 0 && !valueInArray(data.cost, cost) ) {
			return false;
		}

		if ( certified.length > 0 && !valueInArray(data.certified, certified) ) {
			return false;
		}

		if ( platform.length > 0 && data.platform ) {
			for (var i=0; i < data.platform.length; i++) {
				if (platform === data.platform[i]) {
					return true;
				}
			}
			return false;
		}

		return true;
	},

	hasActiveFilters = function() {
		return cost.length > 0 || certified.length > 0 || platform.length > 0;
	},

	valueInArray = function(value, arr) {
		return $.inArray(value, arr) !== -1;
	};

	return {
		init: init
	};

}(jQuery));

$(document).ready(function() {
	Exports.Modules.Gallery.init();
});
</script>
