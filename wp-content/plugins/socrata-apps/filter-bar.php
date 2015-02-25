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

	<a class="btn btn-default" href="javascript:history.go(-1)" onMouseOver="self.status=document.referrer;return true"><span class="icon-arrow-left" style="margin-right:5px; font-size: 12px"></span><span class="hidden-xs hidden-sm">Back</span></a>

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

			<select style="margin: 0 0 0 10px">
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
