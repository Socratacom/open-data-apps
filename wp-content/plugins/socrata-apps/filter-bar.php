<div class="filter-bar">

	<a style="display: inline-block; height: auto; line-height: 1.8; margin-left: -20px; padding: 8px 20px; text-decoration: none; background: rgba(0,0,0,.05)" href="javascript:history.go(-1)" onMouseOver="self.status=document.referrer;return true"><span class="icon-arrow-left" style="margin-right:5px; font-size: 12px"></span>Back</a>

	<?php if ( !is_single() && have_posts() ) { ?>
	
	<div style="display:none; float: right">

		<label style="display:inline-block; margin: 0 10px"><input type="checkbox"> Free</label>

		<label style="display:inline-block; margin: 0 10px"><input type="checkbox"> Socrata Certified</label>

		<select id="example-getting-started" style="display:inline-block; margin: 0 0 0 10px">
			<option value="cheese">Device</option>
			<option value="tomatoes">Tomatoes</option>
			<option value="mozarella">Mozzarella</option>
			<option value="mushrooms">Mushrooms</option>
			<option value="pepperoni">Pepperoni</option>
			<option value="onions">Onions</option>
		</select>

		<select id="example-getting-started" style="display:inline-block; margin: 0 0 0 10px">
			<option value="cheese">Platform</option>
			<option value="tomatoes">Tomatoes</option>
			<option value="mozarella">Mozzarella</option>
			<option value="mushrooms">Mushrooms</option>
			<option value="pepperoni">Pepperoni</option>
			<option value="onions">Onions</option>
		</select>

	</div>

	<?php } ?>

</div>	