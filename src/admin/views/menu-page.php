<h2><?php _e( 'Welcome to Tag Swapper', 'tag_swapper' ); ?></h2>
<hr>
<div class="header-message">
	<p class="backup-database-message"><strong><?php _e( 'Please backup your database before running the Tag Swapper!', 'tag_swapper' ); ?></strong></p>
	<p><?php _e( 'Tag Swapper uses the settings you configured below to find the HTML tag to be replaced when it has the attribute and attribute value.  Then it swaps the tag with the new one you specified.  For example, let\'s say your content has <code>&lt;p&gt;</code> tag with a <code>class</code> attribute and value of <code>headline</code>. You want that pattern to be a <code>h1</code> tag element instead of the <code>p</code>.  Tag Swapper gives you this ability.', 'tag_swapper' ); ?></p>
	<p><?php _e( 'It grabs all of the post type records from the Posts database table, searches for the matching patterns, swaps the tags when a match is found, and then saves all of the updated records back into the database.', 'tag_swapper' ); ?></p>
</div>

<?php if ( $this->validation_error ) : ?>
	<p class="validation-error-message"><?php _e( 'Whoops, the following red outlined fields are required in order to run the Tag Swapper.', 'tag_swapper' ); ?></p>
<?php endif; ?>

<form method="post" action="">
	<table class="form-table">
		<tbody>

			<tr valign="top">
				<th scope="row">
					<label for="tag_swapper[old_tag]"><?php _e( 'Old HTML Tag to be Replaced', 'tag_swapper' ); ?></label>
				</th>
				<td>
					<p>
						<select name="tag_swapper[old_tag]" id="tag_swapper_old_tag">
							<option value=""><?php _e( '-- Select --', 'tag_swapper' ); ?></option>

							<?php foreach ( (array) $this->config['tags'] as $tag ) : ?>
								<option value="<?php echo $tag; ?>"<?php selected( $tag, $this->current_values['old_tag'] ); ?>><?php echo $tag; ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<span class="description"><?php _e( 'Select the HTML tag which is to be replaced when you run the swapper.', 'tag_swapper' ); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="tag_swapper[new_tag]"><?php _e( 'New HTML Tag', 'tag_swapper' ); ?></label>
				</th>
				<td>
					<p>
						<select name="tag_swapper[new_tag]" id="tag_swapper_new_tag">
							<option value=""><?php _e( '-- Select --', 'tag_swapper' ); ?></option>

							<?php foreach ( (array) $this->config['tags'] as $tag ) : ?>
								<option value="<?php echo $tag; ?>"<?php selected( $tag, $this->current_values['new_tag'] ); ?>><?php echo $tag; ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<span class="description"><?php _e( 'Select the new HTML tag which will replace the old one you selected above.', 'tag_swapper' ); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="tag_swapper[search_attribute]"><?php _e( 'Search Attribute', 'tag_swapper' ); ?></label>
				</th>
				<td>
					<p>
						<select name="tag_swapper[search_attribute]" id="tag_swapper_search_attribute">
							<?php foreach ( array( 'class', 'id', 'data' ) as $attribute ) : ?>
								<option value="<?php echo $attribute; ?>"<?php selected( $attribute, $this->current_values['search_attribute'] ); ?>><?php echo $attribute; ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<span class="description"><?php _e( 'Select the attribute which be used to find the HTML tag you want to replace.', 'tag_swapper' ); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="tag_swapper[attribute_value]"><?php _e( 'Search Attribute Value', 'tag_swapper' ); ?></label>
				</th>
				<td>
					<p>
						<input<?php echo $attribute_value_class; ?> type="text" name="tag_swapper[attribute_value]" id="tag_swapper_attribute_value" value="<?php esc_attr_e( $this->current_values['attribute_value'] ); ?>" placeholder="<?php _e( 'Search attribute value', 'tag_swapper' ); ?>" size="30" />
					</p>
					<span class="description"><?php _e( 'Enter the search attribute\'s value.  When the swapper finds that value and the HTML tag matches, then it will swap the old with the new tag.', 'tag_swapper' ); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="tag_swapper[post_type]"><?php _e( 'Post Type', 'tag_swapper' ); ?></label>
				</th>
				<td>
					<p>
						<select name="tag_swapper[post_type]" id="tag_swapper_search_attribute">
							<option value=""><?php _e( '-- Select --', 'tag_swapper' ); ?></option>
							<?php foreach ( (array) get_post_types( '', 'objects' ) as $post_type ) : ?>
							<option value="<?php esc_attr_e( $post_type->name ) ; ?>"<?php selected( $post_type->name, $this->current_values['post_type'] ); ?>><?php esc_html_e( $post_type->labels->name ) ; ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<span class="description"><?php _e( 'Select the post type you want to conduct the search and replace for.', 'tag_swapper' ); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="tag_swapper[count_records]"><?php _e( 'Just count the records', 'tag_swapper' ); ?></label>
				</th>
				<td>
					<p>
						<?php foreach ( $this->config['yes_no_labels'] as $value => $label ) : ?>
						<input type="radio" name="tag_swapper[count_records]" value="<?php echo $value; ?>"<?php checked( (bool) $value, $this->current_values['count_records'] ); ?> /> <?php echo $label ?>
						<?php endforeach; ?>
					</p>
					<span class="description"><?php _e( 'When setting to "Yes", you will get a count of the number of records that will be processed. This is just a count, as it does not change the tags or update your database.', 'tag_swapper' ); ?></span>
					</p>
				</td>
			</tr>
		</tbody>
	</table>

	<p>
		<input type="submit" class="button hide-if-no-js" name="tag_swapper_go" id="tag_swapper_go" value="<?php _e( 'Run the Tag Swapper', 'tag_swapper' ) ?>" />
	</p>

	<noscript>
		<p>
			<em><?php _e( 'You must enable Javascript in order to proceed!', 'tag_swapper' ) ?></em>
		</p>
	</noscript>
</form>

<div class="messages">
	<?php if ( ! $this->validation_error ) : ?>
		<?php if ( $this->current_values['count_records'] === true ) : ?>
		<p class="tag-swapper-message<?php echo $message_class; ?>"><?php printf( __( 'There are %d records which contain the HTML pattern you have specified. Processed in %f seconds.', 'tag_swapper' ), (int) $this->records_count, $this->processing_time_in_seconds ); ?></p>
		<?php elseif ( $this->process_is_complete ) : ?>
			<p class="tag-swapper-message<?php echo $message_class; ?>"><?php printf( __( 'Tag Swap is complete.  %d records were updated with %d tags swapped.  Processed in %f seconds.', 'tag_swapper' ), (int) $this->records_count, (int) $this->number_tag_swaps, $this->processing_time_in_seconds ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
</div>