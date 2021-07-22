CREATE TABLE IF NOT EXISTS `store_categories` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `order` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `noproducts` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `metatitle` varchar(150) DEFAULT NULL,
  `metadesc` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_config` (
  `setting` varchar(100) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`setting`),
  UNIQUE KEY `setting` (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `store_config` (`setting`, `value`) VALUES
('admin_url', 'http://www.myadminsite.co.uk'),
('admin_order_url', '/admin/orders'),
('currency', 'GBP'),
('delivery_type', 'weight'),
('download_attempts', '10'),
('download_link', '%1$s/store/download?id=%2$s&amp;link=%3$s'),
('download_link_expiry', '6 months'),
('download_product_format', '<p><strong>%1$s</strong><br /><br /><strong>Download Link:</strong> <a href="%2$s" title="%1$s Download">%2$s</a>%3$s</p>'),
('download_product_serial_format', '<br /><strong>Serial(s)</strong><br />%s'),
('download_require_serials', '1'),
('gallery_image_folder', 'gallery'),
('gallery_thumbs_folder', 'thumbnails'),
('gallery_thumb_width', '200'),
('logo_root_path', '/images/logo.png'),
('registered_address', 'Street Address, Line 2, Town, County, Postcode'),
('send_review_email', 'false'),
('site_name', 'My Website Name'),
('site_url', 'https://www.mywebsite.co.uk'),
('store_url', '/store/'),
('image_url', '/images/products/'),
('order_history_url', '/history/view/'),
('email_from_name', 'Your Name'),
('email_from_address', 'noreply@mywebsite.co.uk'),
('email_office_address', 'dispatch@mywebsite.co.uk'),

('email_activation_altbody', 'Hello, \\n\\n To be able to log in to your account you first need to activate your account by visiting the following link : \\n %1$s/%2$s \\n\\n You then need to use the following activation key: %3$s \\n\\n If you did not sign up on %1$s recently then this message was sent in error, please ignore it.'),
('email_activation_body', 'Hello,<br/><br/> To be able to log in to your account you first need to activate your account by clicking on the following link : <strong><a href=\"%1$s/%2$s\">%1$s/%2$s</a></strong><br/><br/> You then need to use the following activation key: <strong>%3$s</strong><br/><br/> If you did not sign up on %1$s recently then this message was sent in error, please ignore it.'),
('email_activation_subject', '%s - Activate account'),

('email_dispatch_altbody', 'Order Complete and Despatched\r\n\r\nDear %1$s %2$s,\r\n\r\nThank you for your order no: #%3$s placed on %4$s\r\n\r\nYour transaction has now been despatched (if applicable).\r\n\r\nOrder Number: #%3$s\r\n\r\nOrder Status: Complete and Despatched\r\n\r\nYour Details\r\n\r\nBilling Information\r\n\r\n%6$s\r\n%8$s\r\n%10$s\r\n%12$s\r\n%14$s\r\n\r\nDelivery Information\r\n\r\n%7$s\r\n%9$s\r\n%11$s\r\n%13$s\r\n%15$s\r\n\r\n Just in case you forgot what you ordered:\r\n\r\nItems in this order\r\n\r\n%5$s\r\n\r\nPlease feel free to contact a member of staff if you have any questions or queries regarding your order\r\n'),
('email_dispatch_body', '<h2>Order Complete &amp; Despatched</h2><p>Dear %1$s %2$s,</p><p>Thank you for your order no: #%3$s placed on %4$s</p><p>Your transaction has now been despatched (if applicable).</p><p><strong>Order Number:</strong> #%3$s</p><p><strong>Order Status:</strong> Complete &amp; Despatched</p><h2>Your Details</h2><table width=\"100%%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\"><tr><th colspan=\"2\">Billing Information</th><th colspan=\"2\">Delivery Information</th></tr><tr><td><strong>Address</strong></td><td>%6$s</td><td><strong>Address</strong></td><td>%7$s</td></tr><tr><td></td><td>%8$s</td><td></td><td>%9$s</td></tr><tr><td><strong>Town</strong></td><td>%10$s</td><td><strong>Town</strong></td><td>%11$s</td></tr><tr><td><strong>County</strong></td><td>%12$s</td><td><strong>County</strong></td><td>%13$s</td></tr><tr><td><strong>Postcode</strong></td><td>%14$s</td><td><strong>Postcode</strong></td><td>%15$s</td></tr></table><p>&nbsp;</p><p>Just in case you forgot what you ordered:</p><h2>Items in this order</h2>%5$s<p>Please feel free to contact a member of staff if you have any questions or queries regarding your order</p>'),
('email_dispatch_subject', 'Your order #%3$s has been dispatched'),

('email_download_subject', 'Download access for #%s'),
('email_download_body', '<p>Dear %1$s %2$s,</p><p>Thank you for your order no: %3$s placed on %4$s</p><p>Below are the links you need to access the digital products.</p><p><strong>IMPORTANT</strong> these links will expire on %5$s and you have %6$s attempts to download each product.</p><hr />%7$s<hr /><p>Once again thank you for shopping with %8$s.</p>'),
('email_download_altbody', "Dear %1$s %2$s,\r\n\r\nThank you for your order no: %3$s placed on %4$s.\r\n\r\nBelow are the links you need to access the digital products.\r\n\r\nIMPORTANT these links will expire on %5$s and you have %6$s attempts to download each product.\r\n\r\n------------------------------------------\r\n\r\n%7$s\r\n\r\n------------------------------------------\r\n\r\nOnce again thank you for shopping with %8$s."),

('email_order_cancel_altbody', 'Order Cancelled\r\n\r\nDear %1$s %2$s,\r\n\r\nOrder number #%3$s has been cancelled. More information concerning this may be in the order notes which can be found by using the following this link:\r\n[%4$s]\r\n\r\nOrders can be cancelled by the customer during purchase or by a staff member. If you wish to raise a new order please feel free to do so.'),
('email_order_cancel_body', '<h2>Order Cancelled</h2><p>Dear %1$s %2$s,</p><p>Order number #%3$s has been cancelled. More information concerning this may be in the order notes which can be found by using the following this link:</p><p><a href="%4$s%5$s">%4$s%5$s</p><hr /><p>Orders can be cancelled by the customer during purchase or by a staff member. If you wish to raise a new order please feel free to do so.</p>'),
('email_order_cancel_subject', 'Your %3$s order #%1$s has been cancelled'),

('email_order_confirm_altbody', 'Thank you for your order \n\r\n\r Dear %1$s %2$s, \n\r\n\r Thank you very much for your order. We\'ll get your items packed up and sent out as soon as possible, and we\'ll send you a quick email to let you know when they\'re on their way. \n\r\n\r Your order number: %3$s \n\r\n\r Order Items \n\r\n\r %6$s \n\r\n\r Delivery Address: \n\r\n\r %7$s \n\r %8$s \n\r %9$s %10$s %11$s\n\r\n\r You can view the status of your order at any time, on our website by following the link: \n\r\n\r %4$s%5$s'),
('email_order_confirm_body', '<h3>Thank you for your order</h3><p>Dear %1$s %2$s,</p><p>Thank you very much for your order. We\'ll get your items packed up and sent out as soon as possible, and we\'ll send you a quick email to let you know when they\'re on their way.</p><p>Your order number: %3$s</p><h4>Order Items:</h4>%6$s<p>&nbsp;</p><hr /><h4>Delivery Address:</h4><p>%7$s<br />%8$s<br />%9$s<br />%10$s<br />%11$s</p><hr /><p>You can view the status of your order at any time, on our website by following the link: <a href="%4$s%5$s">%4$s%5$s</a></p>
'),
('email_order_confirm_subject', 'Your %3$s order'),

('email_order_refund_altbody', 'Order Refunded\r\n\r\nDear %1$s %2$s,\r\n\r\nOrder number #%3$s has been refunded. More information concerning this may be in the order notes which can be found by using the following this link: %4$s/%5$s\r\n\r\nPlease allow up to 5 working days for the refund to go back into you account.\r\n\r\nIf you wish to raise a new order please feel free to do so.'),
('email_order_refund_body', '<h2>Order Refunded</h2><p>Dear %1$s %2$s,</p><p>Order number #%3$s has been refunded. More information concerning this may be in the order notes which can be found by using the following this link:</p><p><a href="%4$s%5$s">%4$s%5$s</p><hr /><p>Please allow up to 5 working days for the refund to go back into you account.</p><hr /><p>If you wish to raise a new order please feel free to do so.</p>'),
('email_order_refund_subject', 'Your %3$s order #%1$s has been refunded'),

('email_order_office_subject', 'Order #%1$s placed on %2$s'),
('email_order_office_body', '<p>The following order has been placed and payment has been successfully taken.</p><hr /><p><strong>Order No :</strong> #%1$s<br /><strong>Order Date :</strong> #%2$s<br /><strong>Name :</strong> %3$s<br /><strong>Amount :</strong> %4$s%5$s</p>Further details about this order can be found at <a href="%6$s%7$s">%6$s%7$s</a>.<hr /><h4>Order Items</h4>%8$s<hr /><p>Please can you despatch the goods as soon as possible.</p>'),
('email_order_office_altbody', "The following order has been placed and payment has been successfully taken. \n\n Order No : #%1$s \n Order Date : #%2$s \n\n Name : %3$s \n Amount : %4$s%5$s \n\n Further details about this order can be found at %6$s%7$s. \n\n Order Items \n
%8$s \n\n Please can you despatch the goods as soon as possible."),

('email_reset_subject', '%s - Password reset request'),
('email_reset_body', 'Hello,<br/><br/>To reset your password click the following link :<br/><br/><strong><a href="%1$s/%2$s?key=%3$s">%1$s/%2$s?key=%3$s</a></strong><br/><br/>This link will expire 48 hours from the time it was requested.<br/><br/>If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it.'),
('email_reset_altbody', "Hello, \n\n  To reset your password please visiting the following link : \n %1$s/%2$s?key=%3$s \n\n This link will expire 48 hours from the time it was requested. \n\n If you did not request a password reset key on %1$s recently then this message was sent in error, please ignore it."),

('email_password_change_subject', '%s Password Changed'),
('email_password_change_body', 'Hi %5$s,<br /><br />This email has been sent to confirm that your password has been updated successfully.<br /><br />If you feel that your account has been updated by someone other than yourself please contact support at <a href="%2$s">%2$s</a> if you have questions.<br /><br />Regards<br /><br />%1$s'),
('email_password_change_altbody', "Hi %5$s, \n\r\n\r This email has been sent to confirm that your password has been updated successfully. \n\r\n\r If you feel that your account has been updated by someone other than yourself please contact support at %2$s if you have questions. \n\r\n\r Regards \n\r\n\r %1$s"),

('email_reg_subject', '%s Account Registration'),
('email_reg_body', '<h3>Welcome</h3><p>Dear %4$s %6$s,<br /><br />Thank you for creating an account with %1$s. We\'re excited to have you join us.<br /><br />From now on you can login using your email address and chosen password at <a href="%2$s/%3$s">%2$s/%3$s</a>.<br /><br />Have any questions? Just shoot us an email! We\'re always here to help.<br /><br />Kind Regards<br /><br />The %1$s Team</p>'),
('email_reg_altbody', "Welcome \n\r\n\r Dear %4$s %6%$s, \n\r\n\r Thank you for creating an account with %1$s. We're excited to have you join us. \n\r\n\r From now on you can login using your email address and chosen password at %2$s/%3$s \n\r\n\r Have any questions? Just shoot us an email! We're always here to help. \n\r\n\r Kind Regards \n\r\n\r The %1$s Team"),

('email_review_subject', 'Review has been submitted on %s'),
('email_review_body', '<p>A new review has been submitted for your product %1$s via %2$s.</p><p>Please review this submission</p><p>Review submitted by IP address: %3$s</p>'),
('email_review_altbody', 'A new review has been submitted for your product %1$s via %2$s. \n\r Please review this submission.  \n\r\n\r Review submitted by IP address: %3$s'),

('email_html_wrapper', '<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>%2$s</title>
    <style type="text/css">
img {border: none;-ms-interpolation-mode: bicubic;max-width: 100%%}
body {background-color: #f6f6f6;font-family: sans-serif;-webkit-font-smoothing: antialiased;font-size: 14px;line-height: 1.4;margin: 0;padding: 0;-ms-text-size-adjust: 100%%;-webkit-text-size-adjust: 100%%}
table {border-collapse: separate;mso-table-lspace: 0pt;mso-table-rspace: 0pt;width: 100%%}
table td {font-family: sans-serif;font-size: 14px;vertical-align: top}
.body {background-color: #f6f6f6;width: 100%%}
.container {display: block;margin: 0 auto !important;max-width: 580px;padding: 10px;width: 580px}
.content {box-sizing: border-box;display: block;margin: 0 auto;max-width: 580px;padding: 10px}
.main {background: #ffffff;border-radius: 3px;width: 100%%}
.wrapper {box-sizing: border-box;padding: 20px}
.content-block {padding-bottom: 10px;padding-top: 10px;}
.footer {clear: both;margin-top: 10px;text-align: center;width: 100%%}
.footer td,.footer p,.footer span,.footer a {color: #999999;font-size: 12px;text-align: center}
h1,h2,h3,h4 {color: #000000;font-family: sans-serif;font-weight: 400;line-height: 1.4;margin: 0;margin-bottom: 30px}
h1 {font-size: 35px;font-weight: 300;text-align: center;text-transform: capitalize}
p,ul,ol {font-family: sans-serif;font-size: 14px;font-weight: normal;margin: 0;margin-bottom: 15px}
p li,ul li,ol li {list-style-position: inside;margin-left: 5px}
a {color: #3498db;text-decoration: underline}
.btn {box-sizing: border-box;width: 100%%}
.btn > tbody > tr > td {padding-bottom: 15px}
.btn table {width: auto}
.btn table td {background-color: #ffffff;border-radius: 5px;text-align: center}
.btn a {background-color: #ffffff;border: solid 1px #3498db;border-radius: 5px;box-sizing: border-box;color: #3498db;cursor: pointer;display: inline-block;font-size: 14px;font-weight: 700;margin: 0;padding: 12px 25px;text-decoration: none;text-transform: capitalize}
.btn-primary table td {background-color: #3498db}
.btn-primary a {background-color: #3498db;border-color: #3498db;color: #ffffff}
.last {margin-bottom: 0}
.first {margin-top: 0}
.align-center {text-align: center}
.align-right {text-align: right}
.align-left {text-align: left}
.clear {clear: both}
.mt0 {margin-top: 0}
.mb0 {margin-bottom: 0}
.preheader {color: transparent;display: none;height: 0;max-height: 0;max-width: 0;opacity: 0;overflow: hidden;mso-hide: all;visibility: hidden;width: 0}
.powered-by a {text-decoration: none}
hr {border: 0;border-bottom: 1px solid #f6f6f6;margin: 20px 0}

@media only screen {
    @font-face {font-family: "Open Sans"; font-style: normal; font-weight: 300; src: local("Open Sans Light"), local("OpenSans-Light"), url(https://fonts.gstatic.com/s/opensans/v14/DXI1ORHCpsQm3Vp6mXoaTegdm0LZdjqr5-oayXSOefg.woff2) format("woff2");}
    @font-face {font-family: "Open Sans"; font-style: normal; font-weight: 400; src: local("Open Sans Regular"), local("OpenSans-Regular"), url(https://fonts.gstatic.com/s/opensans/v14/cJZKeOuBrn4kERxqtaUH3VtXRa8TVwTICgirnJhmVJw.woff2) format("woff2");}
    @font-face {font-family: "Open Sans"; font-style: normal; font-weight: 700; src: local("Open Sans Bold"), local("OpenSans-Bold"), url(https://fonts.gstatic.com/s/opensans/v14/k3k702ZOKiLJc3WVjuplzOgdm0LZdjqr5-oayXSOefg.woff2) format("woff2");}
	a, p, li, h1, h2, h3, h4, h5, h6 {font-family: "Open Sans", sans-serif !important}
}

@media only screen and (max-width: 620px) {
    table[class=body] h1 {font-size: 28px !important;margin-bottom: 10px !important}
    table[class=body] p,
    table[class=body] ul,
    table[class=body] ol,
    table[class=body] td,
    table[class=body] span,
    table[class=body] a {font-size: 16px !important}
    table[class=body] .wrapper,
    table[class=body] .article {padding: 10px !important}
    table[class=body] .content {padding: 0 !important}
    table[class=body] .container {padding: 0 !important;width: 100%% !important}
    table[class=body] .main {border-left-width: 0 !important;border-radius: 0 !important;border-right-width: 0 !important}
    table[class=body] .btn table {width: 100%% !important}
    table[class=body] .btn a {width: 100%% !important}
    table[class=body] .img-responsive {height: auto !important;max-width: 100%% !important;width: auto !important}
}

@media all {
    .ExternalClass {width: 100%%}
    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {line-height: 100%%}
    .apple-link a {color: inherit !important;font-family: inherit !important;font-size: inherit !important;font-weight: inherit !important;line-height: inherit !important;text-decoration: none !important}
    .btn-primary table td:hover {background-color: #34495e !important}
    .btn-primary a:hover {background-color: #34495e !important;border-color: #34495e !important}
}
    </style>
  </head>
  <body class="">
    <table border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          %4$s
          <div class="content">
            <span class="preheader">%2$s</span>
            <table class="main">
              <tr>
                <td class="wrapper">
                  <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        %1$s
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <div class="footer">
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="content-block">
                    <span class="apple-link">%3$s</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>'),

('login_url', 'login'),
('table_basket', 'store_orders'),
('table_basket_products', 'store_order_products'),
('table_categories', 'store_categories'),
('table_delivery_fixed_cost', 'store_delivery_fixed_cost'),
('table_delivery_methods', 'store_delivery_methods'),
('table_delivery_value', 'store_delivery_value'),
('table_delivery_weight', 'store_delivery_weight'),
('table_delivery_address', 'users_delivery_address'),
('table_downloads', 'store_downloads'),
('table_gallery', 'store_gallery_images'),
('table_products', 'store_products'),
('table_product_categories', 'store_product_category'),
('table_product_images', 'store_product_images'),
('table_review', 'store_reviews'),
('table_serials', 'store_serials'),
('table_tax', 'store_tax'),
('table_users', 'users'),
('table_users_attempts', 'users_attempts'),
('table_users_requests', 'users_requests'),
('table_users_sessions', 'users_sessions'),
('table_voucher', 'store_vouchers'),
('trading_as', 'My Company LTD'),
('vat_number', ''),
('timezone', 'Europe/London');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(10) DEFAULT NULL,
  `firstname` varchar(150) DEFAULT NULL,
  `lastname` varchar(150) DEFAULT NULL,
  `add_1` varchar(100) DEFAULT NULL,
  `add_2` varchar(100) DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `county` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `postcode` varchar(15) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(72) NOT NULL,
  `ipaddress` varchar(30) DEFAULT NULL,
  `no_orders` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `regtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `require_pass` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `isactive` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(39) NOT NULL,
  `expirydate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users_requests` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL,
  `rkey` varchar(20) NOT NULL,
  `expire` datetime NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL,
  `hash` varchar(40) NOT NULL,
  `expiredate` datetime NOT NULL,
  `ip` varchar(39) NOT NULL,
  `agent` varchar(200) NOT NULL,
  `cookie_crc` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users_delivery_address` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(10) DEFAULT NULL,
  `firstname` varchar(150) DEFAULT NULL,
  `lastname` varchar(150) DEFAULT NULL,
  `add_1` varchar(255) NOT NULL,
  `add_2` varchar(255) DEFAULT NULL,
  `town` varchar(100) NOT NULL,
  `county` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `postcode` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_delivery_fixed_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cost` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_delivery_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_delivery_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `min_price` decimal(10,2) NOT NULL,
  `max_price` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_delivery_weight` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `max_weight` decimal(10,3) UNSIGNED NOT NULL,
  `price` decimal(10,2) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_downloads` (
  `dlid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `order_id` int(11) UNSIGNED NOT NULL,
  `product` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `expire` date NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`dlid`),
  UNIQUE KEY `link` (`link`),
  KEY `order_id` (`order_id`),
  KEY `product` (`product`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_gallery_images` (
  `img_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_orders` (
  `order_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_no` varchar(30) NOT NULL,
  `customer_id` int(11) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) UNSIGNED DEFAULT '1',
  `payment_flagged` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `billing_id` int(11) UNSIGNED DEFAULT NULL,
  `delivery_id` int(11) UNSIGNED DEFAULT NULL,
  `delivery_method` int(11) DEFAULT NULL,
  `digital` tinyint(1) UNSIGNED DEFAULT '0',
  `voucher` varchar(20) DEFAULT NULL,
  `subtotal` decimal(10,2) UNSIGNED NOT NULL,
  `discount` decimal(10,2) UNSIGNED NOT NULL,
  `total_tax` decimal(10,2) UNSIGNED NOT NULL,
  `delivery` decimal(10,2) UNSIGNED NOT NULL,
  `cart_total` decimal(10,2) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_date` datetime DEFAULT NULL,
  `sessionid` varchar(255) DEFAULT NULL,
  `ipaddress` varchar(50) DEFAULT NULL,
  `shipped` datetime DEFAULT NULL,
  `mailsent` tinyint(1) UNSIGNED ZEROFILL DEFAULT '0',
  `followed_up` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `cart_id` (`order_no`),
  KEY `cust_id` (`customer_id`),
  KEY `status` (`status`),
  KEY `has_been_followed_up` (`followed_up`),
  KEY `delivery_id` (`delivery_id`),
  KEY `voucher` (`voucher`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_order_products` (
  `order_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `quantity` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `product_info` text DEFAULT NULL,
  UNIQUE KEY `unique_order_product` (`order_id`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_products` (
  `product_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text,
  `features` text,
  `requirements` text,
  `noimages` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `width` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `height` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `price` decimal(10,2) UNSIGNED DEFAULT NULL,
  `sale_price` decimal(10,2) UNSIGNED DEFAULT NULL,
  `related` varchar(150) DEFAULT NULL,
  `digital` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `digitalloc` varchar(255) DEFAULT NULL,
  `weight` decimal(10,3) NOT NULL DEFAULT '0.000',
  `tax_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `num_reviews` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `review_rating` decimal(3,2) UNSIGNED DEFAULT NULL,
  `featured` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `homepage` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `custom_url` varchar(100) DEFAULT NULL,
  `in_stock` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `type` varchar(20) DEFAULT NULL,
  `isbn` varchar(30) DEFAULT NULL,
  `mpn` varchar(20) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `sales` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `custom_url` (`custom_url`),
  UNIQUE KEY `code` (`code`),
  KEY `disabled` (`active`),
  KEY `tax_id` (`tax_id`),
  KEY `in_stock` (`in_stock`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_product_category` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `category_id` tinyint(3) UNSIGNED NOT NULL,
  `main_category` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  UNIQUE KEY `unique_product_category` (`product_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_product_images` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `image_id` int(11) UNSIGNED NOT NULL,
  UNIQUE KEY `unique_product_image` (`product_id`,`image_id`),
  KEY `image_id` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_reviews` (
  `review_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `approved` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `product` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `rating` tinyint(1) UNSIGNED NOT NULL DEFAULT '5',
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `review` text NOT NULL,
  `ipaddress` varchar(40) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `spam` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`review_id`),
  KEY `product` (`product`),
  KEY `approved` (`approved`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_serials` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `serial` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `purchase_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_serial` (`serial`,`email`,`product_id`),
  KEY `active` (`active`),
  KEY `product_id` (`product_id`),
  KEY `email` (`email`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `store_serial_usage` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `serial_id` int(11) UNSIGNED NOT NULL,
  `success` tinyint(3) UNSIGNED DEFAULT NULL,
  `usage_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `serial_id` (`serial_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_tax` (
  `tax_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `percent` decimal(10,4) UNSIGNED NOT NULL,
  `details` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`tax_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `store_vouchers` (
  `voucher_id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `code` varchar(20) NOT NULL,
  `percent` varchar(10) DEFAULT NULL,
  `amount` varchar(10) DEFAULT NULL,
  `description` text NOT NULL,
  `expire` datetime NOT NULL,
  `selected_products` text,
  `allowed` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `times_used` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`voucher_id`),
  UNIQUE KEY `code` (`code`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints 
--

ALTER TABLE `store_products` ADD FULLTEXT KEY `name` (`name`,`description`);

ALTER TABLE `users_requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `users_sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `users_delivery_address`
  ADD CONSTRAINT `users_delivery_address_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `store_downloads`
  ADD CONSTRAINT `store_downloads_ibfk_1` FOREIGN KEY (`product`) REFERENCES `store_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `store_downloads_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `store_downloads_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `store_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `store_orders`
  ADD CONSTRAINT `store_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `store_orders_ibfk_2` FOREIGN KEY (`delivery_id`) REFERENCES `users_delivery_address` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `store_orders_ibfk_3` FOREIGN KEY (`voucher`) REFERENCES `store_vouchers` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `store_order_products`
  ADD CONSTRAINT `store_order_products_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `store_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `store_order_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `store_products` (`product_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

ALTER TABLE `store_products`
  ADD CONSTRAINT `store_products_ibfk_1` FOREIGN KEY (`tax_id`) REFERENCES `store_tax` (`tax_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `store_product_category`
  ADD CONSTRAINT `store_product_category_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `store_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `store_product_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `store_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `store_product_images`
  ADD CONSTRAINT `store_product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `store_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `store_product_images_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `store_gallery_images` (`img_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `store_reviews`
  ADD CONSTRAINT `store_reviews_ibfk_1` FOREIGN KEY (`product`) REFERENCES `store_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `store_serials`
  ADD CONSTRAINT `store_serials_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `store_products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `store_serials_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `store_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;
