<div class="wrap tab wrap-addon" id="tab-sms-abandoned-cart">
	<div class="wrap egoi4wp-settings" id="tab-forms">
		<div class="row">

			<div id="abandoned_cart_message">
				<?php
				if ( isset( $_POST['form_id'] ) && $_POST['form_id'] == 'form-sms-order-abandoned-cart' ) {
					if ( $result ) {
						$this->helper->smsonw_admin_notice_success();
					} else {
						$this->helper->smsonw_admin_notice_error();
					}
				}
				?>
			</div>

			<div class="main-content col col-12" style="margin:0 0 20px;">

				<p class="label_text"><?php esc_html_e( 'Use this to add a lost cart sms trigger.', 'smart-marketing-addon-sms-order' ); ?></p>
				<br>
				<form action="#" method="post" class="form-sms-order-config" id="form-sms-order-abandoned-cart">
					<?php wp_nonce_field( 'form-sms-order-abandoned-cart' ); ?>
					<input name="form_id" type="hidden" value="form-sms-order-abandoned-cart" />
					<div id="sms_abandoned_cart">
						<table border="0" class="widefat striped" style="max-width: 900px;">
							<thead>
							<tr>
								<th><?php esc_html_e( 'Configurations', 'smart-marketing-addon-sms-order' ); ?></th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td><span><?php esc_html_e( 'Message', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<textarea name="message" id="message" style="min-width: 400px;width: 100%;"><?php echo ! empty( $abandoned_cart_obj['message'] ) ? esc_attr( $abandoned_cart_obj['message'] ) : '';?>
										</textarea>
									<p>
										<?php esc_html_e( 'Use %link% to choose the position of the link otherwise the link will be placed at the end.', 'smart-marketing-addon-sms-order' ); ?><br>
										<?php esc_html_e( 'Use %shop_name% for shop name display.', 'smart-marketing-addon-sms-order' ); ?><br>
									</p>
								</td>
							</tr>


							<tr>
								<td><span><?php esc_html_e( 'Title Pop', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<div>
										<input type="text" id="title_pop" name="title_pop" style="width: 100%;"
											   value="<?php
												echo ( isset( $abandoned_cart_obj['title_pop'] ) ) ? esc_attr( $abandoned_cart_obj['title_pop'] ) : '';
												?>"
										>
									</div>
								</td>
							</tr>

							<tr>
								<td><span><?php esc_html_e( 'Text on send button', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<div>
										<input type="text" id="button_name" name="button_name" style="width: 100%;"
											   value="<?php
												echo ( isset( $abandoned_cart_obj['button_name'] ) ) ? esc_attr( $abandoned_cart_obj['button_name'] ) : '';
												?>"
										>
									</div>
								</td>
							</tr>

							<tr>
								<td><span><?php esc_html_e( 'Text on cancel button', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<div>
										<input type="text" id="button_cancel_name" name="button_cancel_name" style="width: 100%;"
											   value="<?php
												echo ( isset( $abandoned_cart_obj['button_cancel_name'] ) ) ? esc_attr( $abandoned_cart_obj['button_cancel_name'] ) : '';
												?>"
										>
									</div>
								</td>
							</tr>

							<tr>
								<td><span><?php esc_html_e( 'Enabled', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<div>
										<input type="checkbox" id="enable" name="enable"
											<?php
											! empty( $abandoned_cart_obj['enable'] ) ? checked( $abandoned_cart_obj['enable'], 'on' ) : '';
											?>
										>
									</div>
								</td>
							</tr>

							<tr>
								<td><span><?php esc_html_e( 'Shortener', 'smart-marketing-addon-sms-order' ); ?></span></td>
								<td>
									<div>
										<input type="checkbox" id="shortener" name="shortener"
											<?php
											! empty( $abandoned_cart_obj['shortener'] ) ? checked( $abandoned_cart_obj['shortener'], 'on' ) : '';
											?>
										>
									</div>
								</td>
							</tr>

							<tr>
								<td colspan="2">
									<b><?php esc_html_e( 'Styles for dialog', 'smart-marketing-addon-sms-order' ); ?></b>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div>
										<div class="smsnf-input-group">
											<label for="bar-text-color" style="font-size: 13px;"><?php esc_html_e( 'Background Color', 'smart-marketing-addon-sms-order' ); ?></label>

											<div class="colorpicker-wrapper">
												<div style="background-color:<?php echo esc_attr( $abandoned_cart_obj['background_color'] ); ?>" class="view" ></div>
												<input id="background_color" type="text" name="background_color" value="<?php echo esc_attr( $abandoned_cart_obj['background_color'] ); ?>"  autocomplete="off" />
												<p><?php esc_html_e( 'Select Color', 'smart-marketing-addon-sms-order' ); ?></p>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div>
										<div class="smsnf-input-group">
											<label for="bar-text-color" style="font-size: 13px;"><?php esc_html_e( 'Text Color', 'smart-marketing-addon-sms-order' ); ?></label>

											<div class="colorpicker-wrapper">
												<div style="background-color:<?php echo esc_attr( $abandoned_cart_obj['text_color'] ); ?>" class="view" ></div>
												<input id="text_color" type="text" name="text_color" value="<?php echo esc_attr( $abandoned_cart_obj['text_color'] ); ?>"  autocomplete="off" />
												<p><?php esc_html_e( 'Select Color', 'smart-marketing-addon-sms-order' ); ?></p>
											</div>
										</div>
									</div>
								</td>
							</tr>

							<tr>
								<td colspan="2">
									<div>
										<div class="smsnf-input-group">
											<label for="bar-text-color" style="font-size: 13px;"><?php esc_html_e( 'Button Color', 'smart-marketing-addon-sms-order' ); ?></label>

											<div class="colorpicker-wrapper">
												<div style="background-color:<?php echo esc_attr( $abandoned_cart_obj['button_color'] ); ?>" class="view" ></div>
												<input id="button_color" type="text" name="button_color" value="<?php echo esc_attr( $abandoned_cart_obj['button_color'] ); ?>"  autocomplete="off" />
												<p><?php esc_html_e( 'Select Color', 'smart-marketing-addon-sms-order' ); ?></p>
											</div>
										</div>
									</div>
								</td>
							</tr>

							<tr>
								<td colspan="2">
									<div>
										<div class="smsnf-input-group">
											<label for="bar-text-color" style="font-size: 13px;"><?php esc_html_e( 'Button Text Color', 'smart-marketing-addon-sms-order' ); ?></label>

											<div class="colorpicker-wrapper">
												<div style="background-color:<?php echo esc_attr( $abandoned_cart_obj['button_text_color'] ); ?>" class="view" ></div>
												<input id="button_text_color" type="text" name="button_text_color" value="<?php echo esc_attr( $abandoned_cart_obj['button_text_color'] ); ?>"  autocomplete="off" />
												<p><?php esc_html_e( 'Select Color', 'smart-marketing-addon-sms-order' ); ?></p>
											</div>
										</div>
									</div>
								</td>
							</tr>

							</tbody>
						</table>
					</div>
					<div id="sms_order_abandoned_cart">
						<?php submit_button(); ?>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
