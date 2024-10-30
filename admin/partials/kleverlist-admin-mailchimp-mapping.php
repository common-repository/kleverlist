<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
    
if (!empty($user_audience)) :
    ?>
    <form method="post" id="kleverlist_mailchimp_mapping_settings">  
        <div class="kleverlist-sendy-integration-section">        
            <div class="klever-list-data-main-mapping-bg klever-list-mapping-target-field">
                <!-- Step 1: Start -->
                <table class="form-table width-900 ">
                   <div class="kleverlist-mapping-page-heading">
                        <h2><?php esc_html_e('Demographic Fields', 'kleverlist');?></h2>
                        <p><?php esc_html_e('This data is essential for personalizing communication. Using a customer\'s name and surname in emails can create a more personalized and engaging experience.', 'kleverlist');?></p>
                    </div>
                    <tbody class="kleverlist-data-mapping-bg">
                        <tr>
                            <th></th>
                            <td class="klever-list-data-mappinng-page-heading">
                                <div>                                    
                                    <h4><?php esc_html_e('Choose the fields you want to send to your audience.', 'kleverlist');?></h4>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Email *', 'kleverlist');?></th>
                            <td>
                                <input
                                    type="checkbox"
                                    id="kleverlist_mailchimp_user_email"
                                    name="kleverlist_mailchimp_user_email"        
                                    checked="checked" required disabled="disabled" />
                                    <p class="kleverlist-data"><?php esc_html_e('“The email address is taken from the billing email of the customer. This is the only mandatory field.”', 'kleverlist');?></p>
                            </td>
                        </tr>  
                        <tr>
                            <th><?php esc_html_e('First name', 'kleverlist');?></th>
                            <td>          
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_firstname">
                                        <input type="checkbox" name="kleverlist_mailchimp_firstname" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_firstname" <?php checked('1' === get_option('kleverlist_mailchimp_firstname'));?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the first name of the customer is taken from the billing information and filled into the corresponding %1$s“First Name”%2$s field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>
                                </p>       
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Last name', 'kleverlist');?></th>
                            <td>
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_lastname">
                                        <input type="checkbox" name="kleverlist_mailchimp_lastname" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_lastname" <?php checked('1' === get_option('kleverlist_mailchimp_lastname'));?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the last name of the customer is taken from the billing information and filled into the corresponding %1$s“Last Name”%2$s field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>
                                </p>  
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Username', 'kleverlist');?></th>
                            <td>
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_username">
                                        <input type="checkbox" name="kleverlist_mailchimp_username" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_username" <?php checked('1' === get_option('kleverlist_mailchimp_username'));?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>   
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the username of the customer is taken from the user information and filled into the corresponding field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>
                                </p>  
                            </td>
                        </tr>
                    </tbody>
                </table>  
                <!-- Step 1: End -->

                <!-- Step 2: Start -->
                <table class="form-table width-900 ">
                    <div class="kleverlist-mapping-page-heading kleverlist-advanced">
                        <h2><?php esc_html_e(' Geographic Fields', 'kleverlist');?></h2>
                        <p><?php esc_html_e('Particularly useful for businesses that operate in multiple regions or have location-specific products, services, or events.', 'kleverlist');?></p>
                    </div>
                    <tbody class="kleverlist-premium-option kleverlist-data-mapping-bg <?php echo esc_attr(KLEVERLIST_PLUGIN_CLASS)?>">
                        <tr>
                            <th><?php esc_html_e('Country', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>                                                           
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_country">
                                        <input type="checkbox" name="kleverlist_mailchimp_country" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_country" <?php checked('1' === get_option('kleverlist_mailchimp_country') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                                
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the country of the customer is taken from the billing information and filled into the corresponding field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?> 
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Address line 1', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_address_line_1">
                                        <input type="checkbox" name="kleverlist_mailchimp_address_line_1" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_address_line_1" <?php checked('1' === get_option('kleverlist_mailchimp_address_line_1') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                                 
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the address of the customer is taken from the billing information and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Address line 2', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon">
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_address_line_2">
                                        <input type="checkbox" name="kleverlist_mailchimp_address_line_2" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_address_line_2" <?php checked('1' === get_option('kleverlist_mailchimp_address_line_2') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>   
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the continued address of the customer is taken from the billing information and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?> 
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Town/City', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon">
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>                                                           
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_user_town_city">
                                        <input type="checkbox" name="kleverlist_mailchimp_user_town_city" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_user_town_city" <?php checked('1' === get_option('kleverlist_mailchimp_user_town_city') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                                
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the town/city of the customer is taken from the billing information and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?> 
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Province/County/District', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_user_province">
                                        <input type="checkbox" name="kleverlist_mailchimp_user_province" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_user_province" <?php checked('1' === get_option('kleverlist_mailchimp_user_province') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                             
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the province/county/district of the customer is taken from the billing information and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>         
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Postcode / ZIP', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon">
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>                             
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_user_postcode">
                                        <input type="checkbox" name="kleverlist_mailchimp_user_postcode" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_user_postcode" <?php checked('1' === get_option('kleverlist_mailchimp_user_postcode') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                                
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the postcode/zip of the customer is taken from the billing information and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Phone', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_user_phone">
                                        <input type="checkbox" name="kleverlist_mailchimp_user_phone" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_user_phone" <?php checked('1' === get_option('kleverlist_mailchimp_user_phone') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                               
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the telephone number of the customer is taken from the billing information and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <!-- Pro featured code end -->
                    </tbody>
                </table>
                <!-- Step 2: End -->

                <!-- Step 3: Start -->
                <table class="form-table width-900 ">
                    <div class="kleverlist-mapping-page-heading kleverlist-advanced">
                        <h2><?php esc_html_e('B2B Fields', 'kleverlist');?></h2>
                        <p><?php esc_html_e('This is especially relevant in email marketing campaigns targeting business clients or partners.', 'kleverlist');?></p>
                    </div>
                    <tbody class="kleverlist-premium-option kleverlist-data-mapping-bg <?php echo esc_attr(KLEVERLIST_PLUGIN_CLASS)?>">
                        <tr>
                            <th></th>
                            <td class="klever-list-data-mapping-extra-field">
                                <div>                                    
                                    <h4><?php esc_html_e('Choose the fields you want to send to your audience.', 'kleverlist');?></h4>
                                </div>
                            </td>
                        </tr>                        
                        <tr>
                            <th><?php esc_html_e('Company name', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>                                                           
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_company_name">
                                        <input type="checkbox" name="kleverlist_mailchimp_company_name" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_company_name" <?php checked('1' === get_option('kleverlist_mailchimp_company_name') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                                
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the company name of the customer is taken from the billing information and filled into the corresponding field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>                                        
                                </p>  
                            </td>
                        </tr>
                        <!-- Pro featured code end -->
                    </tbody>
                </table>
                <!-- Step 3: End -->

                <!-- Step 4: Start -->
                <table class="form-table width-900 ">
                    <div class="kleverlist-mapping-page-heading kleverlist-advanced">
                        <h2><?php esc_html_e('Behavioral Fields', 'kleverlist');?></h2>
                        <p><?php esc_html_e('This segment is based on customer behavior and interaction with your brand.', 'kleverlist');?></p>
                    </div>
                    <tbody class="kleverlist-premium-option kleverlist-data-mapping-bg <?php echo esc_attr(KLEVERLIST_PLUGIN_CLASS)?>">
                        <tr>
                            <th></th>
                            <td class="klever-list-data-mapping-extra-field">
                                <div>                                    
                                    <h4><?php esc_html_e('Choose the fields you want to send to your audience.', 'kleverlist');?></h4>
                                </div>
                            </td>
                        </tr>                        
                        <tr>
                            <th><?php esc_html_e('Number of orders', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_user_no_of_orders">
                                        <input type="checkbox" name="kleverlist_mailchimp_user_no_of_orders" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_user_no_of_orders" <?php checked('1' === get_option('kleverlist_mailchimp_user_no_of_orders') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                               
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the number of orders made by the customer is taken from WooCommerce and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('First Purchase Date', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>                                                           
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_first_purchase_date">
                                        <input type="checkbox" name="kleverlist_mailchimp_first_purchase_date" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_first_purchase_date" <?php checked('1' === get_option('kleverlist_mailchimp_first_purchase_date') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                                
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the date of the first purchase made by the customer is taken from WooCommerce and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>                                        
                                </p>  
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Last Purchase Date', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>                                                           
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_last_purchase_date">
                                        <input type="checkbox" name="kleverlist_mailchimp_last_purchase_date" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_last_purchase_date" <?php checked('1' === get_option('kleverlist_mailchimp_last_purchase_date') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                                
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the date of last purchase made by the customer is taken from WooCommerce and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>                                        
                                </p>  
                            </td>
                        </tr>
                        <!-- Pro featured code end -->
                    </tbody>
                </table>
                <!-- Step 4: End -->

                <!-- Step 5: Start -->
                <table class="form-table width-900 ">
                    <div class="kleverlist-mapping-page-heading kleverlist-advanced">
                        <h2><?php esc_html_e('Transactional Fields', 'kleverlist');?></h2>
                        <p><?php esc_html_e('Essential for segmenting customers focusing on past purchase behavior and spending patterns.', 'kleverlist');?></p>
                    </div>
                    <tbody class="kleverlist-premium-option kleverlist-data-mapping-bg <?php echo esc_attr(KLEVERLIST_PLUGIN_CLASS)?>">
                        <tr>
                            <th></th>
                            <td class="klever-list-data-mapping-extra-field">
                                <div>                                    
                                    <h4><?php esc_html_e('Choose the fields you want to send to your audience.', 'kleverlist');?></h4>
                                </div>
                            </td>
                        </tr>                        
                        <tr>
                            <th><?php esc_html_e('Total Revenue', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_user_total_revenue">
                                        <input type="checkbox" name="kleverlist_mailchimp_user_total_revenue" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_user_total_revenue" <?php checked('1' === get_option('kleverlist_mailchimp_user_total_revenue') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                               
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the total revenue earned from the customer is taken from WooCommerce and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Average Order Amount', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?>  
                            </th>
                            <td>
                                <div class="kleverlist-container">
                                    <label class="kleverlist-switch" for="kleverlist_mailchimp_user_average_order_amount">
                                        <input type="checkbox" name="kleverlist_mailchimp_user_average_order_amount" class="kleverlist-mapping-checkbox" id="kleverlist_mailchimp_user_average_order_amount" <?php checked('1' === get_option('kleverlist_mailchimp_user_average_order_amount') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> value="1" />
                                        <div class="kleverlist-slider kleverlist-round"></div>
                                    </label>
                                </div>                               
                                <p class="kleverlist-maapping-subheading">
                                    <?php
                                        printf(
                                            esc_html__('if %1$senabled%2$s, the average order amount earned from the customer is taken from WooCommerce and filled into the corresponding custom field in Mailchimp audience.', 'kleverlist'),
                                            '<strong>',
                                            '</strong>'
                                        );
                                    ?>
                                </p>
                            </td>
                        </tr>
                        <!-- Pro featured code end -->
                    </tbody>
                </table>
                <!-- Step 5: End -->
               
                <table class="form-table width-900">
                    <tbody class="kleverlist-data-mapping-bg kleverlist-margin">
                        <tr>
                            <th></th>
                            <td class="kleverlist-position button-mapping">
                            <?php
                                $button_attributes = array( 'id' => 'kleverlist_mailchimp_settings_save' );
                                submit_button(__('Save Changes', 'kleverlist'), 'primary', '', true, $button_attributes);
                            ?>
                                <div class="kleverlist-loader-outer-div">
                                    <div id="loader" class="hidden"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <p class="kleverlist-response"></p>
<?php endif;?>
