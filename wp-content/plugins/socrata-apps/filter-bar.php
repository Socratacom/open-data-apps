<style>
.filter-bar {
	background: #e9e9e9;
	margin-bottom: 30px;
	height: 42px;
	line-height: 42px;
	padding: 0 20px;
	border-radius: 0;
}

.filter-bar .btn-default {
	margin-left: -20px;
	vertical-align: top;
	background: #ddd;
	color: #333;
}

.filter-bar .btn-group {
	margin-left: 20px;
	margin-right: -20px;
	vertical-align: top;
}
</style>

<div class="filter-bar hidden-xs">

	<?php if ( !is_single() && have_posts() && !is_tax('socrata_apps_resources')) { ?>

	<div style="float: right">

		<div style="display: inline-block" class="js-cost">
			<label style="display:inline-block; margin: 0 10px"><input type="checkbox" value="free"> Free</label>
		</div>

		<div style="display: inline-block" class="js-certified">
			<label style="display:inline-block; margin: 0 10px"><input type="checkbox" value="certified"> Socrata Certified</label>
		</div>

		<div class="js-platform" style="display: inline-block">

			<div class="btn-group" style="display: none">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					Platform <span class="caret"></span>
				</button>
				<ul class="dropdown-menu pull-right" role="menu">
					<li><a href="#">Web</a></li>
					<li><a href="#">iOS</a></li>
					<li><a href="#">Android</a></li>
					<li><a href="#">Windows Phone</a></li>
					<li class="divider" style="height: 2px; background: #dedede; display: block; margin: 10px 0; width: 100%;"></li>
					<li><a href="#">Mac OS</a></li>
					<li><a href="#">Linux</a></li>
					<li><a href="#">Windows</a></li>
				</ul>
			</div>

			<select class="form-control" style="margin: 0 0 0 10px">
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

		<div class="js-device" style="display: none">
			<select style="display:inline-block; margin: 0 0 0 10px">
				<option value="all">All Devices</option>
				<option value="web">Web</option>
				<option value="mobile">Mobile</option>
				<option value="desktop">Desktop</option>
			</select>
		</div>

	</div>

	<?php } ?>

</div>

<script>

var Exports = {
	Modules : {}
};

Exports.Modules.Gallery = (function($, undefined) {

	var $cost,
	$certified,
	$platform,

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
	},

	initShuffle = function() {
	    // instantiate the plugin
	    $grid.shuffle({
	    	itemSelector: '.shuffle-item',
	    	columnWidth: 293,
	      	speed : 250,
	      	delimeter: ',',
	        easing : 'cubic-bezier(0.165, 0.840, 0.440, 1.000)' // easeOutQuart
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

		if ( platform.length > 0 ) {
			for (i=0; i < data.platform.length; i++) {
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
