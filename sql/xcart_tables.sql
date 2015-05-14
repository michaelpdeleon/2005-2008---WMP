







CREATE TABLE xcart_benchmark_pages (
  pageid int(11) NOT NULL auto_increment,
  script varchar(64) NOT NULL default '',
  data varchar(255) NOT NULL default '',
  method char(1) NOT NULL default 'G',
  PRIMARY KEY  (pageid),
  UNIQUE KEY sdm (script,data,method)
) TYPE=MyISAM;





CREATE TABLE xcart_categories (
  categoryid int(11) NOT NULL auto_increment,
  parentid int(11) NOT NULL default '0',
  categoryid_path varchar(255) NOT NULL default '',
  category varchar(255) NOT NULL default '',
  description text NOT NULL,
  meta_descr varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  views_stats int(11) NOT NULL default '0',
  order_by int(11) NOT NULL default '0',
  threshold_bestsellers int(11) NOT NULL default '1',
  product_count int(11) NOT NULL default '0',
  meta_keywords varchar(255) NOT NULL default '',
  PRIMARY KEY  (categoryid),
  KEY category_path (parentid,categoryid_path),
  KEY category_path2 (categoryid,categoryid_path),
  KEY avail (avail),
  KEY order_by (order_by,category),
  KEY am (avail),
  KEY ia (categoryid,avail),
  KEY pa (categoryid_path,avail)
) TYPE=MyISAM;





CREATE TABLE xcart_categories_lng (
  code char(2) NOT NULL default '',
  categoryid int(11) NOT NULL default '0',
  category varchar(255) NOT NULL default '',
  description text NOT NULL,
  PRIMARY KEY  (code,categoryid)
) TYPE=MyISAM;





CREATE TABLE xcart_categories_subcount (
  categoryid int(11) NOT NULL default '0',
  subcategory_count int(11) NOT NULL default '0',
  product_count int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (categoryid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_category_memberships (
  categoryid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (categoryid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_cc_gestpay_data (
  value char(32) NOT NULL default '',
  type char(1) NOT NULL default 'C',
  PRIMARY KEY  (value,type)
) TYPE=MyISAM;





CREATE TABLE xcart_cc_pp3_data (
  ref varchar(255) NOT NULL default '',
  sessionid varchar(255) NOT NULL default '',
  param1 varchar(255) NOT NULL default '',
  param2 varchar(255) NOT NULL default '',
  param3 varchar(255) NOT NULL default '',
  param4 varchar(255) NOT NULL default '',
  param5 varchar(255) NOT NULL default '',
  trstat varchar(255) NOT NULL default '',
  is_callback char(1) NOT NULL default '',
  UNIQUE KEY refk (ref)
) TYPE=MyISAM;





CREATE TABLE xcart_ccprocessors (
  module_name varchar(255) NOT NULL default '',
  type char(1) NOT NULL default '',
  processor varchar(255) NOT NULL default '',
  template varchar(255) NOT NULL default '',
  param01 varchar(255) NOT NULL default '',
  param02 varchar(255) NOT NULL default '',
  param03 varchar(255) NOT NULL default '',
  param04 varchar(255) NOT NULL default '',
  param05 varchar(255) NOT NULL default '',
  param06 varchar(255) NOT NULL default '',
  param07 varchar(255) NOT NULL default '',
  param08 varchar(255) NOT NULL default '',
  param09 varchar(255) NOT NULL default '',
  disable_ccinfo char(1) NOT NULL default 'N',
  background char(1) NOT NULL default 'N',
  testmode char(1) NOT NULL default 'N',
  is_check char(1) NOT NULL default '',
  is_refund char(1) NOT NULL default '',
  c_template varchar(255) NOT NULL default '',
  paymentid int(11) NOT NULL default '0',
  cmpi char(1) NOT NULL default '',
  PRIMARY KEY  (module_name)
) TYPE=MyISAM;





CREATE TABLE xcart_class_lng (
  code char(2) NOT NULL default 'US',
  classid int(11) NOT NULL default '0',
  class varchar(128) NOT NULL default '',
  classtext varchar(255) NOT NULL default '',
  PRIMARY KEY  (classid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_class_options (
  optionid int(11) NOT NULL auto_increment,
  classid int(11) NOT NULL default '0',
  option_name varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  avail char(1) NOT NULL default 'Y',
  price_modifier decimal(12,2) NOT NULL default '0.00',
  modifier_type char(1) NOT NULL default '$',
  PRIMARY KEY  (optionid),
  KEY orderby (orderby,avail),
  KEY ia (classid,avail)
) TYPE=MyISAM;





CREATE TABLE xcart_classes (
  classid int(11) NOT NULL auto_increment,
  productid int(11) NOT NULL default '0',
  class varchar(128) NOT NULL default '',
  classtext varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  avail char(1) NOT NULL default 'Y',
  is_modifier char(1) NOT NULL default 'Y',
  PRIMARY KEY  (classid),
  KEY orderby (orderby,avail),
  KEY productid (productid),
  KEY is_modifier (is_modifier),
  KEY class (class)
) TYPE=MyISAM;





CREATE TABLE xcart_config (
  name varchar(32) NOT NULL default '',
  comment varchar(255) NOT NULL default '',
  value text NOT NULL,
  category varchar(32) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  type enum('numeric','text','textarea','checkbox','separator','selector','multiselector') default 'text',
  defvalue text NOT NULL,
  variants text NOT NULL,
  PRIMARY KEY  (name),
  KEY orderby (orderby)
) TYPE=MyISAM;





CREATE TABLE xcart_contact_fields (
  fieldid int(11) NOT NULL auto_increment,
  field varchar(255) NOT NULL default '',
  type char(1) NOT NULL default 'T',
  variants text NOT NULL,
  def varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  avail varchar(4) NOT NULL default '',
  required varchar(4) NOT NULL default '',
  PRIMARY KEY  (fieldid),
  KEY avail (avail),
  KEY required (required)
) TYPE=MyISAM;





CREATE TABLE xcart_counters (
  type char(1) NOT NULL default '',
  value int(11) NOT NULL auto_increment,
  PRIMARY KEY  (type,value)
) TYPE=MyISAM;





CREATE TABLE xcart_counties (
  countyid int(11) NOT NULL auto_increment,
  stateid int(11) NOT NULL default '0',
  county varchar(255) NOT NULL default '',
  PRIMARY KEY  (countyid),
  UNIQUE KEY countyname (stateid,county),
  KEY countyid (stateid,countyid)
) TYPE=MyISAM;





CREATE TABLE xcart_countries (
  code char(2) NOT NULL default '',
  code_A3 char(3) NOT NULL default '',
  code_N3 int(4) NOT NULL default '0',
  region char(2) NOT NULL default '',
  charset varchar(32) NOT NULL default 'iso-8859-1',
  active char(1) NOT NULL default 'Y',
  fedex_zone char(1) NOT NULL default '',
  display_states char(1) NOT NULL default 'Y',
  PRIMARY KEY  (code),
  KEY fedex_zone (fedex_zone)
) TYPE=MyISAM;





CREATE TABLE xcart_country_currencies (
  code char(3) NOT NULL default '',
  country_code char(2) NOT NULL default '',
  PRIMARY KEY  (code,country_code)
) TYPE=MyISAM;





CREATE TABLE xcart_currencies (
  code char(3) NOT NULL default '',
  code_int int(3) NOT NULL default '0',
  name varchar(128) NOT NULL default '',
  symbol varchar(16) NOT NULL default '',
  UNIQUE KEY code (code),
  KEY code_int (code_int)
) TYPE=MyISAM;





CREATE TABLE xcart_customers (
  login varchar(32) NOT NULL default '',
  usertype char(1) NOT NULL default '',
  password varchar(255) NOT NULL default '',
  password_hint varchar(128) NOT NULL default '',
  password_hint_answer varchar(128) NOT NULL default '',
  b_title varchar(32) NOT NULL default '',
  b_firstname varchar(128) NOT NULL default '',
  b_lastname varchar(128) NOT NULL default '',
  b_address varchar(64) NOT NULL default '',
  b_city varchar(64) NOT NULL default '',
  b_county varchar(32) NOT NULL default '',
  b_state varchar(32) NOT NULL default '',
  b_country char(2) NOT NULL default '',
  b_zipcode varchar(32) NOT NULL default '',
  title varchar(32) NOT NULL default '',
  firstname varchar(128) NOT NULL default '',
  lastname varchar(128) NOT NULL default '',
  company varchar(255) NOT NULL default '',
  s_title varchar(32) NOT NULL default '',
  s_firstname varchar(128) NOT NULL default '',
  s_lastname varchar(128) NOT NULL default '',
  s_address varchar(255) NOT NULL default '',
  s_city varchar(255) NOT NULL default '',
  s_county varchar(32) NOT NULL default '',
  s_state varchar(32) NOT NULL default '',
  s_country char(2) NOT NULL default '',
  s_zipcode varchar(32) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  phone varchar(32) NOT NULL default '',
  fax varchar(32) NOT NULL default '',
  url varchar(128) NOT NULL default '',
  card_name varchar(255) NOT NULL default '',
  card_type varchar(16) NOT NULL default '',
  card_number varchar(128) NOT NULL default '',
  card_expire varchar(4) NOT NULL default '',
  card_cvv2 varchar(64) NOT NULL default '',
  last_login int(11) NOT NULL default '0',
  first_login int(11) NOT NULL default '0',
  status char(1) NOT NULL default 'Y',
  referer varchar(255) NOT NULL default '',
  ssn varchar(32) NOT NULL default '',
  language char(2) NOT NULL default 'US',
  cart mediumtext NOT NULL,
  change_password char(1) NOT NULL default 'N',
  parent varchar(32) NOT NULL default '',
  pending_plan_id int(11) NOT NULL default '0',
  activity char(1) NOT NULL default 'Y',
  membershipid int(11) NOT NULL default '0',
  pending_membershipid int(11) NOT NULL default '0',
  tax_number varchar(50) NOT NULL default '',
  tax_exempt char(1) NOT NULL default 'N',
  PRIMARY KEY  (login),
  KEY usertype (usertype),
  KEY last_login (last_login),
  KEY first_login (first_login),
  KEY status (status),
  KEY membershipid (membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_delivery (
  shippingid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  PRIMARY KEY  (shippingid,productid),
  KEY productid_index (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_discount_coupons (
  coupon char(16) NOT NULL default '',
  discount decimal(12,2) NOT NULL default '0.00',
  coupon_type char(12) NOT NULL default '',
  productid int(11) NOT NULL default '0',
  categoryid int(11) NOT NULL default '0',
  minimum decimal(12,2) NOT NULL default '0.00',
  times int(11) NOT NULL default '0',
  per_user char(1) NOT NULL default 'N',
  times_used int(11) NOT NULL default '0',
  expire int(11) NOT NULL default '0',
  status char(1) NOT NULL default '',
  provider char(32) NOT NULL default '',
  recursive char(1) NOT NULL default 'N',
  apply_category_once char(1) NOT NULL default 'N',
  apply_product_once char(1) NOT NULL default 'N',
  PRIMARY KEY  (coupon),
  KEY provider (provider),
  KEY status (status)
) TYPE=MyISAM;





CREATE TABLE xcart_discount_coupons_login (
  coupon varchar(16) NOT NULL default '',
  login varchar(32) NOT NULL default '',
  times_used int(11) NOT NULL default '0',
  PRIMARY KEY  (coupon,login)
) TYPE=MyISAM;





CREATE TABLE xcart_discount_memberships (
  discountid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (discountid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_discounts (
  discountid int(11) NOT NULL auto_increment,
  minprice decimal(12,2) NOT NULL default '0.00',
  discount decimal(12,2) NOT NULL default '0.00',
  discount_type char(32) NOT NULL default 'absolute',
  provider char(32) NOT NULL default '',
  PRIMARY KEY  (discountid),
  KEY provider (provider),
  KEY minprice (minprice)
) TYPE=MyISAM;





CREATE TABLE xcart_download_keys (
  download_key char(100) NOT NULL default '',
  expires int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  itemid int(11) NOT NULL default '0',
  PRIMARY KEY  (download_key),
  UNIQUE KEY itemid (itemid),
  KEY productid (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_export_ranges (
  sec varchar(64) NOT NULL default '',
  id varchar(64) NOT NULL default '',
  PRIMARY KEY  (sec,id)
) TYPE=MyISAM;





CREATE TABLE xcart_extra_field_values (
  productid int(11) NOT NULL default '0',
  fieldid int(11) NOT NULL default '0',
  value char(255) NOT NULL default '',
  PRIMARY KEY  (productid,fieldid),
  FULLTEXT KEY value (value)
) TYPE=MyISAM;





CREATE TABLE xcart_extra_fields (
  fieldid int(11) NOT NULL auto_increment,
  provider char(32) NOT NULL default '',
  field char(255) NOT NULL default '',
  value char(255) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  service_name char(32) NOT NULL default '',
  PRIMARY KEY  (fieldid),
  KEY provider (provider),
  KEY active (active)
) TYPE=MyISAM;





CREATE TABLE xcart_extra_fields_lng (
  fieldid int(11) NOT NULL default '0',
  code char(2) NOT NULL default 'US',
  field char(255) NOT NULL default '',
  UNIQUE KEY fc (fieldid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_featured_products (
  productid int(11) NOT NULL default '0',
  categoryid int(11) NOT NULL default '0',
  product_order int(11) NOT NULL default '0',
  avail char(1) NOT NULL default 'Y',
  PRIMARY KEY  (productid,categoryid),
  KEY product_order (product_order),
  KEY avail (avail),
  KEY pacpo (productid,avail,categoryid,product_order)
) TYPE=MyISAM;





CREATE TABLE xcart_fedex_rates (
  r_id int(11) NOT NULL auto_increment,
  r_zone varchar(6) NOT NULL default '',
  r_weight varchar(255) NOT NULL default '0',
  r_meth_id int(11) NOT NULL default '0',
  r_rate decimal(12,2) NOT NULL default '0.00',
  r_ishundreds int(1) NOT NULL default '0',
  r_container int(1) NOT NULL default '0',
  PRIMARY KEY  (r_id),
  KEY r_zone (r_zone),
  KEY r_meth_id (r_meth_id),
  KEY r_rate (r_rate)
) TYPE=MyISAM;





CREATE TABLE xcart_fedex_zips (
  zip_id int(11) NOT NULL auto_increment,
  zip_first varchar(5) NOT NULL default '000',
  zip_last varchar(5) NOT NULL default '',
  zip_zone varchar(6) NOT NULL default '',
  zip_meth int(11) NOT NULL default '0',
  PRIMARY KEY  (zip_id),
  KEY zip_first (zip_first),
  KEY zip_last (zip_last),
  KEY zip_zone (zip_zone)
) TYPE=MyISAM;





CREATE TABLE xcart_ge_products (
  sessid varchar(40) NOT NULL default '',
  geid varchar(32) NOT NULL default '',
  productid int(11) NOT NULL default '0',
  UNIQUE KEY sgp (sessid,geid,productid),
  KEY geid (geid)
) TYPE=MyISAM;





CREATE TABLE xcart_giftcerts (
  gcid varchar(16) NOT NULL default '',
  orderid int(11) NOT NULL default '0',
  purchaser varchar(64) NOT NULL default '',
  recipient varchar(64) NOT NULL default '',
  send_via char(1) NOT NULL default 'E',
  recipient_email varchar(64) NOT NULL default '',
  recipient_firstname varchar(128) NOT NULL default '',
  recipient_lastname varchar(128) NOT NULL default '',
  recipient_address varchar(64) NOT NULL default '',
  recipient_city varchar(64) NOT NULL default '',
  recipient_state varchar(32) NOT NULL default '',
  recipient_zipcode varchar(32) NOT NULL default '',
  recipient_country char(2) NOT NULL default '',
  recipient_phone varchar(32) NOT NULL default '',
  message text NOT NULL,
  amount decimal(12,2) NOT NULL default '0.00',
  debit decimal(12,2) NOT NULL default '0.00',
  status char(1) NOT NULL default 'P',
  add_date int(11) NOT NULL default '0',
  block_date int(11) NOT NULL default '0',
  tpl_file varchar(255) NOT NULL default 'template_default.tpl',
  recipient_county varchar(32) NOT NULL default '',
  PRIMARY KEY  (gcid),
  KEY orderid (orderid),
  KEY status (status),
  KEY add_date (add_date)
) TYPE=MyISAM;





CREATE TABLE xcart_images_C (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_D (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  KEY image_path (image_path),
  KEY id (id)
) TYPE=MyISAM;





CREATE TABLE xcart_images_M (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_P (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_T (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_images_W (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  PRIMARY KEY  (imageid),
  UNIQUE KEY id (id),
  KEY image_path (image_path)
) TYPE=MyISAM;





CREATE TABLE xcart_import_cache (
  data_type char(3) NOT NULL default '',
  id varchar(255) NOT NULL default '',
  value varchar(255) NOT NULL default '',
  login varchar(32) NOT NULL default '',
  PRIMARY KEY  (data_type,id,login)
) TYPE=MyISAM;





CREATE TABLE xcart_languages (
  code char(2) NOT NULL default '',
  name varchar(128) NOT NULL default '',
  value text NOT NULL,
  topic varchar(24) NOT NULL default '',
  PRIMARY KEY  (code,name),
  KEY topic (topic)
) TYPE=MyISAM;





CREATE TABLE xcart_languages_alt (
  code char(2) NOT NULL default '',
  name varchar(128) NOT NULL default '',
  value text NOT NULL,
  PRIMARY KEY  (code,name)
) TYPE=MyISAM;





CREATE TABLE xcart_login_history (
  login varchar(32) NOT NULL default '',
  date_time int(11) NOT NULL default '0',
  usertype char(1) NOT NULL default '',
  action varchar(32) NOT NULL default '',
  status varchar(32) NOT NULL default '',
  ip varchar(32) NOT NULL default '',
  PRIMARY KEY  (login,date_time)
) TYPE=MyISAM;





CREATE TABLE xcart_manufacturers (
  manufacturerid int(11) NOT NULL auto_increment,
  manufacturer varchar(255) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  descr text NOT NULL,
  orderby int(11) NOT NULL default '0',
  provider varchar(32) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  PRIMARY KEY  (manufacturerid),
  KEY manufacturer (manufacturer),
  KEY orderby (orderby),
  KEY provider (provider),
  KEY avail (avail)
) TYPE=MyISAM;





CREATE TABLE xcart_manufacturers_lng (
  manufacturerid int(11) NOT NULL default '0',
  code char(2) NOT NULL default 'US',
  manufacturer varchar(255) NOT NULL default '',
  descr text NOT NULL,
  UNIQUE KEY mc (manufacturerid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_memberships (
  membershipid int(11) NOT NULL auto_increment,
  area char(1) NOT NULL default 'C',
  membership varchar(255) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  flag char(2) NOT NULL default '',
  PRIMARY KEY  (membershipid),
  KEY area (area),
  KEY orderby (orderby),
  KEY active (active)
) TYPE=MyISAM;





CREATE TABLE xcart_memberships_lng (
  membershipid int(11) NOT NULL default '0',
  code char(2) NOT NULL default 'US',
  membership varchar(255) NOT NULL default '',
  UNIQUE KEY mc (membershipid,code)
) TYPE=MyISAM;





CREATE TABLE xcart_modules (
  moduleid int(11) NOT NULL auto_increment,
  module_name varchar(255) NOT NULL default '',
  module_descr varchar(255) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  PRIMARY KEY  (moduleid),
  KEY module_name (module_name),
  KEY active (active)
) TYPE=MyISAM;





CREATE TABLE xcart_newsletter (
  newsid int(11) NOT NULL auto_increment,
  subject varchar(128) NOT NULL default '',
  body text NOT NULL,
  send_date int(11) NOT NULL default '0',
  email1 varchar(128) NOT NULL default '',
  email2 varchar(128) NOT NULL default '',
  email3 varchar(128) NOT NULL default '',
  status char(1) NOT NULL default 'N',
  listid int(11) NOT NULL default '0',
  show_as_news char(1) NOT NULL default 'N',
  allow_html char(1) NOT NULL default 'N',
  PRIMARY KEY  (newsid),
  KEY status (status),
  KEY send_date (send_date)
) TYPE=MyISAM;





CREATE TABLE xcart_newslist_subscription (
  listid int(11) NOT NULL default '0',
  email char(128) NOT NULL default '',
  since_date int(11) NOT NULL default '0',
  PRIMARY KEY  (listid,email)
) TYPE=MyISAM;





CREATE TABLE xcart_newslists (
  listid int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  descr text NOT NULL,
  show_as_news char(1) NOT NULL default 'N',
  avail char(1) NOT NULL default 'N',
  subscribe char(1) NOT NULL default 'N',
  lngcode char(2) NOT NULL default 'US',
  PRIMARY KEY  (listid)
) TYPE=MyISAM;





CREATE TABLE xcart_old_passwords (
  login varchar(32) NOT NULL default '',
  password varchar(32) NOT NULL default '',
  PRIMARY KEY  (login,password)
) TYPE=MyISAM;





CREATE TABLE xcart_order_details (
  orderid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  price decimal(12,2) NOT NULL default '0.00',
  amount int(11) NOT NULL default '0',
  provider varchar(32) NOT NULL default '',
  product_options text NOT NULL,
  extra_data text NOT NULL,
  itemid int(11) NOT NULL auto_increment,
  productcode varchar(32) NOT NULL default '',
  product varchar(255) NOT NULL default '',
  PRIMARY KEY  (itemid),
  KEY orderid (orderid),
  KEY productid (productid),
  KEY provider (provider),
  KEY productcode (productcode)
) TYPE=MyISAM;





CREATE TABLE xcart_order_extras (
  orderid int(11) NOT NULL default '0',
  khash varchar(64) NOT NULL default '',
  value text NOT NULL,
  PRIMARY KEY  (orderid,khash)
) TYPE=MyISAM;





CREATE TABLE xcart_orders (
  orderid int(11) NOT NULL auto_increment,
  login varchar(32) NOT NULL default '',
  membership varchar(255) NOT NULL default '',
  total decimal(12,2) NOT NULL default '0.00',
  giftcert_discount decimal(12,2) NOT NULL default '0.00',
  giftcert_ids text NOT NULL,
  subtotal decimal(12,2) NOT NULL default '0.00',
  discount decimal(12,2) NOT NULL default '0.00',
  coupon varchar(32) NOT NULL default '',
  coupon_discount decimal(12,2) NOT NULL default '0.00',
  shippingid int(11) NOT NULL default '0',
  tracking varchar(64) NOT NULL default '',
  shipping_cost decimal(12,2) NOT NULL default '0.00',
  tax decimal(12,2) NOT NULL default '0.00',
  taxes_applied text NOT NULL,
  date int(11) NOT NULL default '0',
  status char(1) NOT NULL default 'Q',
  payment_method varchar(64) NOT NULL default '',
  flag char(1) NOT NULL default 'N',
  notes text NOT NULL,
  details text NOT NULL,
  customer_notes text NOT NULL,
  customer varchar(32) NOT NULL default '',
  title varchar(32) NOT NULL default '',
  firstname varchar(32) NOT NULL default '',
  lastname varchar(32) NOT NULL default '',
  company varchar(255) NOT NULL default '',
  b_title varchar(32) NOT NULL default '',
  b_firstname varchar(128) NOT NULL default '',
  b_lastname varchar(128) NOT NULL default '',
  b_address varchar(64) NOT NULL default '',
  b_city varchar(64) NOT NULL default '',
  b_county varchar(32) NOT NULL default '',
  b_state varchar(32) NOT NULL default '',
  b_country char(2) NOT NULL default '',
  b_zipcode varchar(32) NOT NULL default '',
  s_title varchar(32) NOT NULL default '',
  s_firstname varchar(128) NOT NULL default '',
  s_lastname varchar(128) NOT NULL default '',
  s_address varchar(64) NOT NULL default '',
  s_city varchar(64) NOT NULL default '',
  s_county varchar(32) NOT NULL default '',
  s_state varchar(32) NOT NULL default '',
  s_country char(2) NOT NULL default '',
  s_zipcode varchar(32) NOT NULL default '',
  phone varchar(32) NOT NULL default '',
  fax varchar(32) NOT NULL default '',
  url varchar(32) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  language char(2) NOT NULL default 'US',
  clickid int(11) NOT NULL default '0',
  extra text NOT NULL,
  membershipid int(11) NOT NULL default '0',
  paymentid int(11) NOT NULL default '0',
  payment_surcharge decimal(12,2) NOT NULL default '0.00',
  tax_number varchar(50) NOT NULL default '',
  tax_exempt char(1) NOT NULL default 'N',
  PRIMARY KEY  (orderid),
  KEY order_date (date),
  KEY s_state (s_state),
  KEY b_state (b_state),
  KEY s_country (s_country),
  KEY b_country (b_country),
  KEY login (login)
) TYPE=MyISAM;





CREATE TABLE xcart_pages (
  pageid int(11) NOT NULL auto_increment,
  filename varchar(255) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  level char(1) NOT NULL default 'E',
  orderby int(11) NOT NULL default '0',
  active char(1) NOT NULL default 'Y',
  language char(2) NOT NULL default '',
  PRIMARY KEY  (pageid),
  KEY orderby (level,orderby,title)
) TYPE=MyISAM;





CREATE TABLE xcart_payment_methods (
  paymentid int(11) NOT NULL auto_increment,
  payment_method varchar(128) NOT NULL default '',
  payment_details varchar(255) NOT NULL default '',
  payment_template varchar(128) NOT NULL default '',
  payment_script varchar(128) NOT NULL default '',
  protocol varchar(6) NOT NULL default 'http',
  orderby int(11) NOT NULL default '0',
  active char(1) NOT NULL default 'Y',
  is_cod char(1) NOT NULL default '',
  af_check char(1) NOT NULL default 'Y',
  processor_file varchar(255) NOT NULL default '',
  surcharge decimal(12,2) NOT NULL default '0.00',
  surcharge_type char(1) NOT NULL default '$',
  PRIMARY KEY  (paymentid),
  KEY orderby (orderby),
  KEY protocol (protocol)
) TYPE=MyISAM;





CREATE TABLE xcart_pmethod_memberships (
  paymentid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (paymentid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_pricing (
  priceid int(11) NOT NULL auto_increment,
  productid int(11) NOT NULL default '0',
  quantity int(11) NOT NULL default '0',
  price decimal(12,2) NOT NULL default '0.00',
  variantid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (priceid),
  KEY productid (productid),
  KEY variantid (variantid),
  KEY pvq (productid,variantid,quantity),
  KEY pvqm (productid,variantid,quantity,membershipid),
  KEY pv (productid,variantid),
  KEY vq (variantid,quantity),
  KEY vqm (variantid,quantity,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_bookmarks (
  productid int(11) NOT NULL default '0',
  add_date int(11) NOT NULL default '0',
  login varchar(32) NOT NULL default '',
  UNIQUE KEY productid (productid,login)
) TYPE=MyISAM;





CREATE TABLE xcart_product_links (
  productid1 int(11) NOT NULL default '0',
  productid2 int(11) NOT NULL default '0',
  orderby int(11) NOT NULL default '0',
  KEY productid2 (productid2),
  KEY productid1 (productid1),
  KEY orderby (orderby)
) TYPE=MyISAM;





CREATE TABLE xcart_product_memberships (
  productid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_options_ex (
  optionid int(11) NOT NULL default '0',
  exceptionid int(11) NOT NULL default '0',
  PRIMARY KEY  (optionid,exceptionid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_options_js (
  productid int(11) NOT NULL default '0',
  javascript_code text,
  PRIMARY KEY  (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_options_lng (
  code char(2) NOT NULL default 'US',
  optionid int(11) NOT NULL default '0',
  option_name varchar(255) NOT NULL default '',
  PRIMARY KEY  (code,optionid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_reviews (
  review_id int(11) NOT NULL auto_increment,
  remote_ip varchar(15) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  message text NOT NULL,
  productid int(11) NOT NULL default '0',
  PRIMARY KEY  (review_id),
  KEY productid (productid),
  KEY remote_ip (remote_ip)
) TYPE=MyISAM;





CREATE TABLE xcart_product_taxes (
  productid int(11) NOT NULL default '0',
  taxid int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,taxid)
) TYPE=MyISAM;





CREATE TABLE xcart_product_votes (
  vote_id int(11) NOT NULL auto_increment,
  remote_ip varchar(15) NOT NULL default '',
  vote_value int(1) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  PRIMARY KEY  (vote_id),
  KEY remote_ip (remote_ip),
  KEY productid (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_products (
  productid int(11) NOT NULL auto_increment,
  productcode varchar(32) NOT NULL default '',
  product varchar(255) NOT NULL default '',
  provider varchar(32) NOT NULL default '',
  distribution varchar(255) NOT NULL default '',
  weight decimal(12,2) NOT NULL default '0.00',
  list_price decimal(12,2) NOT NULL default '0.00',
  descr text NOT NULL,
  fulldescr text NOT NULL,
  avail int(11) NOT NULL default '0',
  rating int(11) NOT NULL default '0',
  forsale char(1) NOT NULL default 'Y',
  add_date int(11) NOT NULL default '0',
  views_stats int(11) NOT NULL default '0',
  sales_stats int(11) NOT NULL default '0',
  del_stats int(11) NOT NULL default '0',
  shipping_freight decimal(12,2) NOT NULL default '0.00',
  free_shipping char(1) NOT NULL default 'N',
  discount_avail char(1) NOT NULL default 'Y',
  min_amount int(11) NOT NULL default '1',
  dim_x int(11) NOT NULL default '0',
  dim_y int(11) NOT NULL default '0',
  dim_z int(11) NOT NULL default '0',
  low_avail_limit int(11) NOT NULL default '10',
  free_tax char(1) NOT NULL default 'N',
  product_type char(1) NOT NULL default 'N',
  manufacturerid int(11) NOT NULL default '0',
  return_time int(11) NOT NULL default '0',
  keywords varchar(255) NOT NULL default '',
  PRIMARY KEY  (productid),
  UNIQUE KEY productcode (productcode,provider),
  KEY product (product),
  KEY rating (rating),
  KEY add_date (add_date),
  KEY provider (provider),
  KEY avail (avail),
  KEY best_sellers (sales_stats,views_stats),
  KEY categories (forsale),
  KEY fi (forsale,productid),
  KEY fia (forsale,productid,avail)
) TYPE=MyISAM;





CREATE TABLE xcart_products_categories (
  categoryid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  main char(1) NOT NULL default 'N',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (categoryid,productid),
  KEY productid (productid),
  KEY main (main),
  KEY orderby (categoryid,orderby),
  KEY pm (productid,main),
  KEY cpm (categoryid,productid,main)
) TYPE=MyISAM;





CREATE TABLE xcart_products_lng (
  code char(2) NOT NULL default '',
  productid int(11) NOT NULL default '0',
  product varchar(255) NOT NULL default '',
  descr text NOT NULL,
  fulldescr text NOT NULL,
  keywords varchar(255) NOT NULL default '',
  PRIMARY KEY  (code,productid)
) TYPE=MyISAM;





CREATE TABLE xcart_quick_flags (
  productid int(11) NOT NULL default '0',
  is_variants char(1) NOT NULL default '',
  is_product_options char(1) NOT NULL default '',
  is_taxes char(1) NOT NULL default '',
  image_path_T varchar(255) default NULL,
  PRIMARY KEY  (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_quick_prices (
  productid int(11) NOT NULL default '0',
  priceid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  variantid int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_referers (
  referer char(255) NOT NULL default '',
  visits int(11) NOT NULL default '0',
  last_visited int(11) NOT NULL default '0',
  PRIMARY KEY  (referer)
) TYPE=MyISAM;





CREATE TABLE xcart_register_field_values (
  fieldid int(11) NOT NULL default '0',
  login varchar(32) NOT NULL default '',
  value text NOT NULL,
  PRIMARY KEY  (fieldid,login)
) TYPE=MyISAM;





CREATE TABLE xcart_register_fields (
  fieldid int(11) NOT NULL auto_increment,
  field varchar(255) NOT NULL default '',
  type char(1) NOT NULL default 'T',
  variants text NOT NULL,
  def varchar(255) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  section char(1) NOT NULL default 'A',
  avail varchar(4) NOT NULL default '',
  required varchar(4) NOT NULL default '',
  PRIMARY KEY  (fieldid),
  KEY avail (avail),
  KEY required (required)
) TYPE=MyISAM;





CREATE TABLE xcart_sessions_data (
  sessid varchar(40) NOT NULL default '',
  start int(11) NOT NULL default '0',
  expiry int(11) NOT NULL default '0',
  data mediumtext NOT NULL,
  PRIMARY KEY  (sessid)
) TYPE=MyISAM;





CREATE TABLE xcart_setup_images (
  itype char(1) NOT NULL default '',
  location char(2) NOT NULL default 'DB',
  save_url char(1) NOT NULL default '',
  size_limit int(11) NOT NULL default '0',
  md5_check varchar(32) NOT NULL default '',
  default_image varchar(255) NOT NULL default './default_image.gif',
  UNIQUE KEY itype (itype)
) TYPE=MyISAM;





CREATE TABLE xcart_shipping (
  shippingid int(11) NOT NULL auto_increment,
  shipping varchar(128) NOT NULL default '',
  shipping_time varchar(128) NOT NULL default '',
  destination char(1) NOT NULL default 'I',
  code varchar(32) NOT NULL default '',
  subcode varchar(32) NOT NULL default '',
  orderby int(11) NOT NULL default '0',
  active char(1) NOT NULL default 'Y',
  intershipper_code varchar(32) NOT NULL default '',
  weight_min decimal(12,2) NOT NULL default '0.00',
  weight_limit decimal(12,2) NOT NULL default '0.00',
  service_code int(11) NOT NULL default '0',
  is_cod char(1) NOT NULL default '',
  is_new char(1) NOT NULL default '',
  PRIMARY KEY  (shippingid),
  KEY code (code),
  KEY orderby (orderby)
) TYPE=MyISAM;





CREATE TABLE xcart_shipping_options (
  carrier varchar(32) NOT NULL default '',
  param00 text NOT NULL,
  param01 varchar(128) NOT NULL default '',
  param02 varchar(128) NOT NULL default '',
  param03 varchar(128) NOT NULL default '',
  param04 varchar(128) NOT NULL default '',
  param05 varchar(128) NOT NULL default '',
  param06 varchar(128) NOT NULL default '',
  param07 varchar(128) NOT NULL default '',
  param08 varchar(128) NOT NULL default '',
  param09 varchar(128) NOT NULL default '',
  PRIMARY KEY  (carrier)
) TYPE=MyISAM;





CREATE TABLE xcart_shipping_rates (
  rateid int(11) NOT NULL auto_increment,
  shippingid int(11) NOT NULL default '0',
  zoneid int(11) NOT NULL default '0',
  maxamount int(11) NOT NULL default '1000000',
  minweight decimal(12,2) NOT NULL default '0.00',
  maxweight decimal(12,2) NOT NULL default '1000000.00',
  mintotal decimal(12,2) NOT NULL default '0.00',
  maxtotal decimal(12,2) NOT NULL default '0.00',
  rate decimal(12,2) NOT NULL default '0.00',
  item_rate decimal(12,2) NOT NULL default '0.00',
  weight_rate decimal(12,2) NOT NULL default '0.00',
  rate_p decimal(12,2) NOT NULL default '0.00',
  provider char(32) NOT NULL default '',
  type char(1) NOT NULL default 'D',
  PRIMARY KEY  (rateid),
  KEY provider (provider),
  KEY shippingid (shippingid),
  KEY maxamount (maxamount),
  KEY maxweight (maxweight),
  KEY zoneid (zoneid)
) TYPE=MyISAM;





CREATE TABLE xcart_states (
  stateid int(11) NOT NULL auto_increment,
  state varchar(32) NOT NULL default '',
  code varchar(32) NOT NULL default '',
  country_code char(2) NOT NULL default '',
  PRIMARY KEY  (stateid),
  UNIQUE KEY code (country_code,code),
  KEY state (state)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_adaptive (
  platform varchar(64) NOT NULL default '',
  browser varchar(10) NOT NULL default '',
  version varchar(16) NOT NULL default '',
  java char(1) NOT NULL default 'Y',
  js char(1) NOT NULL default 'Y',
  count int(11) NOT NULL default '0',
  cookie char(1) NOT NULL default '',
  screen_x int(11) NOT NULL default '0',
  screen_y int(11) NOT NULL default '0',
  last_date int(11) NOT NULL default '0',
  PRIMARY KEY  (platform,browser,java,js,version,cookie,screen_x,screen_y)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_cart_funnel (
  transactionid int(11) NOT NULL auto_increment,
  login varchar(32) NOT NULL default '',
  start_page int(11) NOT NULL default '0',
  step1 int(11) NOT NULL default '0',
  step2 int(11) NOT NULL default '0',
  step3 int(11) NOT NULL default '0',
  final_page int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  PRIMARY KEY  (transactionid),
  KEY start_page (start_page),
  KEY step1 (step1),
  KEY step2 (step2),
  KEY step3 (step3),
  KEY final_page (final_page),
  KEY date (date)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_customers_products (
  productid int(11) NOT NULL default '0',
  login varchar(32) NOT NULL default '',
  counter int(11) NOT NULL default '0',
  PRIMARY KEY  (productid,login),
  KEY counter (counter)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_pages (
  pageid int(11) NOT NULL auto_increment,
  page varchar(255) NOT NULL default '',
  PRIMARY KEY  (pageid),
  KEY page (page)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_pages_paths (
  path varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  KEY counter (date),
  KEY path (path)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_pages_views (
  pageid int(255) NOT NULL default '0',
  time_avg int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  KEY pageid (pageid),
  KEY time_avg (time_avg),
  KEY date (date)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_search (
  search varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  KEY search (search),
  KEY date (date)
) TYPE=MyISAM;





CREATE TABLE xcart_stats_shop (
  id int(11) NOT NULL default '0',
  action char(1) NOT NULL default 'V',
  date int(11) NOT NULL default '0',
  KEY id (id),
  KEY date (date),
  KEY action (action)
) TYPE=MyISAM;





CREATE TABLE xcart_stop_list (
  octet1 int(3) NOT NULL default '0',
  octet2 int(3) NOT NULL default '0',
  octet3 int(3) NOT NULL default '0',
  octet4 int(3) NOT NULL default '0',
  ip varchar(15) NOT NULL default '',
  reason char(1) NOT NULL default 'M',
  date int(11) NOT NULL default '0',
  ipid int(11) NOT NULL auto_increment,
  ip_type char(1) NOT NULL default 'B',
  PRIMARY KEY  (ipid),
  UNIQUE KEY octet1 (octet1,octet2,octet3,octet4),
  KEY ip (ip)
) TYPE=MyISAM;





CREATE TABLE xcart_subscription_customers (
  last_payed_date int(11) NOT NULL default '0',
  orderid int(11) NOT NULL default '0',
  productid int(11) NOT NULL default '0',
  login varchar(32) NOT NULL default '',
  last_payed_orderid int(11) NOT NULL default '0',
  subscriptionid int(11) NOT NULL auto_increment,
  subscription_status varchar(50) NOT NULL default 'Active',
  PRIMARY KEY  (subscriptionid),
  KEY last_payed_date (last_payed_date)
) TYPE=MyISAM;





CREATE TABLE xcart_subscriptions (
  productid int(11) NOT NULL default '0',
  pay_period_type varchar(64) NOT NULL default 'Monthly',
  price_period decimal(12,2) NOT NULL default '0.00',
  oneday_price decimal(12,6) NOT NULL default '0.000000',
  days_as_period int(11) NOT NULL default '0',
  pay_dates text,
  PRIMARY KEY  (productid)
) TYPE=MyISAM;





CREATE TABLE xcart_tax_rate_memberships (
  rateid int(11) NOT NULL default '0',
  membershipid int(11) NOT NULL default '0',
  PRIMARY KEY  (rateid,membershipid)
) TYPE=MyISAM;





CREATE TABLE xcart_tax_rates (
  rateid int(11) NOT NULL auto_increment,
  taxid int(11) NOT NULL default '0',
  zoneid int(11) NOT NULL default '0',
  formula varchar(255) NOT NULL default '',
  rate_value decimal(12,3) NOT NULL default '0.000',
  rate_type char(1) NOT NULL default '',
  provider varchar(32) NOT NULL default '',
  PRIMARY KEY  (rateid),
  KEY provider (provider),
  KEY tax_rate (taxid,zoneid)
) TYPE=MyISAM;





CREATE TABLE xcart_taxes (
  taxid int(11) NOT NULL auto_increment,
  tax_name varchar(10) NOT NULL default '',
  formula varchar(255) NOT NULL default '',
  address_type char(1) NOT NULL default 'S',
  active char(1) NOT NULL default 'N',
  price_includes_tax char(1) NOT NULL default 'N',
  display_including_tax char(1) NOT NULL default 'N',
  display_info char(1) NOT NULL default '',
  regnumber varchar(255) NOT NULL default '',
  priority int(11) NOT NULL default '0',
  PRIMARY KEY  (taxid),
  UNIQUE KEY tax_name (tax_name),
  KEY active (active)
) TYPE=MyISAM;





CREATE TABLE xcart_temporary_data (
  id varchar(32) NOT NULL default '',
  data text,
  expire int(11) default NULL,
  PRIMARY KEY  (id),
  KEY expire (expire)
) TYPE=MyISAM;





CREATE TABLE xcart_titles (
  titleid int(11) NOT NULL auto_increment,
  title varchar(64) NOT NULL default '',
  active char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  PRIMARY KEY  (titleid),
  KEY ia (titleid,active),
  KEY orderby (orderby)
) TYPE=MyISAM;





CREATE TABLE xcart_users_online (
  sessid varchar(40) NOT NULL default '',
  usertype char(1) NOT NULL default '',
  is_registered char(1) NOT NULL default '',
  expiry int(11) NOT NULL default '0',
  PRIMARY KEY  (sessid),
  KEY usertype (usertype),
  KEY iu (is_registered,usertype)
) TYPE=MyISAM;





CREATE TABLE xcart_variant_items (
  optionid int(11) NOT NULL default '0',
  variantid int(11) NOT NULL default '0',
  PRIMARY KEY  (optionid,variantid)
) TYPE=MyISAM;





CREATE TABLE xcart_variants (
  variantid int(11) NOT NULL auto_increment,
  productid int(11) NOT NULL default '0',
  avail int(11) NOT NULL default '0',
  weight decimal(12,2) NOT NULL default '0.00',
  productcode varchar(32) NOT NULL default '0',
  def char(1) NOT NULL default '',
  PRIMARY KEY  (variantid),
  UNIQUE KEY productcode (productcode),
  KEY productid (productid),
  KEY avail (avail)
) TYPE=MyISAM;





CREATE TABLE xcart_wishlist (
  wishlistid int(11) NOT NULL auto_increment,
  login varchar(32) NOT NULL default '',
  productid int(11) NOT NULL default '0',
  amount int(11) NOT NULL default '0',
  amount_purchased int(11) NOT NULL default '0',
  options text NOT NULL,
  event_id int(11) NOT NULL default '0',
  object text NOT NULL,
  PRIMARY KEY  (wishlistid),
  KEY login_product (login,productid),
  KEY event (event_id)
) TYPE=MyISAM;





CREATE TABLE xcart_zone_element (
  zoneid int(11) NOT NULL default '0',
  field varchar(36) NOT NULL default '',
  field_type char(1) NOT NULL default '',
  PRIMARY KEY  (zoneid,field,field_type),
  KEY field (field_type,field)
) TYPE=MyISAM;





CREATE TABLE xcart_zones (
  zoneid int(11) NOT NULL auto_increment,
  zone_name varchar(255) NOT NULL default '',
  zone_cache varchar(255) NOT NULL default '',
  provider varchar(32) NOT NULL default '',
  PRIMARY KEY  (zoneid),
  KEY zone_name (provider,zone_name)
) TYPE=MyISAM;

